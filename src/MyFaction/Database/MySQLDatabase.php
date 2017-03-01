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
		
		// faction name - name in lower case
		// faction mask - shown name
		$factionInit =
		"CREATE TABLE IF NOT EXISTS `factions` (
			factionName VARCHAR(255) NOT NULL PRIMARY KEY,
			factionMask VARCHAR(255) NOT NULL,
			exp INT NOT NULL,
			leader VARCHAR(16) NOT NULL
		);
		";
		
		$usersInit =
		"CREATE TABLE IF NOT EXISTS `users` (
			nickname VARCHAR(16) NOT NULL PRIMARY KEY,
			factionName VARCHAR(255) NOT NULL,
			factionMask VARCHAR(255) NOT NULL,
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
	
	public function registerFaction(string $faction, string $owner) {
		$factionName = strtolower($faction);
		$level = MyFaction::LEADER_LEVEL;
		
		self::$database->query(
		"INSERT INTO `factions` (factionName, factionMask, exp, leader) VALUES
		('$factionName', '$faction', 0, '$owner');");
		
		self::$database->query(
		"INSERT INTO `users` (nickname, factionName, factionMask, exp, factionLevel) VALUES
		('$owner', '$factionName', '$faction', 0, $level);");
		
		return;
	}
	
	public function deleteFaction(string $faction){
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
	
	public function changeOwnership(string $oldLeader, string $newLeader, string $faction){
		self::$database->query(
		"UPDATE `factions` 
		SET leader='$newLeader'
		WHERE factionName = '$faction';");
		
		$officerLevel = MyFaction::OFFICER_LEVEL;
		$leaderLevel = MyFaction::LEADER_LEVEL;
		
		$this->setPlayerLevel($oldLeader, $officerLevel);
		$this->setPlayerLevel($newLeader, $leaderLevel);
	}
	
	public function getFactionInfo(string $faction){
		$data = self::$database->query(
		"SELECT * FROM `factions` 
		WHERE `factionName` = '$faction'");
		
		return $data->fetch_assoc();
	}
	
	public function registerPlayer(string $nickname, string $faction){
		$level = MyFaction::NORMAL_LEVEL;
		
		self::$database->query(
		"INSERT INTO `users` (nickname, factionName, exp, factionLevel) VALUES
		('$nickname', '$faction', 0, $level);");
	
		return;
	}
	
	public function kickPlayer(string $nickname){
		self::$database->query(
		"DELETE FROM `users` 
		WHERE `nickname` = '$nickname';");
		
		return;
	}
	
	public function getPlayerInfo(string $nickname){
		$data = self::$database->query(
		"SELECT * FROM `users` 
		WHERE `nickname` = '$nickname';");
		
		return $data->fetch_assoc();
	}
	
	public function setPlayerLevel(string $nickname, int $level){
		self::$database->query(
		"UPDATE `users`
		SET `factionLevel` = $level
		WHERE `nickname` = '$nickname';");
		
		return;
	}
	
	public function setHome(int $x, int $y, int $z, string $faction){
		// если нет дома
		if($this->getHome($faction) == null){
			self::$database ->query(
			"INSERT INTO `homes` (x, y, z, factionName) VALUES
			($x, $y, $z, '$faction');");
		} else {
		// если есть
			self::$database->query(
			"UPDATE `homes`
			SET `x` = $x, `y` = $y, `z` = $z
			WHERE `factionName` = '$faction';");
		}
		
		return;
	}

	public function deleteHome(string $faction){
		self::$database->query(
		"DELETE FROM `homes` 
		WHERE `factionName` = '$faction';");
	
		return;
	}
	
	public function getHome(string $faction){
		$data = self::$database->query(
		"SELECT * FROM `homes` 
		WHERE `factionName` = '$faction'");
	
		return $data->fetch_assoc();
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