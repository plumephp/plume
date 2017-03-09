<?php

namespace Plume\Core;

use Plume\Core\ControllerTrait;
use Plume\Core\ApplicationTrait;

class Controller{

    use ControllerTrait;
    use ApplicationTrait;

    protected $service = null;

    public function __construct($app, $service = null) {
        $this->app = $app;
        $this->service = $service;
    }

    public function beforeDispatch() {
    }

    public function afterDispatch() {
    }

	protected function getParamValue($paramName, $default=null) {
		if (isset($_REQUEST[$paramName])) {
            return is_array($_REQUEST[$paramName]) ? $_REQUEST[$paramName] : trim($_REQUEST[$paramName]);
		}
	    return $default;
	}

	protected function isPost() {
	    $isPost = true;
        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            $isPost = false;
        }
        return $isPost;
    }
}
