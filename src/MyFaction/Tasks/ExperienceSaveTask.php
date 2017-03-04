<?php

namespace MyFaction\Tasks;

use MyFaction\MyFaction;

use pocketmine\scheduler\PluginTask;

class ExperienceSaveTask extends PluginTask {
	
	public function __construct(MyFaction $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun($currentTick){
		$this->plugin->getExperienceManager()->flushCache();
	}
	
}