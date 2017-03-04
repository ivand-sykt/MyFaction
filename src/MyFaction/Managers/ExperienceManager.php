<?php

namespace MyFaction\Managers;

use MyFaction\MyFaction;

class ExperienceManager {

	private $cache;
	
	public function __construct(MyFaction $plugin){
		$this->plugin = $plugin;
		$this->language = $this->plugin->getLanguage();
		$this->database = $this->plugin->getDatabase();
	}
	
	public function flushCache(){
		if($this->cache == null) return;
		foreach($this->cache as $player => $exp){
			$this->database->addPlayerExperience($player, $exp);
		}
		$this->cache = [];
	}
	
	public function addExperience(string $player, $exp){
		$this->cache[$player] ?? ($this->cache[$player] = 0);
		$this->cache[$player] += $exp;
	}
	
}
