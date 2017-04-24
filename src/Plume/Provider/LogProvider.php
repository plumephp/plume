<?php

namespace Plume\Provider;

use Plume\Util\ArrayUtils;
use Plume\Util\IPUtils;

class LogProvider extends Provider{

    public function log($fileName, $title, $info = array(), $level = null){
    	$level = is_null($level) ? 'INFO' : strtoupper($level);
        if($level === 'DEBUG' && !$this->plume('plume.log.debug')){
            return;
        }
        //异步日志
        $logConfig = $this->getConfigValue('log');
        if(!empty($logConfig)){
            $env = $this->plume('plume.env');
            $name = ArrayUtils::getValue($logConfig, 'project_name', 'project_default');
            $data = array(
                'env' => $env,
                'project_name' => $name,
                'fileName' => $fileName,
                'title' => $title,
                'info' => $info,
                'level' => $level
            );
            $serverConfig = isset($logConfig['server']) ? $logConfig['server'] : array('127.0.0.1' => 4730);
            $client = $this->provider('async')->connect($serverConfig);
            $client->doBackground('Service::Log::asyncLog', json_encode($data));
            return;
        }
    	$dir = $this->plume('plume.root.path').'var/logs/'.date('Y-m-d') .'/';
    	if(!is_dir($dir)){
    		mkdir($dir, 0777, true);
    	}
    	$file = $dir.$fileName.'.log';
    	$date = date('Y/m/d H:i:s', time());
    	$infoJSON = json_encode($info, JSON_UNESCAPED_UNICODE);
    	$log = "[" . $date . "] - " . $level . " - " . $title . " - " . $infoJSON. "\r\n\r\n";
    	file_put_contents($file, $log, FILE_APPEND);
    }

    public function logTime($startTime = null){
        if(!$this->plume('plume.log.time')){
            return;
        }
        $startTime = is_null($startTime) ? $this->plume('plume.time.start') : $startTime;
        $spendTime = microtime(true) - $startTime;
        //异步性能日志
        $timeConfig = $this->plume('plume.log.time.config');
        if(!empty($timeConfig)){
            $env = $this->plume('plume.env');
            $data = array(
                'ip_remote' => IPUtils::getRemoteIP(),
                'ip_local' => $timeConfig['ip_local'],
                'project' => $timeConfig['project'],
                'url' => $this->plume('plume.request.path'),
                'time_used' => $spendTime,
                'time_create' => '',
                'context' => '',
                'note' => '',
                'env' => $env
            );
            $client = $this->provider('async')->connect($timeConfig['server']);
            $client->doBackground('Service::Log::asyncTime', json_encode($data));
            return;
        }
        $this->log('spendtime', $this->plume('plume.request.path'), $spendTime);
    }

    public function info($title, $info = array()){
        $this->log($this->plume('plume.request.path.full'), $title, $info, 'INFO');
    }

    public function debug($title, $info){
        $this->log('debug', $title, $info, 'DEBUG');
    }

    public function exception($title, $info){
        $this->log('exception', $title, $info, 'ERROR');
    }
}