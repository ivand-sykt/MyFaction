<?php

namespace MyFaction\Commands;

use MyFaction\MyFaction;

use pocketmine\Player;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class MyFactionCommand implements CommandExecutor {
	
	public function __construct(MyFaction $plugin){
		$this->plugin = $plugin;
		$this->language = $this->plugin->getLanguage();
		$this->database = $this->plugin->getDatabase();
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if($sender instanceof ConsoleCommandSender
			or $sender->hasPermission('myfaction.myfaction')) {
			
			switch(strtolower(array_shift($args))){
				case "info":
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage("faction_noName"));
						return;
					}
				
					$factionName = strtolower($args[0]);
					
					$factionData = $this->database->getFactionInfo($factionName);
					
					if($factionData == null){
						$sender->sendMessage($this->language->getMessage('faction_noFaction'));
						return;
					}
					
					$level = $this->plugin->getFactionLevel($factionData['exp']);
					$data = $this->database->getFactionPlayers($factionData['factionName']);
					
					foreach($data as $nickname => $factionLevel){
						$name = $this->plugin->getRankName($factionLevel);
						$players[] = "$nickname ($name)";
					}
					$players = implode(', ', $players);
					
					$message = $this->language->getMessage('info_other',
					['{faction}', '{leader}', '{factionLevel}', '{minexp}', '{maxexp}', '{players}'],
					[$factionData['factionMask'], $factionData['leader'], $level, $factionData['exp'], $this->plugin->getMaxExp($level), $players]);

					$sender->sendMessage($message);
				break;
				
				case "delete":
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage("faction_noName"));
						return;
					}

					$targetFaction = strtolower($args[0]);

					$this->plugin->getDatabase()->deleteFaction($targetFaction);
					$sender->sendMessage($this->language->getMessage('faction_deleted'));
				break;
				
				case "playerinfo":
					if(!isset($args[0])){
						$sender->sendMessage($this->language->getMessage("faction_noPlayer"));
						return;
					}

					$player = strtolower($args[0]);
					
					$data = $this->database->getPlayerInfo($player);
					
					if($data == null){
						$sender->sendMessage($this->language->getMessage("faction_noTargetIn"));
						return;
					}
					
					$message = $this->language->getMessage('info_player',
					['{player}', '{faction}', '{rank}', '{exp}'],
					[$args[0], $data['factionName'], $this->plugin->getRankName($data['factionLevel']), $data['exp']]);
					
					$sender->sendMessage($message);
				break;
			}
		}
	}
	
}