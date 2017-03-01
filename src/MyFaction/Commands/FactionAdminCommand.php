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
			
			$senderData = $this->database->getPlayerInfo($senderName);
			
			if($senderData == null){ 
				$sender->sendMessage($this->language->getMessage('faction_notIn'));
				return;
			}
			
			switch(array_shift($args)) {
				
				case "delete":
					if($senderData['factionLevel'] != MyFaction::LEADER_LEVEL) {
						$sender->sendMessage($this->language->getMessage('faction_notLeader'));
						return;
					}
					
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage('faction_noName'));
						return;
					}
					
					$targetFaction = strtolower($args[0]);
					
					if($senderData['factionName'] != $targetFaction){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}
					
					$this->plugin->getDatabase()->deleteFaction($targetFaction);
					$sender->sendMessage($this->language->getMessage('faction_deleted'));
				break;

				case "changerank":
					if($senderData['factionLevel'] != MyFaction::LEADER_LEVEL) {
						$sender->sendMessage($this->language->getMessage('faction_notLeader'));
						return;
					}
					
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage('faction_noPlayer'));
						return;
					}

					$player = strtolower($args[0]);
					$targetData = $this->database->getPlayerInfo($player);
					if($targetData['factionName'] != $senderData['factionName']){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}
					
					if(!isset($args[1])){
						$sender->sendMessage($this->language->getMessage('faction_noRank'));
						return;
					}
					
					$level = $this->detectLevel($args[1]);
					
					if($level == false){
						$sender->sendMessage($this->language->getMessage('faction_wrongLevel'));
						return;
					} else {
						$this->database->setPlayerLevel($player, $level);
						$sender->sendMessage($this->language->getMessage('faction_level'));
						return;
					}
				break;
				
				case "sethome":
					
					if($senderData['factionLevel'] != MyFaction::LEADER_LEVEL and $senderData['factionLevel'] != MyFaction::OFFICER_LEVEL){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}	
					
					$x = $sender->getFloorX();
					$y = $sender->getFloorY();
					$z = $sender->getFloorZ();
					$faction = $senderData['factionName'];
				
					$this->database->setHome($x, $y, $z, $faction);
					$sender->sendMessage($this->language->getMessage('faction_setHome'));
				break;
				
				case "delhome":
					if($senderData['factionLevel'] != MyFaction::LEADER_LEVEL and $senderData['factionLevel'] != MyFaction::OFFICER_LEVEL){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}

					$faction = $senderData['factionName'];
					
					$this->database->deleteHome($faction);
					$sender->sendMessage($this->language->getMessage('faction_delHome'));
				break;

				case "kick":
					if($senderData['factionLevel'] != MyFaction::LEADER_LEVEL and $senderData['factionLevel'] != MyFaction::OFFICER_LEVEL){
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
					
					if($commiter['faction'] != $target['faction']){
						$sender->sendMessage($this->language->getMessage('noPermission'));
						return;
					}
					
					// officer tries to kick leader
					if($commiter['factionLevel'] == MyFaction::OFFICER_LEVEL and $target['factionLevel'] == MyFaction::LEADER_LEVEL){
						$sender->sendMessage($this->language->get('noPermission'));
						return;
					}
					// officer tries to kick officer
					if($commiter['factionLevel'] == MyFaction::OFFICER_LEVEL and $target['factionLevel'] == MyFaction::OFFICER_LEVEL){
						$sender->sendMessage($this->language->get('noPermission'));
						return;
					}
					
					$this->database->kickPlayer($targetName);
					
				break;
				
				case "invite":
					if($senderData['factionLevel'] != MyFaction::LEADER_LEVEL and $senderData['factionLevel'] != MyFaction::OFFICER_LEVEL){
						$sender->sendMessage($this->language->get('noPermission'));
						return;
					} 

					if(!isset($args[0])) {
						$sender->sendMessage($this->language->getMessage('faction_noPlayer'));
						return;
					}

					$nickname = strtolower($args[0]);
					
					if($this->getPlayer($nickname) instanceof Player){
						$this->plugin->pendInvite($nickname, $senderData['factionName']);
						return;
					} else {
						$sender->sendMessage($this->language->getMessage('faction_notOnline'));
					}
					
				break;
				
				case "changeowner":
					if($senderData['factionLevel'] != MyFaction::LEADER_LEVEL){
						$sender->sendMessage($this->language->get('noPermission'));
						return;
					} 
					
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage('faction_noPlayer'));
						return;
					}
					
					$targetName = strtolower($args[0]);
					$targetData = $this->database->getPlayerInfo($targetName);
					
					if($targetData == null){
						$sender->sendMessage($this->language->getMessage('faction_noTargetIn'));
						return;
					}
					
					if($targetData['factionName'] != $senderData['factionName']){
						$sender->sendMessage($this->language->get('faction_notSame'));
						return;
					}
					
					$this->database->changeOwnership($senderName, $targetName, $senderData['factionName']);
				break;
			}
		}

	}
	
	private function detectLevel($level) {
		(string) $level = strtolower($level);
		switch($level) {
			case "0":
			case "1":
			case "normal":
			case "player":
				return MyFaction::NORMAL_LEVEL;
				break;
			case "2":
			case "capitain":
				return MyFaction::CAPITAIN_LEVEL;
				break;
			case "3":
			case "officer":
				return MyFaction::OFFICER_LEVEL;
				break;
			default:
				return false;
		}
	}
	
	private function getPlayer($nickname) {
		return $this->plugin->getServer()->getPlayer($nickname);
	}
	
	private function notifyPlayer(string $nickname, string $message) {
		if($this->getPlayer($nickname) instanceof Player) {
			$player->sendMessage($message);
		}
		return;
	}
	
}