<?php

namespace Plume\Provider;

use redis as redisClient;

class RedisProvider extends Provider{

    private $instance = null;

    public function connect(){
        if($this->instance instanceof redisClient){
            return $this->instance;
        }
        $this->instance = $this->instance ?: new RedisClient();
        $config = $this->getConfig();
        $config = isset($config['redis']) ? $config['redis'] : $config;
        $host = isset($config['host']) ? $config['host'] : '127.0.0.1';
        $port = isset($config['port']) ? (int) $config['port'] : '6379';
        $password = isset($config['password']) ? $config['password'] : '';
        $database = isset($config['database']) ? $config['database'] : '0';
        $timeout = isset($config['timeout']) ? $config['timeout'] : '0';
        if (!$this->instance->connect($host, (int) $port, (int) $timeout)) {
                throw new \Exception('redis connection Failed');
        }
        if ($password && !$this->instance->auth($password)) {
                throw new \Exception('redis password is wrong!');
        }
        if ($database) {
            $this->instance->select((int) $database);
        }
        return $this->instance;
    }

    public function close(){
        if($this->instance instanceof redisClient){
            $this->instance->close();
        }
    }
}