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

	/**
	 * @return CacheProvider
	 */
    public function getCacheProvider(){
        return $this->provider('cache');
    }

	/**
	 * @return DataBaseProvider
	 */
    public function getDataBaseProvider(){
        return $this->provider('dataBase');
    }

	/**
	 * @return ExceptionProvider
	 */
    public function getExceptionProvider(){
        return $this->provider('exception');
    }

	/**
	 * @return LogProvider
	 */
    public function getLogProvider(){
        return $this->provider('log');
    }

	/**
	 * @return RedisProvider
	 */
    public function getRedisProvider(){
        return $this->provider('redis');
    }

	/**
	 * @return RenderProvider
	 */
    public function getRenderProvider(){
        return $this->provider('render');   
    }

	/**
	 * @return RouteProvider
	 */
    public function getRouteProvider(){
        return $this->provider('route');
    }

	/**
	 * @return SessionProvider
	 */
    public function getSessionProvider(){
        return $this->provider('session');
    }

	/**
	 * @return AsyncProvider
	 */
    public function getAsyncProvider(){
        return $this->provider('async');
    }



}