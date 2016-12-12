<?php

namespace Plume\Core;

use Plume\Core\MysqliTrait;
use Plume\Core\ApplicationTrait;

class Service{

    use MysqliTrait;
    use ApplicationTrait;

    protected $dao = null;
    protected $classType = 'service';

    public function __construct($app, $dao){
        $this->app = $app;
        $this->dao = $dao;
    }
}