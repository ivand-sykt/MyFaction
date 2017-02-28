<?php

namespace MyFaction\Database;

interface BaseDatabase {
	
	public function registerFaction($faction, $owner);
	
	public function deleteFaction($faction);
	
	public function getFactionInfo($faction);
	
	public function registerPlayer($nickname, $faction);
	
	public function kickPlayer($nickname, $faction);
	
	public function getPlayerInfo($nickname);
	
	public function setPlayerLevel($nickname, $faction);
	
	public function setHome($x, $y, $z, $faction);

	public function deleteHome($faction);
	
	public function close();

}