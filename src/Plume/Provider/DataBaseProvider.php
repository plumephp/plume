<?php

namespace Plume\Provider;

use MysqliDb;

class DataBaseProvider extends Provider{

    private $con = array();

    public function connect($db = 'db'){
        if(!isset($this->con[$db])){
            $config = $this->getConfig()[$db];
            $mysqliDb = new MysqliDb(
                $config['host'], $config['username'],
                $config['password'], $config['database'],
                (int)$config['port'], $config['charset']
            );
            $this->con[$db] = $mysqliDb->getInstance();
        }
        return $this->con[$db];
    }

    public function close($db = 'db'){
        if(isset($this->con[$db])){
            if($this->con[$db] instanceof MysqliDb){
                $this->con[$db]->disconnect();
            }
            $this->con[$db] = null;
            unset($this->con[$db]);
        }
    }
}
