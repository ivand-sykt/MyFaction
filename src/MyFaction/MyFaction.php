<?php

namespace MyFaction;

use MyFaction\Language;

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
		
		$this->language = new Language($this);
		$this->language->lang_init($this->config->get('language'));
		
		$this->getCommand("faction")->setExecutor(new Commands\FactionCommand($this));
		$this->getCommand("factionadmin")->setExecutor(new Commands\FactionAdminCommand($this));
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

	### INTERNAL ###
	
	public function getLanguage(){
		return $this->language;
	}
	
	public function getDatabase(){
		return $this->database;
	}
	
}