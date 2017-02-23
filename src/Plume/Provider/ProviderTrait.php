<?php

namespace Plume\Provider;

trait ProviderTrait{
	
    protected $providers = array();

    public function provider($providerName){
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

    public function getCacheProvider(){
        $this->provider('cache');
    }

    public function getDataBaseProvider(){
        $this->provider('dataBase');
    }

    public function getExceptionProvider(){
        $this->provider('exception');
    }

    public function getHttpProvider(){
        $this->provider('http');
    }

    public function getLogProvider(){
        $this->provider('log');
    }

    public function getRedisProvider(){
        $this->provider('redis');
    }

    public function getRenderProvider(){
        $this->provider('render');   
    }

    public function getRouteProvider(){
        $this->provider('route');
    }

    public function getSessionProvider(){
        $this->provider('session');
    }

    public function getAsyncProvider(){
        $this->provider('async');
    }



}