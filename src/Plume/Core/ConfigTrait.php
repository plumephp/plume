<?php

namespace Plume\Core;

trait ConfigTrait{

    protected $config = null;

    public function getConfig(){
        if(is_null($this->config)){
            $this->config = require $this['plume.root.path'].'config/'.$this['plume.env'].'.php';
        }
        return $this->config;
    }
	
}