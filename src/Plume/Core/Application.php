<?php

namespace Plume\Core;

use Plume\Provider\ProviderTrait;
use Plume\Core\ContextTrait;
use Plume\Core\ArrayTrait;
use Plume\Core\ConfigTrait;

class Application implements \ArrayAccess{

    use ProviderTrait;
    use ArrayTrait;
    use ContextTrait;
    use ConfigTrait;

    //init default config
    public function __construct($env = 'dev') {
    	$this['plume.env'] = $env;
    }

    //run application
    // public function run(){
    // }


}