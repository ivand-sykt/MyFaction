<?php

namespace MyFaction\Commands;

use MyFaction\MyFaction;

use pocketmine\Player;

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
					
				break;
				
				case "home":
					if($senderData == null){
						$sender->sendMessage($this->language->getMessage('faction_notIn'));
						return;
					}
					
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