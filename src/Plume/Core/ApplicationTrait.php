<?php

namespace Plume\Core;

use Plume\Util\Guid;

trait ApplicationTrait{

    protected $app = null;
    
    /**
     * @return \Plume\Core\ApplicationTrait
     */
    protected function log($title, $data){
    	$this->app->provider('log')->info($title, $data);
    	return $this;
    }

    /**
     * @return \Plume\Core\ApplicationTrait
     */
    protected function debug($title, $data){
        $this->app->provider('log')->debug($title, $data);
        return $this;
    }

    protected function id($prefix=null){
    	return Guid::get($prefix);
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
     * @return \Plume\Provider\DataBaseProvider
     */
    public function getDataBaseProvider(){
        return $this->app->provider('dataBase');
    }

    /**
     * @return \MysqliDb
     */
    public function getDB(){
        return $this->app->provider('dataBase')->connect();
    }

    /**
     * @return \Plume\Core\ApplicationTrait
     */
    public function closeDB(){
        $this->app->provider('dataBase')->close();
        //$this->app->provider('dataBase')->connect()->__destruct();
        return $this;
    }

    /**
     * @return \Plume\Provider\LogProvider
     */
    public function getLogProvider(){
        return $this->app->provider('log');
    }

    /**
     * @return \Plume\Provider\RedisProvider
     */
    public function getRedisProvider(){
        return $this->app->provider('redis');
    }

    /**
     * @return \redis
     */
    public function getRedis(){
        return $this->app->provider('redis')->connect();
    }

    /**
     * @return \Plume\Core\ApplicationTrait
     */
    public function closeRedis(){
        $this->app->provider('redis')->close();
        return $this;
    }

    /**
     * @return \redis
     */
    public function getSlaveRedis(){
        return $this->app->provider('redis')->connectSlave();
    }

    /**
     * @return \Plume\Core\ApplicationTrait
     */
    public function closeSlaveRedis(){
        $this->app->provider('redis')->closeSlave();
        return $this;
    }

    /**
     * @return \Plume\Provider\SessionProvider
     */
    public function getSessionProvider(){
        return $this->app->provider('session');
    }

    /**
     * @return \Plume\Provider\AsyncProvider
     */
    public function getAsyncProvider(){
        return $this->app->provider('async');
    }

    /**
    * @return \GearmanClient
    */
    public function getGearmanClient(){
        return $this->app->provider('async')->connect();   
    }
}