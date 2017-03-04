<?php

namespace MyFaction;

use MyFaction\Language;
use MyFaction\EconomyManager;

use pocketmine\utils\Config;

use pocketmine\plugin\PluginBase;

class MyFaction extends PluginBase {
	
	const LEADER_LEVEL = 4;
	const OFFICER_LEVEL = 3;
	const CAPITAIN_LEVEL = 2;
	const NORMAL_LEVEL = 1;
	
	const BASE_EXP = 100;
	
	public $database;
	public static $instance;
	public $invites;
	public $economy;
	public $experience;
	
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
		$this->getCommand("myfaction")->setExecutor(new Commands\MyFactionCommand($this));
		
		if($this->config->get('use_economy')){
			$this->economy = new Managers\EconomyManager($this);
			$this->economy->economy_init();
		}

		$this->experience = new Managers\ExperienceManager($this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new Tasks\ExperienceSaveTask($this), 20 * 60);
		self::$instance = $this;
	}
	
	public function onDisable(){
		$this->database->close();
	}
	
	### API ###
	
	/*
	** Call this first, if you want to interact with plugin API
	** In main class of your plugin:
	** $api = $this->getServer()->getPluginManager()->getPlugin('MyFaction')
	** $api::getInstance();
	*/
	
	public static function getInstance() {
		return self::$instance;
	}

	public static function getPlayerFactionName(string $nickname){
		$data = $this->database->getPlayerInfo(strtolower($nickname));
		
		return $data['factionMask'] ?? null;
	}
	### INTERNAL ###
	
	public function pendInvite($nickname, $factionName){
		if($nickname instanceof Player){
			$nickname = $player->getName();
		}
		
		$nickname = strtolower($nickname);
		$this->invites[$nickname] = $factionName;
		return;
	}
	
	public function removeInvite($nickname){		
		if($nickname instanceof Player){
			$nickname = $player->getName();
		}
		
		$nickname = strtolower($nickname);
		unset($this->invites[$nickname]);
		return;
	}
	
	public function getInvite($nickname){
		return $this->invites[$nickname] ?? null;
	}
	
	public function getLanguage(){
		return $this->language;
	}
	
	public function getDatabase(){
		return $this->database;
	}
	
	public function getEconomy(){
		return $this->economy->api;
	}
	
	public function getExperienceManager(){
		return $this->experience;
	}
	
	public function getFactionLevel($exp){
		return floor(sqrt($exp / self::BASE_EXP));
	}
	
	public function getMaxExp($level){
		return floor(self::BASE_EXP * pow(($level + 1), 2));

		// level + 1 is required to get next level's max xp, otherwise it will return current level max
	}
	
	public function getRankName($level){
		switch((int) $level){
			case MyFaction::NORMAL_LEVEL:
				return $this->language->getMessage('player');
			break;
			
			case MyFaction::CAPITAIN_LEVEL:
				return $this->language->getMessage('capitain');
			break;
			
			case MyFaction::OFFICER_LEVEL:
				return $this->language->getMessage('officer');
			break;
			
			case MyFaction::LEADER_LEVEL:
				return $this->language->getMessage('leader');
			break;
		}
	}
	
}