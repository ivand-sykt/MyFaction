<?php

namespace MyFaction\Commands;

use MyFaction\MyFaction;

use pocketmine\Player;

use pocketmine\math\Vector3;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class FactionCommand implements CommandExecutor {
	
	public function __construct(MyFaction $plugin){
		$this->plugin = $plugin;
		$this->language = $this->plugin->getLanguage();
		$this->database = $this->plugin->getDatabase();
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){

		if($sender instanceof Player){

			$senderName = strtolower($sender->getName());
			$senderData = $this->database->getPlayerInfo($senderName);
			
			switch(array_shift($args)) {
				case "create":
					if($senderData != null){
						$sender->sendMessage($this->language->getMessage('faction_already'));
						return;
					}
					
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage('faction_noName'));
						return;
					}
					
					// economy TODO
					
					$this->database->registerFaction($args[0], $senderName);
				break;
				
				case "home":
					if($senderData == null){
						$sender->sendMessage($this->language->getMessage('faction_notIn'));
						return;
					}
					
					$home = $this->database->getHome($senderData['factionName']);
					if($home == null){
						$sender->sendMessage($this->language->getMessage('faction_noHome'));
						return;
					}
					
					$sender->teleport(new Vector3($home['x'], $home['y'], $home['z']));
					$sender->sendMessage($this->language->getMessage('faction_home'));
				break;
				
				case "help":

				break;
				
				case "info":
					if(!isset($args[0])){
						
						return;
					}
					
				break;
				
				case "leave":
					if($senderData == null){
						$sender->sendMessage($this->language->getMessage('faction_notIn'));
						return;
					}
					
					if($senderData['factionLevel'] == MyFaction::LEADER_LEVEL){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}
					
					$this->database->kickPlayer($senderName);
					$sender->sendMessage($this->language->getMessage('faction_left'));
				break;
				
				case "accept":
					if($senderData != null){
						$sender->sendMessage($this->language->getMessage('faction_already'));
						return;
					}
					
				break;
			}


		}
	
	}
	
}