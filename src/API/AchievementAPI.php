<?php

/**
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

$GLOBALS['__AchievementAPI'] = "defined";

class AchievementAPI{
	public static $achievements = array(
		"openInventory" => array(
			"name" => "首次进入",
			"requires" => array(),
		),
		"mineWood" => array(
			"name" => "获得木头",
			"requires" => array(
				"openInventory",
			),
		),
		"buildWorkBench" => array(
			"name" => "创造时间",
			"requires" => array(
				"mineWood",
			),
		),
		"buildPickaxe" => array(
			"name" => "该我了！",
			"requires" => array(
				"buildWorkBench",
			),
		),
		"buildFurnace" => array(
			"name" => "热门头条",
			"requires" => array(
				"buildPickaxe",
			),
		),
		"acquireIron" => array(
			"name" => "来点硬的",
			"requires" => array(
				"buildFurnace",
			),
		),
		"buildHoe" => array(
			"name" => "是时候做农活了！",
			"requires" => array(
				"buildWorkBench",
			),
		),
		"makeBread" => array(
			"name" => "法国面包",
			"requires" => array(
				"buildHoe",
			),
		),
		"bakeCake" => array(
			"name" => "蛋糕是个谎言",
			"requires" => array(
				"buildHoe",
			),
		),
		"buildBetterPickaxe" => array(
			"name" => "获得升级",
			"requires" => array(
				"buildPickaxe",
			),
		),
		"buildSword" => array(
			"name" => "狙击时刻",
			"requires" => array(
				"buildWorkBench",
			),
		),
		"diamonds" => array(
			"name" => "钻石！！！",
			"requires" => array(
				"acquireIron",
			),
		),
		"abnormal" => array( //获得下界反应核
			"name" => "反常物质",
			"requires" => array(
				"buildWorkBench",
				"diamonds",
			),
		),
		"greatsleep" => array(
			"name" => "甜蜜的梦",
			"requires" => array(
				"buildWorkBench",
			),
		),
		"peaceandlove" => array(
			"name" => "和平礼物",
			"requires" => array(
			),
		),
		"wqnmlgb" => array(
			"name" => "我去年买了个表[doge]",
			"requires" => array(
				"buildWprlBench",
			),
		),
	);

	function __construct(){
	}
	
	public static function broadcastAchievement(Player $player, $achievementId){
		if(isset(self::$achievements[$achievementId])){
			$result = ServerAPI::request()->api->dhandle("achievement.broadcast", array("player" => $player, "achievementId" => $achievementId));
			if($result !== false and $result !== true){
				if(ServerAPI::request()->api->getProperty("announce-player-achievements") == true){
					ServerAPI::request()->api->chat->broadcast($player->username." 得到了成就 ".self::$achievements[$achievementId]["name"]);
				}else{
					$player->sendChat("你刚刚得到了成就 ".self::$achievements[$achievementId]["name"]);
				}			
			}
			return true;
		}
		return false;
	}
	
	public static function addAchievement($achievementId, $achievementName, array $requires = array()){
		if(!isset(self::$achievements[$achievementId])){
			self::$achievements[$achievementId] = array(
				"name" => $achievementName,
				"requires" => $requires,
			);
			return true;
		}
		return false;
	}
	
	public static function hasAchievement(Player $player, $achievementId){
		if(!isset(self::$achievements[$achievementId]) or !isset($player->achievements)){
			$player->achievements = array();
			return false;
		}
		
		if(!isset($player->achievements[$achievementId]) or $player->achievements[$achievementId] == false){
			return false;
		}
		return true;
	}
	
	public static function grantAchievement(Player $player, $achievementId){
		if(isset(self::$achievements[$achievementId]) and !self::hasAchievement($player, $achievementId)){
			foreach(self::$achievements[$achievementId]["requires"] as $requerimentId){
				if(!self::hasAchievement($player, $requerimentId)){
					return false;
				}
			}
			if(ServerAPI::request()->api->dhandle("achievement.grant", array("player" => $player, "achievementId" => $achievementId)) !== false){
				$player->achievements[$achievementId] = true;
				self::broadcastAchievement($player, $achievementId);
				return true;
			}else{
				return false;
			}
		}
		return false;
	}
	
	public static function removeAchievement(Player $player, $achievementId){
		if(self::hasAchievement($player, $achievementId)){
			$player->achievements[$achievementId] = false;
		}
	}
	
	public function init(){
	}
}
