<?php

namespace MyFaction\Commands;

use MyFaction\MyFaction;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class FactionCommand implements CommandExecutor {
	
	public function __construct(MyFaction $plugin){
		$this->plugin = $plugin;
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$data = $this->plugin->getPlayerData(strtolower($sender->getName()));
		
		if($data == false){
			
			return;
		}
	}
	
}