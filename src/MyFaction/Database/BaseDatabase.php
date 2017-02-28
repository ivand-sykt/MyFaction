<?php

namespace MyFaction\Database;

interface BaseDatabase {
	
	public function registerFaction(string $faction, string $owner);
	
	public function deleteFaction(string $faction);
	
	public function getFactionInfo(string $faction);
	
	public function registerPlayer(string $nickname, string $faction);
	
	public function kickPlayer(string $nickname);
	
	public function getPlayerInfo(string $nickname);
	
	public function setPlayerLevel(string $nickname, int $level);
	
	public function setHome(int $x, int $y, int $z, string $faction);

	public function deleteHome(string $faction);
	
	public function getHome(string $faction);
	
	public function close();

}