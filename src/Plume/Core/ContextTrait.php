<?php

namespace Plume\Core;

trait ContextTrait{
	
    protected $context = array(
        'plume.env' => 'dev',
        'plume.log.debug' => false,
        'plume.log.time' => false,
        'plume.root.path' => '',
        'plume.root.default' => 'Plume\Controller\IndexController',
        'plume.cache.db' => false,
        'plume.cache.request' => false,
        'plume.cache.driver' => 'files',
        'plume.cache.expire' => 5,
        'plume.module.default' => 'plume',
        'plume.module.prefix' => '',
        'plume.method.before' => 'beforeDispatch',
        'plume.method.before.result' => 'nothing',
        'plume.method.after' => 'afterDispatch',
        'plume.method.after.result' => 'nothing',
        'plume.msg.error.permission' => 'Permission is not found',
        'plume.msg.error.view' => 'View is not found',
        'plume.msg.error.action' => 'Action is not found',
        'plume.msg.error.controller' => 'Controller is not found',
        'plume.templates.300' => 'templates/index.html',
        'plume.templates.404' => 'templates/404.html',
        'plume.templates.500' => 'templates/500.html',
        'plume.msg.welcome' => 'Welcome to Plume, you can set defualt module for index page',
        //plume系统使用全局参数
        'plume.time.start' => 0,
        'plume.request.path' => '/',
        'plume.request.path.full' => 'default'
    );

    public function getContext(){
        return $this->context;
    }
}