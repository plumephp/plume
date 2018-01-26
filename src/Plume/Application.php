<?php

/*
* The main implements on plume1.
*
* @author zhangbaitong <https://github.com/zhangbaitong>
*/

namespace Plume;

use Plume\Core\Application as App;

class Application extends App{

    public function __construct($env = 'dev') {
        $this['plume.env'] = $env;
        $this['plume.time.start'] = $_SERVER['REQUEST_TIME'];
        //PHP5.5之前不可以在类对象实例化之前使用魔术变量DIR
        $this['plume.root.path'] = __DIR__.'/../../../../../';
    }

    public function run(){
        //$this->provider('exception')->handle();
        //处理请求
        $this->provider('route')->handleRequest();
        header('Content-Type: text/html; charset=UTF-8');
        $this->provider('cache')->cacheWith(
            function(){
                //处理路由
                $route = $this->provider('route')->handleRoute();
                //渲染Action
                $data = $this->provider('render')->renderAction($route);
                //渲染View
                $this->provider('render')->renderView($route, $data);
            },
            'request');
        $this->provider('log')->logTime();
    }
}