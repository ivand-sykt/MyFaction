<?php

namespace MyFaction\Database;

interface BaseDatabase {
	
	public function registerFaction(string $faction, string $owner);
	
	public function deleteFaction(string $faction);
	
	public function changeOwnership(string $oldLeader, string $newLeader, string $faction);
	
	public function getFactionInfo(string $faction);
	
	public function registerPlayer(string $nickname, string $faction);
	
	public function kickPlayer(string $nickname);
	
	/*
	** Gets player data
	** Returns false, if player is not in a faction
	** Otherwise, returns array with data:
	** $data['nickname'] => string, player's name
	** $data['factionName'] => string, name of faction
	** $data['exp'] => int, how much expeirence did player brought to faction
	** $data['factionLevel'] => int, faction rank (see at line 11)
	*/
	
	public function getPlayerInfo(string $nickname);
	
	public function setPlayerLevel(string $nickname, int $level);
	
	public function setHome(int $x, int $y, int $z, string $faction);

	public function deleteHome(string $faction);
	
	public function getHome(string $faction);
	
	public function close();

}