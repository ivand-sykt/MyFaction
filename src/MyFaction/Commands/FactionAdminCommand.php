<?php

namespace MyFaction\Commands;

use MyFaction\MyFaction;

use pocketmine\Player;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class FactionAdminCommand implements CommandExecutor {
	
	public function __construct(MyFaction $plugin) {
		$this->plugin = $plugin;
		$this->language = $this->plugin->getLanguage();
		$this->database = $this->plugin->getDatabase();
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {

		if($sender instanceof Player) {

			$senderName = strtolower($sender->getName());
			
			$data = $this->database->getPlayerInfo($senderName);
			
			if($data == null){ 
				$sender->sendMessage($this->language->getMessage('faction_notIn'));
				return;
			}
			
			switch(array_shift($args)) {
				case "delete":
					if($data['factionLevel'] != MyFaction::LEADER_LEVEL) {
						$sender->sendMessage($this->language->getMessage('faction_notLeader'));
						return;
					}
					
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage('faction_noName'));
						return;
					}
					
					if($data['factionName'] != $args[0]){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}
					
					$this->plugin->getDatabase()->deleteFaction($args[0]);
					$sender->sendMessage($this->language->getMessage('faction_deleted'));
				break;

				case "changerank":
					if($data['factionLevel'] != MyFaction::LEADER_LEVEL) {
						$sender->sendMessage($this->language->getMessage('faction_notLeader'));
						return;
					}
					
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage('faction_noPlayer'));
						return;
					}

					if(!isset($args[1])){
						$sender->sendMessage($this->language->getMessage('faction_noRank'));
						return;
					}
					
				break;
				
				case "sethome":
					
					if($data['factionLevel'] != MyFaction::LEADER_LEVEL and $data['factionLevel'] != MyFaction::OFFICER_LEVEL){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}	
					
					$x = $sender->getFloorX();
					$y = $sender->getFloorY();
					$z = $sender->getFloorZ();
					$faction = $data['factionName'];
				
					$this->database->setHome($x, $y, $z, $faction);
					$sender->sendMessage($this->language->getMessage('faction_setHome'));
				break;
				
				case "delhome":
					if($data['factionLevel'] != MyFaction::LEADER_LEVEL and $data['factionLevel'] != MyFaction::OFFICER_LEVEL){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}

					$faction = $data['factionName'];
					
					$this->database->deleteHome($faction);
					$sender->sendMessage($this->language->getMessage('faction_delHome'));
				break;

				case "kick":
					if($data['factionLevel'] != MyFaction::LEADER_LEVEL or $data['factionLevel'] != MyFaction::OFFICER_LEVEL){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}
					
					if(!isset($args[0])) {
						$sender->sendMessage($this->language->getMessage('faction_noPlayer'));
						return;
					}
					
					$targetName = strtolower($args[0]);
					
					$commiter = $this->database->getPlayerInfo($senderName);
					$target = $this->database->getPlayerInfo($targetName);
					
					if($nickname == $targetName){
						$sender->sendMessage($this->language->getMessage('faction_notSelf'));
						return;
					}
					
					if($commiter['faction'] != $targer['faction']){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}
					
					if($commiter['factionLevel'] == MyFaction::OFFICER_LEVEL and $target['factionLevel'] == MyFaction::LEADER_LEVEL){
						$sender->sendMessage($this->language->get('noPermission'));
						return;
					}
					
					$this->database->kickPlayer($targetName);
					
				break;
				
				case "invite":
					// TODO
				break;				
			}
		}

	}
	
}