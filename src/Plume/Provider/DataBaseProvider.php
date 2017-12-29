<?php

namespace Plume\Provider;

use MysqliDb;

class DataBaseProvider extends Provider{

	private $con = null;

	public function connect(){
		if(is_null($this->con)){
			$config = $this->getConfig()['db'];
			$mysqliDb = new MysqliDb(
				$config['host'], $config['username'],
				$config['password'], $config['database'],
				(int)$config['port'], $config['charset']
			);
			$this->con = $mysqliDb->getInstance();
		}
		return $this->con;
	}

	public function close(){
		if(!is_null($this->con)){
			if($this->con instanceof MysqliDb){
				$this->con->disconnect();
			}
			$this->con = null;
		}
	}
}
