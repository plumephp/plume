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
        return $this->provider('cache');
    }

    public function getDataBaseProvider(){
        return $this->provider('dataBase');
    }

    public function getExceptionProvider(){
        return $this->provider('exception');
    }

    public function getHttpProvider(){
        return $this->provider('http');
    }

    public function getLogProvider(){
        return $this->provider('log');
    }

    public function getRedisProvider(){
        return $this->provider('redis');
    }

    public function getRenderProvider(){
        return $this->provider('render');   
    }

    public function getRouteProvider(){
        return $this->provider('route');
    }

    public function getSessionProvider(){
        return $this->provider('session');
    }

    public function getAsyncProvider(){
        return $this->provider('async');
    }



}