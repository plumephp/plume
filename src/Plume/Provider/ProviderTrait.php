<?php

namespace Plume\Provider;

trait ProviderTrait{
	
    protected $providers = array();

    public function provider($providerName){
        //fix database for db provider
        if(strtolower($providerName) == "database"){
            $providerName = "dataBase";
        }
        if(!isset($this->providers[$providerName])){
            $class = 'Plume\Provider\\'.ucfirst($providerName).'Provider';
            try {
                $this->providers[$providerName] = new $class($this);    
            } catch (Exception $e) {
                throw new \Exception('Provider is not found '.$class);
            }
        }
        return $this->providers[$providerName];
    }
}