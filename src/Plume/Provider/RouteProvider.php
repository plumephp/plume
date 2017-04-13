<?php

namespace Plume\Provider;

use Plume\Util\ArrayUtils;

class RouteProvider extends Provider{

	public function handleRequest(){
        $reqUri = $_SERVER['REQUEST_URI'];
        $index = strpos($reqUri, '?');
        //去除参数部分
        $requestPath = $index > 0 ? substr($reqUri, 0, $index) : $reqUri;
        $this->plume('plume.request.path', $requestPath);
        //不允许直接使用php扩展名的文件请求
        if(stripos($requestPath, '.php') == true ) {
            throw new \Exception($this->plume('plume.msg.error.permission'), 404);
        }
        $this->provider('log')->debug('request', $requestPath);
        return $requestPath;
    }

    /*
        路由定义：module/controller/action
        Controller定义：Plume/Controller/IndexController
        Action定义：Plume/Controller/IndexController/editAction
        视图定义：root/plume/view/index/edit.phtml
    */
    public function handleRoute($reqPath = null){
        $reqPath = is_null($reqPath) ? $this->plume('plume.request.path') : $reqPath;
        //设置默认header，以免在action进行echo
        $reqPathArr = explode('/', $reqPath);
        if(empty($this->plume('plume.module.prefix'))){
            $module = ucfirst(ArrayUtils::getValue($reqPathArr, 1, $this->plume('plume.module.default')));
            $controller = ucfirst(ArrayUtils::getValue($reqPathArr, 2, 'index'));
            $action = ArrayUtils::getValue($reqPathArr, 3, 'index');
        }else{
            //使用模块名前缀配置，使页面访问路径缩短为二级路径
            $module = $this->plume('plume.module.prefix');
            $controller = ucfirst(ArrayUtils::getValue($reqPathArr, 1, 'index'));
            $action = ArrayUtils::getValue($reqPathArr, 2, 'index');
        }
        $controllerClass = $module.'\\Controller\\'.$controller.'Controller';
        $actionName =  $action.'Action';
        $viewPath = $this->plume('plume.root.path').'modules/'.$module.'/View/'.strtolower($controller.'/'.$action.'.phtml');
        $this->plume('plume.request.path.full', $module.'-'.$controller.'-'.$actionName);
        $route =  array(
                'module' => $module,
                'controller' => $controller,
                'action' => $action,
                'controllerClass' => $controllerClass,
                'actionName' => $actionName,
                'viewPath' => $viewPath

        );
        $this->provider('log')->debug('route', $route);
        return $route;
    }
}