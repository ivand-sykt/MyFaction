<?php

namespace MyFaction;

use MyFaction\MyFaction;

use pocketmine\plugin\Plugin;

class EconomyManager {
	
	public $api;
	
	public function __construct(MyFaction $plugin) {
		$this->plugin = $plugin;
		$this->language = $this->plugin->getLanguage();
	}
	
	public function economy_init() {
		$this->server = $this->plugin->getServer();
		
		$plugin = $this->server->getPluginManager()->getPlugin('EconomyAPI');

		if($plugin instanceof Plugin) {
			$this->server->getLogger()->info($this->language->getMessage('economy_found'));
			$this->api = $plugin::getInstance();
		} else {
			$this->server->getLogger()->error($this->language->getMessage('economy_not_found'));
			$this->server->getPluginManager()->disablePlugin($this->plugin);
		}
	}
	
}