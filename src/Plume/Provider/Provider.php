<?php

namespace Plume\Provider;

use Plume\Core\ApplicationTrait;

class Provider {

	use ApplicationTrait;
	
    public function __construct($app) {
    	$this->app = $app;
    }
}