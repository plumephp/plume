<?php

/*
* The main implements on plume-async.
*
* @author zhangbaitong <https://github.com/zhangbaitong>
*/

namespace Plume\Async;

use GearmanWorker as Worker;

use Example\TestWorker;

use Plume\Provider\ProviderTrait;
use Plume\Async\ContextTrait;
use Plume\Core\ArrayTrait;
use Plume\Core\ConfigTrait;

class Asynclication implements \ArrayAccess{

	use ProviderTrait;
	use ContextTrait;
	use ArrayTrait;
	use ConfigTrait;

	public $workers = array();
	public $totalNum = 0;
	public $childs = array();
	public $pid = null;
	public $isValidate = true;
	public $stoping = false;

	public $sockets = array();

    public function __construct($env = 'dev') {
        $this['plume.env'] = $env;
        $this['plume.root.path'] = __DIR__.'/../../../../../../';
    }

	public function run(){

		$this->init();

		$this->loadWorkers();

		// $this->daemon();
		$this->daemonSocket();
	}

	public function daemonSocket(){
		$sock = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_bind($sock, '0.0.0.0', 4731);
		socket_listen($sock);
		$conn = @socket_accept($sock);
		while (true){
			if($conn < 0){
				$this->debug('daemon socket', 'socket connect faild');
			}else{
				$recv = socket_read($conn, 4731);
				if(stripos($recv, 'exit') === 0 || stripos($recv, 'exit') > 0){
					socket_close($conn);
					sleep(2);
					$conn = @socket_accept($sock);
				}else{
					$send_data = substr($recv,0,strlen($recv)-2);
					socket_write($conn, $send_data);	
				}
			}
		}
	}

	public function stopWorkers($cmd, $workers){
		$this->stoping = true;
		$this->debug('admin cmd', $cmd);
		switch ($cmd) {
			case 'start':
				$this->loadWorkers($workers);
				break;
			case 'stop':
				$this->unloadWorkers($workers);
				break;
			default:
				break;
		}
		$this->stoping = false;
	}

	public function unloadWorkers($workers = null){
		foreach ($workers as $name => $num) {
			//没有有进程
			if(!in_array($name, $this->childs)){
				continue;
			}
			$pids = array_keys($this->childs, $name);
			foreach ($pids as $pid) {
				posix_kill($pid, SIGTERM);
				unset($this->childs[$pid]);
			}
		}
	}


	public function init(){
		$this->pid = getmypid();
		//ticks使系统运行产生时间片段，以便配合signal获取
		//PHP < 5.3
		if (!function_exists("pcntl_signal_dispatch")) {
    		declare(ticks=1);
		}
		declare(ticks = 1);
		error_reporting(E_ALL | E_STRICT);
		pcntl_signal(SIGCHLD, array($this, 'sighandler'));

		$this->debug('plume-async', '-- init --');
		$config = $this->getConfig();
		$this->workers = isset($config['workers']) ? $config['workers'] : null;
		if(is_null($this->workers) || count($this->workers) === 0){
			throw new \Exception('worker is not found.');
		}else{
			$this->totalNum = count($this->workers);
			$this->debug('workers total number', $this->totalNum);
		}
	}

	public function daemon(){
		while (true) {
			sleep(30);
			$this->validate();
		}
	}

	public function validate(){
		echo 'check';
		if(!$this->isValidate || $this->stoping){
			return;
		}
		$this->isValidate = false;
		//PHP >= 5.3
		if (function_exists("pcntl_signal_dispatch")) {
    		pcntl_signal_dispatch();
		}
		$this->debug('daemon', "-- start check workers --");
		$this->debug('daemon', $this->childs);
		foreach ($this->childs as $pid => $name) {
	        $res = pcntl_waitpid($pid, $status, WNOHANG);
	        if($res == 0){
	        	$this->debug('daemon', "{$name} {$pid} works well");
	        }
	        if ($res == -1 || $res > 0){
	        	$this->debug('daemon', "{$name} {$pid} not found");
	            unset($this->childs[$pid]);
	            //reload
	            $this->fork($name);
	    	}
		}
		$this->debug('daemon', "-- end check --");
		$this->isValidate = true;
	}

	public function loadWorkers($workers = null){
		$workers = is_null($workers) ? $this->workers : $workers;
		foreach ($workers as $name => $num) {
			$this->debug('load worker', "{$name} {$num} times");
			for ($i=0; $i < $num; $i++) { 
				$this->fork($name);				
			}
		}
	}

	public function fork($name){
		//name => Example::TestWorker::reverse
		$nameArr = explode('::', $name);
		if(count($nameArr) !== 3){
			throw new \Exception('Your worker format is wrong.');
		}
		$module = $nameArr[0];
		$class = $nameArr[1];
		$method = $nameArr[2];
		$className = "{$module}\\{$class}";
		$pid = pcntl_fork();
		switch ($pid) {
			case -1://error
				throw new \Exception('fork process for your worker faild!');
				break;
			case 0://sub
				$this->debug('fork::worker','-- startWorker --');
				$this->startWorker($name, $className, $method);
				exit;
				break;
			default://current
				$this->childs[$pid] = $name;
				$this->debug('fork::pid',$pid);
				break;
		}
	}

	public function startWorker($name, $class, $method){
		$worker= new Worker();
		$config = $this->getConfig();
		$server = isset($config['server']) ? $config['server'] : array('127.0.0.1' => 4730);
		foreach ($server as $host => $port) {
			$worker->addServer($host, (int)$port);
		}
		$job = new $class();
		$worker->addFunction($name, array($job, $method));
		while ($worker->work());
	}

	public function log($title, $info){
		$this->provider('log')->log('asynclication', $title, $info);
	}

	public function debug($title, $info){
		$this->provider('log')->debug($title, $info);
	}

	//invoked when sub process is exited
	public function sighandler($sig){
	    switch ($sig) {
	        case SIGCHLD:
	            $this->debug('sig handler',$sig);
	            break;
	    }
	    $this->validate();
	}


}