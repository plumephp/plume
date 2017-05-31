<?php

namespace Plume\Provider;

class ExceptionProvider extends Provider{

	//非开发环境全局处理error(warning)，exception,shutdown
    public function handle(){
    	if($this->plume('plume.env') === 'dev'){
            error_reporting(E_ALL);
    		return;
    	}
        // Report simple running errors
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        set_error_handler(array($this, 'error_function'));
        set_exception_handler(array($this, 'exception_function'));
        register_shutdown_function(array($this, 'shutdown_function'));
    }

    //1=>'ERROR', 2=>'WARNING', 4=>'PARSE', 8=>'NOTICE'
    public function error_function($errno, $errstr, $errfile, $errline, $errcontext){
    	$this->provider('log')->exception('error_function',
    		array('errno' => $errno,'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline, 'errcontext' => $errcontext));
        require $this->plume('plume.root.path').$this->plume('plume.templates.500');
        die();
        // return false;//如果函数返回 FALSE，标准错误处理处理程序将会继续调用。
    }

    public function exception_function($e){
        $config = $this->plume();
        switch ($e->getCode()){
        case 404:
            $this->provider('log')->log('404', $this->plume('plume.request.path.full'), $e->getMessage(), 'ERROR');
            require $config['plume.root.path'].$config['plume.templates.404'];
            break;  
        case 300:
            $this->provider('log')->log($this->plume('plume.request.path.full'), '300', $e->getMessage(), 'ERROR');
            require $config['plume.root.path'].$config['plume.templates.300'];
            break;
        default:
            $this->provider('log')->log($this->plume('plume.request.path.full'), '500', $e->getMessage().' line num:'.$e->getLine(), 'ERROR');
            require $config['plume.root.path'].$config['plume.templates.500'];
        }
        die();
    }

    public function shutdown_function(){
        $error = error_get_last();
        if(!empty($error)){
        	$this->provider('log')->exception('shutdown_function_last_error', $error['message']);
        }
        
    }
}