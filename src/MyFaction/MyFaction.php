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
		
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		
		$this->getCommand("faction")->setExecutor(new Commands\FactionCommand($this));
		self::$instance = $this;
	}
	
	public function onDisable(){
		$this->database->close();
	}
	
	### API ###
	
	/*
	** Call this first, if you want to intercat with API
	** In main class of your plugin:
	** $api = $this->getServer()->getPluginManager()->getPlugin('MyFaction')
	** $api::getInstance();
	*/
	
	public static function getInstance() {
		return self::$instance;
	}

	/*
	** Gets player data
	** Returns false, if player is not in a faction
	** Otherwise, returns array with data:
	** $data['nickname'] => string, player's name
	** $data['factionName'] => string, name of faction
	** $data['exp'] => int, how much expeirence did player brought to faction
	** $data['factionLevel'] => int, faction rank (see at line 11)
	*/
	
	public function getPlayerData(string $nickname){
		$data = $this->database->getPlayerInfo($nickname);
		
		if($data == null) {
			return false;
		}
		
		return $data;
	}

	### INTERNAL ###
	
	private function getDatabase(){
		return $this->database;
	}
	
}