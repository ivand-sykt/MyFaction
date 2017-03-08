<?php

namespace MyFaction\Managers;

use MyFaction\MyFaction;

class TropheyManager {
	
	public function __construct(MyFaction $plugin){
		$this->plugin = $plugin;
		$this->database = $this->plugin->getDatabase();
	}
	
	public function buyTrophey($faction, $trophey){
		
	}
	
	public function getFactionTropheys($faction){
		
	}
	
	public function getAllTropheys(){
		
	}
	
	public function getTropheyLevel($faction, $trophey){
		
	}
	
	public function setTropheyLevel($trophey, $level){
		
	}
	
	public function getTrophey($trophey){
		switch((string) strtolower($trophey)){
			case "trophey_save":
				return 1;
			break;
		}
	}
	
}