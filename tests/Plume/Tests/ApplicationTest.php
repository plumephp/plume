<?php

namespace Plume\Tests;

use Plume\Application;
use Plume\Controller\AbstractController;

class ApplicationTest extends \PHPUnit_Framework_TestCase{

    private $app;

	/*
	********** RenderRoute **********
	*/
    public function testRenderRouteDefault(){
    	$route = $this->app->provider('route')->handleRoute();
    	$this->assertEquals('Plume', $route['module']);
    	$this->assertEquals('Index', $route['controller']);
    	$this->assertEquals('index', $route['action']);
        $this->assertEquals('Plume\Controller\IndexController', $route['controllerClass']);
        $this->assertEquals('indexAction', $route['actionName']);
        $this->assertEquals($this->app['plume.root.path'].'modules/Plume/View/index/index.phtml', $route['viewPath']);
    }

    public function testRenderRouteAPI(){
        $route = $this->app->provider('route')->handleRoute('/example/user/list');
        $this->assertEquals('Example', $route['module']);
        $this->assertEquals('User', $route['controller']);
        $this->assertEquals('list', $route['action']);
        $this->assertEquals('Example\Controller\UserController', $route['controllerClass']);
        $this->assertEquals('listAction', $route['actionName']);
        $this->assertEquals($this->app['plume.root.path'].'modules/Example/View/user/list.phtml', $route['viewPath']);
    }

    public function testRenderRouteNoMethod(){
        $route = $this->app->provider('route')->handleRoute('/example/user');
        $this->assertEquals('Example', $route['module']);
        $this->assertEquals('User', $route['controller']);
        $this->assertEquals('index', $route['action']);
        $this->assertEquals('Example\Controller\UserController', $route['controllerClass']);
        $this->assertEquals('indexAction', $route['actionName']);
        $this->assertEquals($this->app['plume.root.path'].'modules/Example/View/user/index.phtml', $route['viewPath']);
    }

   	/*
	********** RenderAction **********
	*/

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Controller is not found
     */
    public function testRenderAction404(){
        $app = new Application();
        $route = $app->provider('route')->handleRoute('/example/index/index');
        $app->provider('render')->renderAction($route);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Controller is not found
     */
    public function testRenderActionPermission1(){
        $app = new Application();
        $route = $app->provider('route')->handleRoute('/example/index/index.php');
        $data = $app->provider('render')->renderAction($route);
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Controller is not found
     */
    public function testRenderActionPermission2(){
        $app = new Application();
        $route = $app->provider('route')->handleRoute('/index.html');
        $data = $app->provider('render')->renderAction($route);
    }

    /**
     * @before
     */
    public function setupPlume(){
    	$this->app = new Application('pro');
    }
}