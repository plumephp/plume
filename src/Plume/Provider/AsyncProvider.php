<?php

namespace Plume\Provider;

use GearmanClient as Client;

class AsyncProvider extends Provider{

	private $client = null;

	public function connect(){
		if(is_null($this->client)){
			$this->client = new GearmanClient();
			$config = $this->getConfig();
			$server = isset($config['gearman']) ? $config['gearman'] : array('127.0.0.1' => 4730);
			foreach ($server as $host => $port) {
				$this->client->addServer($host, (int)$port);
			}
		}
		return $this->client;
	}
}