# MyFaction
MyFaction is simple factions plugin.

## Last update: v0.1-dev#1 (March 3rd, 2017)
* First release

## Permissions:
- This plugin does not have permissions

## Commands: 
- `/faction` - player commands (alias `f`)

 - `/faction create <faction name>` - creates a faction
 - `/faction info [faction name]` - gives information about faction `faction name`, if it's null, gives information about player's faction
 - `/faction home` - teleports player to faction's home
 - `/faction leave` - leave player's faction (Faction leaders cannot leave, howewer. They must change faction ownership of delete faction)
 - `/faction accept` - accept invitation to faction
 
- `/factionadmin` - commands for officers and leaders (alias `fadmin`)

 - `/factionadmin delete <player's faction name>` - deletes player's faction with all it's players (leader)
 - `/factionadmin changerank <player> <rank>` - changes player's rank (cannot change rank to leader) (leader)
 - `/factionadmin sethome` - sets faction's home to current position (officer, leader)
 - `/factionadmin delhome` - deletes faction's home (officer, leader)
 - `/factionadmin kick <player>` - kicks a player from the faction (officer, leader)
 - `/factionadmin invite <player>` - invites a player to the faction (officer, leader)
 - `/factionadmin changeowner <player>` - changes faction's leader to `<player>` (leader)
 
- `/myfaction` - factions configuration of the plugin

## Available ranks:
* Player - this rank is given after player accepts invitation to clan
* Capitain - have the same possibilites as player
* Officer - can use sofe of `/factionadmin` commands
* Leader - can do everything

## Configuration: 



## Planned features:
* Tropheys
* Gaining expeirence
* Faction funds