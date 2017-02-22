<?php

namespace Plume\Provider;

use Plume\Util\ViewUtils;

class RenderProvider extends Provider{

	/*
        返回结果格式：data:any,[api]:boolean,[error]:boolean,[view]:string
    */
    public function renderAction($route){
        $name = $route['controllerClass'];
        $method = $route['actionName'];

        if($name === $this->plume('plume.root.default')){
            require $this->plume('plume.root.path').$this->plume('plume.templates.300');
            exit;
        }
        if (!class_exists($name)) {
            throw new \Exception($this->plume('plume.msg.error.controller').':'.$name, 404);
        }
        //通过反射实例化类
        $class = new \ReflectionClass($name);
        $instance  = $class->newInstanceArgs(array($this->app));
        if (!$class->hasMethod($method)) {
            throw new \Exception($this->plume('plume.msg.error.action').':'.$method, 404);
        }
        $log = $this->provider('log');
        $method = $class->getmethod($method);
        $beforeDispatch = $class->getMethod($this->plume('plume.method.before'));
        $beforResult = $beforeDispatch->invoke($instance);
        $this->plume('plume.method.before.result', $beforResult);
        $log->debug('ActionbeforResult', $beforResult);
        $result = $method->invoke($instance);
        $log->debug('ActionResult', $result);
        $afterDispatch = $class->getMethod($this->plume('plume.method.after'));
        $afterResult = $afterDispatch->invoke($instance);
        $this->plume('plume.method.after.result', $afterResult);
        $log->debug('ActionafterResult', $afterResult);
        return $result;
    }


    public function renderView($route, $actionResult){
    	$log = $this->provider('log');
    	//API
    	if($actionResult['api']){
            $log->debug('renderView API','Here is API');
            header('Content-Type: application/json; charset=UTF-8');
            echo $actionResult['data'];
            $log->debug('renderView data',$actionResult['data']);
            return;
        }
        //重定向view
        if(!empty($actionResult['view'])){
            $route['viewPath'] = str_ireplace($route['action'].'.phtml', strtolower($actionResult['view'].'.phtml'), $route['viewPath']);
            $log->debug('renderView','View is redirected to '.$route['viewPath']);
        }
        //重定向view_exception
        if(!empty($actionResult['view_exception'])){
            $route['viewPath'] = $this->plume('plume.root.path').'templates/'.$actionResult['view_exception'].'.phtml';
            $log->debug('renderView','View_exception is redirected to '.$route['viewPath']);
        }
        if(!file_exists($route['viewPath'])){
            throw new \Exception($this->plume('plume.msg.error.view').':'.$route['viewPath'], 404);
        }
        //获取页面数据
        $data = $actionResult['data'];
        //初始化view工具类
        $view =  new ViewUtils();
        $plume = array(
            'ROOT_PATH' => $this->plume('plume.root.path'),
            'before' => $this->plume('plume.method.before.result'),
            'after' => $this->plume('plume.method.after.result'),
            'error' => $actionResult['error']);
        $log->debug('renderView','Here is view');
        $log->debug('view data', $data);
        $log->debug('view plume', $plume);
        require $route['viewPath'];
    }
}