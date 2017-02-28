<?php

namespace MyFaction\Database;

use MyFaction\Database\BaseDatabase;
use MyFaction\MyFaction;

use pocketmine\Thread;

class MySQLDatabase extends Thread implements BaseDatabase {

	public static $database;
	
	public function __construct($dataPath, $config){
		$this->dataPath = $dataPath;
		$this->config = $config;
		$this->db_init();
	}
	
	public function db_init(){
		self::$database = new \mysqli($this->config->get('ip'), $this->config->get('username'), $this->config->get('password'));
		
		self::$database->select_db('plugins');
		
		$factionInit =
		"CREATE TABLE IF NOT EXISTS `factions` (
			factionName VARCHAR(255) NOT NULL PRIMARY KEY,
			exp INT NOT NULL,
			level INT NOT NULL,
			leader VARCHAR(16) NOT NULL
		);
		";
		
		$usersInit =
		"CREATE TABLE IF NOT EXISTS `users` (
			nickname VARCHAR(16) NOT NULL PRIMARY KEY,
			factionName varchar(255) NOT NULL,
			exp INT NOT NULL,
			factionLevel INT NOT NULL
		);
		";
		
		$homesInit =
		"CREATE TABLE IF NOT EXISTS `homes` (
			factionName VARCHAR(255) NOT NULL PRIMARY KEY,
			x INT NOT NULL,
			y INT NOT NULL,
			z INT NOT NULL
		);
		";
		
		self::$database->query($factionInit);
		self::$database->query($usersInit);
		self::$database->query($homesInit);
		
		$this->start();
	}
	
	public function registerFaction($faction, $owner) {
		self::$database->query(
		"INSERT INTO `factions` (factionName, exp, level, leader) VALUES
		('$faction', 0, 1, '$owner');");
		
		self::$database->query(
		"INSERT INTO `users` (nickname, factionName, exp, factionLevel) VALUES
		('$owner', '$faction', 0, 4);");
		
		return;
	}
	
	public function deleteFaction($faction){
		self::$database->query(
		"DELETE FROM `factions`
		WHERE `factionName` = '$faction';");
		
		self::$database->query(
		"DELETE FROM `users`
		WHERE `factionName` = '$faction';");
		
		self::$database->query(
		"DELETE FROM `homes`
		WHERE `factionName` = '$faction'");
		
		return;
	}
	
	public function getFactionInfo($faction){
		$data = self::$database->query(
		"SELECT * FROM `factions` 
		WHERE `factionName` = '$faction'");
		
		return $data->fetch_assoc();
	}
	
	public function registerPlayer($nickname, $faction){
		
	}
	
	public function kickPlayer($nickname, $faction){
		
	}
	
	public function getPlayerInfo($nickname){
		
	}
	
	public function setPlayerLevel($nickname, $faction){
		
	}
	
	public function setHome($x, $y, $z, $faction){
		
	}

	public function deleteHome($faction){
		
	}
	
	public function close(){
		@self::$database->close();
	}
	
	public function run() {
		//do nothing
	}
	
	public function getThreadName(){
		return "MyFaction requests";
	}
	
}