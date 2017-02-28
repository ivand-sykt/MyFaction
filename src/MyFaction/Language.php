<?php

namespace MyAuth;

use MyAuth\MyAuth;
use pocketmine\utils\Config;

class Language {
	
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
	}
	
	public function getMessage(string $message, $keys = [], $values = []){
		$message = $this->messages->get($message);
		$message = str_replace($keys, $values, $message);
		return $message;
	}
	
	public function lang_init(string $language){
		$data = $this->plugin->getDataFolder();
		
		if(!is_file($data . "lang_$language.yml")){
			$this->plugin->saveResource("lang_$language.yml");
			$this->messages = new Config($data . "lang_$language.yml", Config::YAML);
			$this->plugin->getLogger()->info($this->getMessage('lang_selected'));
		} else {
			$this->messages = new Config($data . "lang_$language.yml", Config::YAML);
			$this->plugin->getLogger()->info($this->getMessage('lang_selected'));
		}
	}
}