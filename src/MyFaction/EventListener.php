<?php

namespace MyFaction;

use MyFaction\MyFaction;

use pocketmine\event\Listener;

class EventListener implements Listener {
	
	public function __construct(MyFaction $plugin){
		$this->plugin = $plugin;
	}
	
}