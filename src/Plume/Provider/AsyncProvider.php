<?php

namespace Plume\Provider;

use GearmanClient as Client;

class AsyncProvider extends Provider{

	private $client = null;

	public function connect($servers = null){
		if(is_null($this->client)){
			$this->client = new Client();
			$config = $this->getConfig();
            if(empty($servers)){
                $server = isset($config['gearman']) ? $config['gearman'] : array('127.0.0.1' => 4730);
            }else{
                $server = $servers;
            }
			foreach ($server as $host => $port) {
				$this->client->addServer($host, (int)$port);
			}
		}
		return $this->client;
	}
}