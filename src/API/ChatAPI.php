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

$GLOBALS['ChatAPI'] = "defined";

class ChatAPI{
	private $server;
	function __construct(){
		$this->server = ServerAPI::request();
	}
	
	public function init(){
		$this->server->api->console->register("tell", "<玩家名称> <私人信息……>", array($this, "commandHandler"));
		$this->server->api->console->register("me", "<动作……>", array($this, "commandHandler"));
		$this->server->api->console->register("say", "<信息……>", array($this, "commandHandler"));
		$this->server->api->ban->cmdWhitelist("tell");
		$this->server->api->ban->cmdWhitelist("me");
	}

    /**
     * @param string $cmd
     * @param array $params
     * @param string $issuer
     * @param string $alias
     *
     * @return string
     */
    public function commandHandler($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "say":
				$s = implode(" ", $params);
				if(trim($s) == ""){
					$output .= "用法： /say <信息>\n";
					break;
				}
				$sender = ($issuer instanceof Player) ? "Server":ucfirst($issuer);
				$this->server->api->chat->broadcast("[$sender] ".$s);
				break;
			case "me":
				if(!($issuer instanceof Player)){
					$sender = ucfirst($issuer);
				}else{
					$sender = $issuer->username;
				}
				$this->broadcast("* $sender ".implode(" ", $params));
				break;
			case "tell":
				if(!isset($params[0]) or !isset($params[1])){
					$output .= "用法: /$cmd <玩家名称> <信息>\n";
					break;
				}
				if(!($issuer instanceof Player)){
					$sender = ucfirst($issuer);
				}else{
					$sender = $issuer->username;
				}
				$n = array_shift($params);
				$target = $this->server->api->player->get($n);
				if($target instanceof Player){
					$target = $target->username;
				}else{
					$target = strtolower($n);
					if($target === "server" or $target === "console"){
						$target = "Console";
					}
				}
				$mes = implode(" ", $params);
				$output .= "[me -> ".$target."] ".$mes."\n";
				if($target !== "Console"){
					$this->sendTo(false, "[".$sender." -> me] ".$mes, $target);
				}
				if($target === "Console" or $sender === "Console"){
					console("[INFO] [".$sender." -> ".$target."] ".$mes);
				}
				break;
		}
		return $output;
	}

    /**
     * @param string $message
     */
    public function broadcast($message){
		$this->send(false, $message);
	}

    /**
     * @param string $owner
     * @param string $text
     * @param mixed $player Can be either Player object or string username. Boolean false for broadcast.
     */
    public function sendTo($owner, $text, $player){
		$this->send($owner, $text, array($player));
	}

    /**
     * @param mixed $owner Can be either Player object or string username. Boolean false for broadcast.
     * @param string $text
     * @param $whitelist
     * @param $blacklist
     */
    public function send($owner, $text, $whitelist = false, $blacklist = false){
		$message = array(
			"player" => $owner,
			"message" => $text,
		);
		if($owner !== false){
			if($owner instanceof Player){
				if($whitelist === false){
					console("[INFO] <".$owner->username."> ".$text);
				}
			}else{
				if($whitelist === false){
					console("[INFO] <".$owner."> ".$text);
				}
			}
		}else{
			if($whitelist === false){
				console("[INFO] $text");
			}
			$message["player"] = "";
		}

		$this->server->handle("server.chat", new Container($message, $whitelist, $blacklist));
	}
}