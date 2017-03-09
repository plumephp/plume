<?php

namespace Plume\Core;

use Plume\Util\Guid;

trait ApplicationTrait{

    protected $app = null;

    protected function log($title, $data){
    	$this->app->provider('log')->info($title, $data);
    	return $this;
    }

    protected function debug($title, $data){
        $this->app->provider('log')->debug($title, $data);
        return $this;
    }

    protected function id(){
    	return Guid::get();
    }

    protected function getConfig(){
        return $this->app->getConfig();
    }

	protected function getConfigValue($configKey, $default=null) {
		if (empty($configKey)) {
			return $default;
		}
		$configArray = $this->app->getConfig();
		return isset($configArray[$configKey])?$configArray[$configKey]:$default;
	}

    protected function plume($key = null, $val = null){
        //return config
        if(is_null($key)){
            return $this->app->getContext();
        }
        //get method
        if(is_null($val)){
            return $this->app[$key];
        }
        //set method
        $this->app[$key] = $val;
    }

    protected function provider($providerName){
        return $this->app->provider($providerName);
    }

    /**
     * @return DataBaseProvider
     */
    public function getDataBaseProvider(){
        return $this->provider('dataBase');
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