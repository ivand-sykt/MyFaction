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

	}
	
}