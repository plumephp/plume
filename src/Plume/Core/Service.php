<?php

namespace Plume\Core;

use Plume\Core\MysqliTrait;
use Plume\Core\ApplicationTrait;
use Plume\Provider\ProviderTrait;

class Service{

    use MysqliTrait;
    use ApplicationTrait;
    use ProviderTrait;

    protected $dao = null;
    protected $classType = 'service';

    public function __construct($app, $dao){
        $this->app = $app;
        $this->dao = $dao;
    }
}