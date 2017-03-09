<?php

namespace Plume\Core;

use Plume\Core\MysqliTrait;
use Plume\Core\ApplicationTrait;

class Dao{

    use MysqliTrait;
    use ApplicationTrait;

    protected $tableName = null;
    protected $tableId = null;
    protected $classType = 'dao';

    public function __construct($app, $tableName, $tableId = 'id'){
        $this->app = $app;
        $this->tableName = $tableName;
        $this->tableId = $tableId;
    }

    public function getTableName(){
    	return $this->tableName;
    }

    public function getTableId(){
    	return $this->tableId;
    }

    public function connect(){
        return $this->provider('dataBase')->connect();
    }
}