<?php

namespace MyFaction;

use MyFaction\MyFaction;

use pocketmine\event\Listener;
use pocketmine\event\EventPriority;

use pocketmine\event\block\BlockBreakEvent;

use pocketmine\event\player\PlayerDeathEvent;

class EventListener implements Listener {
	
	public function __construct(MyFaction $plugin){
		$this->plugin = $plugin;
		$this->blocks = $this->plugin->config->get('break_block_allowed');
	}
	
	/**
	 *  @priority MONITOR
	 */
	
	public function onBreak(BlockBreakEvent $event){
		if(!$event->isCancelled()){
			$blockId = $event->getBlock()->getId();
			
			if(in_array($blockId, $this->blocks)){
				$exp = $this->plugin->config->get('break_block_exp');
				$this->plugin->getExperienceManager()->addExperience(strtolower($event->getPlayer()->getName()), $exp);
			}
			
		}
	}
	
	/**
	 * @priority HIGHEST 
	 */
	 
}