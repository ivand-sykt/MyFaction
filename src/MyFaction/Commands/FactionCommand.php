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

		if(!$sender instanceof Player){
			$sender->sendMessage($this->language->getMessage('ingame'));
			return;
		}

		$senderName = strtolower($sender->getName());
		$senderData = $this->database->getPlayerInfo($senderName);
		
		switch(array_shift($args)) {
			case "help":
				$sender->sendMessage($this->language->getMessage('faction_help'));
			break;
			
			case "create":
				if(isset($senderData['factionName'])){
					$sender->sendMessage($this->language->getMessage('faction_alreadyIn'));
					return;
				}
				
				if(!isset($args[0])){
					$sender->sendMessage($this->language->getMessage('faction_noName'));
					return;
				}
					
				$data = $this->database->getFactionInfo($args[0]);
				
				if(isset($data['factionName'])){
					$sender->sendMessage($this->language->getMessage('faction_already'));
					return;
				}
				
				if($this->plugin->config->get('use_economy')){
					if($this->plugin->config->get('paid_faction')){
					
						$economy = $this->plugin->getEconomy();
						(int) $cost = $this->plugin->config->get('faction_cost');
						(int) $money = $economy->myMoney($senderName);
						
						if(!($money >= $cost)){
							$sender->sendMessage($this->language->getMessage('faction_noMoney', ['{needed}', '{current}'], [$cost, $money]));
							return;
						}
					
					}
				}
				
				$this->database->registerFaction($args[0], $senderName);
				$sender->sendMessage($this->language->getMessage('faction_created'));
				if($this->plugin->config->get('use_economy')) $economy->reduceMoney($senderName, $cost);
			
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
			
			case "info":
				if(!isset($args[0])){
						if($senderData == null){
						$sender->sendMessage($this->language->getMessage('faction_notIn'));
						return;
				}
					
					$factionData = $this->database->getFactionInfo($senderData['factionName']);
					(int) $level = $this->plugin->getFactionLevel($factionData['exp']);
					$data = $this->database->getFactionPlayers($senderData['factionName']);
					
					$players = implode(', ', $data);
					
					$message = $this->language->getMessage('info_self',
					['{faction}', '{leader}', '{level}', '{factionLevel}', '{minexp}', '{maxexp}', '{exp}', '{players}'],
					[$senderData['factionMask'], $factionData['leader'], $this->getLevelName($senderData['factionLevel']), $level, $factionData['exp'], $this->plugin->getMaxExp($level), $senderData['exp'], $players]);
					$sender->sendMessage($message);
				} else {
					//info about faction in args0				
					$factionName = strtolower($args[0]);
					
					$factionData = $this->database->getFactionInfo($factionName);
					$level = $this->plugin->getFactionLevel($factionName);
					$players = implode($this->database->getFactionPlayers($factionName));
					
					$message = $this->language->getMessage('info_other',
					['{faction}', '{leader}', '{factionLevel}', '{minexp}', '{maxexp}', '{players}'],
					[$factionData['factionMask'], $factionData['leader'], $level, $factionData['exp'], $this->plugin->getMaxExp($level), $players]);
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
				
				$invite = $this->plugin->getInvite($senderName);
				
				if($invite == null){
					$sender->sendMessage($this->language->getMessage('faction_noInvite'));
					return;
				}
				
			break;
		}

	}
	
	private function getLevelName(int $level){
		switch($level){
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