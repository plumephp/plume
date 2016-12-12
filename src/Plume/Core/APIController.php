<?php

namespace Plume\Core;

use Plume\Core\Controller;

class APIController extends Controller{

    protected $service = null;

    public function __construct($app, $service = null) {
        parent::__construct($app, $service);
        $this->api();
    }
   
}