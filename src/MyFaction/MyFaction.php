<?php

namespace MyFaction;

use pocketmine\utils\Config;

use pocketmine\plugin\PluginBase;

class MyFaction extends PluginBase {
	
	const LEADER_LEVEL = 4;
	const OFFICER_LEVEL = 3;
	const CAPITAIN_LEVEL = 2;
	const NORMAL_LEVEL = 1;
	
	public $database;
	public static $instance;
	
	public function onEnable(){
		@mkdir($this->getDataFolder());
		
		if(!is_file($this->getDataFolder() . 'config.yml')){
			$this->saveResource('config.yml');
		}
		$this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML);
		
		switch($this->config->get('type')){
			case "sqlite":
			case "sqlite3":
				$this->database = new Database\SQLiteDatabase($this->getDataFolder(), $this->config);
			break;
			
			case "mysql":
			case "mysqli":
				$this->database = new Database\MySQLDatabase($this->getDataFolder(), $this->config);
			break;
			
			default:
				$this->database = new Database\SQLiteDatabase($this->getDataFolder(), $this->config);
		}
	
		$this->database->db_init();
		
		self::$instance = $this;
	}
	
	public function onDisable(){
		$this->database->close();
	}
	
	public static function getInstance() {
		return self::$instance;
	}
	
	public function getDatabase(){
		return $this->database;
	}
	
}