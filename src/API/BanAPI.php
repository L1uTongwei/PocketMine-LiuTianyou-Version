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

$GLOBALS['BanAPI'] = "defined";

class BanAPI{
	private $server;
    /*
     * I would use PHPDoc Template here but PHPStorm does not recognise it. - @sekjun9878
     */
    /** @var Config */
	private $whitelist;
    /** @var Config */
	private $banned;
    /** @var Config */
	private $ops;
    /** @var Config */
	private $bannedIPs;
	private $cmdWL = array();//Command WhiteList
	function __construct(){
		$this->server = ServerAPI::request();
	}
	
	public function init(){
		$this->whitelist = new Config(DATA_PATH."white-list.txt", CONFIG_LIST);//Open whitelist list file
		$this->bannedIPs = new Config(DATA_PATH."banned-ips.txt", CONFIG_LIST);//Open Banned IPs list file
		$this->banned = new Config(DATA_PATH."banned.txt", CONFIG_LIST);//Open Banned Usernames list file
		$this->ops = new Config(DATA_PATH."ops.txt", CONFIG_LIST);//Open list of OPs
		if($this->server->proxy == false){ //代理模式
			$this->server->api->console->register("banip", "<add|remove|list|reload> [IP|玩家名称]", array($this, "commandHandler"));
			$this->server->api->console->alias("ban-ip", "banip add");
			$this->server->api->console->alias("pardon-ip", "banip remove");
		}
		$this->server->api->console->register("ban", "<add|remove|list|reload> [玩家名称]", array($this, "commandHandler"));
		$this->server->api->console->register("kick", "<玩家名称> [理由……]", array($this, "commandHandler"));
		$this->server->api->console->register("whitelist", "<on|off|list|add|remove|reload> [玩家名称]", array($this, "commandHandler"));
		$this->server->api->console->register("op", "<玩家名称>", array($this, "commandHandler"));
		$this->server->api->console->register("deop", "<玩家名称>", array($this, "commandHandler"));
		$this->server->api->console->register("sudo", "<玩家名称>", array($this, "commandHandler"));
		$this->server->api->console->alias("banlist", "ban list");
		$this->server->api->console->alias("pardon", "ban remove");
		$this->server->addHandler("console.command", array($this, "permissionsCheck"), 1);//Event handler when commands are issued. Used to check permissions of commands that go through the server.
		$this->server->addHandler("player.block.break", array($this, "permissionsCheck"), 1);//Event handler for blocks
		$this->server->addHandler("player.block.place", array($this, "permissionsCheck"), 1);//Event handler for blocks
		$this->server->addHandler("player.flying", array($this, "permissionsCheck"), 1);//Flying Event
	}

    /**
     * @param string $cmd Command to Whitelist
     */
    public function cmdWhitelist($cmd){//Whitelists a CMD so everyone can issue it - Even non OPs.
		$this->cmdWhitelist[strtolower(trim($cmd))] = true;
	}

    /**
     * @param string $username
     *
     * @return boolean
     */
    public function isOp($username){//Is a player op?
		$username = strtolower($username);
		if($this->server->api->dhandle("op.check", $username) === true){
			return true;
		}elseif($this->ops->exists($username)){
			return true;
		}
		return false;	
	}

    /**
     * @param mixed $data
     * @param string $event
     *
     * @return boolean
     */
    public function permissionsCheck($data, $event){
		switch($event){
			case "player.flying"://OPs can fly around the server.
				if($this->isOp($data->iusername)){
					return true;
				}
				break;
			case "player.block.break":
			case "player.block.place"://Spawn protection detection. Allows OPs to place/break blocks in the spawn area.
				if(!$this->isOp($data["player"]->iusername)){
					$t = new Vector2($data["target"]->x, $data["target"]->z);
					$s = new Vector2($this->server->spawn->x, $this->server->spawn->z);
					if($t->distance($s) <= $this->server->api->getProperty("spawn-protection") and $this->server->api->dhandle($event.".spawn", $data) !== true){
						return false;
					}
				}
				return;
				break;
			case "console.command"://Checks if a command is allowed with the current user permissions.
				if(isset($this->cmdWhitelist[$data["cmd"]])){
					return;
				}
				
				if($data["issuer"] instanceof Player){
					if($this->server->api->handle("console.check", $data) === true or $this->isOp($data["issuer"]->iusername)){
						return;
					}
				}elseif($data["issuer"] === "console"){
					return;
				}
				return false;
			break;
		}
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
			case "sudo":
				$target = strtolower(array_shift($params));
				$player = $this->server->api->player->get($target);
				if(!($player instanceof Player)){
					$output .= "玩家未在线。\n";
					break;
				}
				$this->server->api->console->run(implode(" ", $params), $player);
				$output .= "以".$player->username."身份运行命令。\n";
				break;
			case "op":
				$user = strtolower($params[0]);
				if($user == NULL){
				  $output .= "用法： /op <玩家名称>\n";
				  break;
				}
				if($this->server->safeMode != 0){
					$output .= "警告：服务器开启了安全模式，不允许服务器管理员存在，如果你有验证插件，请关闭安全模式。";
					break;
				}
				$player = $this->server->api->player->get($user);
				if(!($player instanceof Player)){
					$this->ops->set($user);
					$this->ops->save($user);
					$output .= $user." 现在成为服务器管理员。\n";
					break;
				}
				$this->ops->set($player->iusername);
				$this->ops->save();
				$output .= $player->iusername." 现在成为服务器管理员。\n";
				$this->server->api->chat->sendTo(false, "你现在是服务器管理员了。", $player->iusername);
				break;
			case "deop":
				$user = strtolower($params[0]);
				$player = $this->server->api->player->get($user);
				if(!($player instanceof Player)){
					$this->ops->remove($user);
					$this->ops->save();
					$output .= $user." 不再是服务器管理员。\n";
					break;
				}
				$this->ops->remove($player->iusername);
				$this->ops->save();
				$output .= $player->iusername." 不再是服务器管理员。\n";
				$this->server->api->chat->sendTo(false, "你不再是服务器管理员了。", $player->iusername);
				break;
			case "kick":
				if(!isset($params[0])){
					$output .= "用法： /kick <玩家名称> [原因……]\n";
				}else{
					$name = strtolower(array_shift($params));
					$player = $this->server->api->player->get($name);
					if($player === false){
						$output .= "玩家 \"".$name."\" 不存在。\n";
					}else{
						$reason = implode(" ", $params);
						$reason = $reason == "" ? "无理由踢出":$reason;
						
						$this->server->schedule(60, array($player, "close"), "你被踢出了： ".$reason); //Forces a kick
						$player->blocked = true;
						if($issuer instanceof Player){
							$this->server->api->chat->broadcast($player->username." 被 ".$issuer->username."以 $reason 的理由踢出。\n");
						}else{
							$this->server->api->chat->broadcast($player->username." 被踢出了： $reason\n");
						}
					}
				}
				break;
			case "whitelist":
				$p = strtolower(array_shift($params));
				switch($p){
					case "remove":
						$user = strtolower($params[0]);
						$this->whitelist->remove($user);
						$this->whitelist->save();
						$output .= "玩家 \"$user\" 从白名单中删除了。\n";
						break;
					case "add":
						$user = strtolower($params[0]);
						$this->whitelist->set($user);
						$this->whitelist->save();
						$output .= "玩家 \"$user\" 已添加到白名单。\n";
						break;
					case "reload":
						$this->whitelist = new Config(DATA_PATH."white-list.txt", CONFIG_LIST);
						break;
					case "list":
						$output .= "白名单： ".implode(", ", $this->whitelist->getAll(true))."\n";
						break;
					case "on":
					case "true":
					case "1":
						$output .= "白名单开启\n";
						$this->server->api->setProperty("white-list", true);
						break;
					case "off":
					case "false":
					case "0":
						$output .= "白名单关闭\n";
						$this->server->api->setProperty("white-list", false);
						break;
					default:
						$output .= "用法: /whitelist <on|off|list|add|remove|reload> [玩家名称]\n";
						break;
				}
				break;
			case "banip":
				$p = strtolower(array_shift($params));
				switch($p){
					case "pardon":
					case "remove":
						$ip = strtolower($params[0]);
						$this->bannedIPs->remove($ip);
						$this->bannedIPs->save();
						$output .= "IP \"$ip\" 从封禁名单中移除了。\n";
						break;
					case "add":
					case "ban":
						$ip = strtolower($params[0]);
						$player = $this->server->api->player->get($ip);
						if($player instanceof Player){
							$ip = $player->ip;
							$player->close("banned");
						}
						$this->bannedIPs->set($ip);
						$this->bannedIPs->save();
						$output .= "IP \"$ip\" 加入了封禁名单。\n";
						break;
					case "reload":
						$this->bannedIPs = new Config(DATA_PATH."banned-ips.txt", CONFIG_LIST);
						break;
					case "list":
						$output .= "IP封禁名单： ".implode(", ", $this->bannedIPs->getAll(true))."\n";
						break;
					default:
						$output .= "用法: /banip <add|remove|list|reload> [IP|玩家名称]\n";
						break;
				}
				break;
			case "ban":
				$p = strtolower(array_shift($params));
				switch($p){
					case "pardon":
					case "remove":
						$user = strtolower($params[0]);
						$this->banned->remove($user);
						$this->banned->save();
						$output .= "玩家 \"$user\" 从封禁名单中移除了。\n";
						break;
					case "add":
					case "ban":
						$user = strtolower($params[0]);
						$this->banned->set($user);
						$this->banned->save();
						$player = $this->server->api->player->get($user);
						if($player !== false){
							$player->close("You have been banned");
						}
						if($issuer instanceof Player){
							$this->server->api->chat->broadcast($user." 被 ".$issuer->username." 封禁了。\n");
						}else{
							$this->server->api->chat->broadcast($user." 被封禁了。\n");
						}
						$this->kick($user, "Banned");
						$output .= "玩家 \"$user\" 被加入到封禁名单中。\n";
						break;
					case "reload":
						$this->banned = new Config(DATA_PATH."banned.txt", CONFIG_LIST);
						break;
					case "list":
						$output .= "封禁名单： ".implode(", ", $this->banned->getAll(true))."\n";
						break;
					default:
						$output .= "用法： /ban <add|remove|list|reload> [玩家名称]\n";
						break;
				}
				break;
		}
		return $output;
	}

    /**
     * @param string $username
     */
    public function ban($username){
		$this->commandHandler("ban", array("add", $username), "console", "");
	}

    /**
     * @param string $username
     */
	public function pardon($username){
		$this->commandHandler("ban", array("pardon", $username), "console", "");
	}

    /**
     * @param string $ip
     */
	public function banIP($ip){
		$this->commandHandler("banip", array("add", $ip), "console", "");
	}

    /**
     * @param string $ip
     */
	public function pardonIP($ip){
		$this->commandHandler("banip", array("pardon", $ip), "console", "");
	}

    /**
     * @param string $username
     * @param string $reason
     */
    public function kick($username, $reason = "No Reason"){
		$this->commandHandler("kick", array($username, $reason), "console", "");
	}
	
	public function reload(){
		$this->commandHandler("ban", array("reload"), "console", "");
		$this->commandHandler("banip", array("reload"), "console", "");
		$this->commandHandler("whitelist", array("reload"), "console", "");
	}

    /**
     * @param string $ip
     *
     * @return boolean
     */
    public function isIPBanned($ip){
		if($this->server->api->dhandle("api.ban.ip.check", $ip) === false){
			return true;
		}elseif($this->bannedIPs->exists($ip, true)){
			return true;
		}else{
			return false;
        }
	}

    /**
     * @param string $username
     *
     * @return boolean
     */
    public function isBanned($username){
		$username = strtolower($username);
		if($this->server->api->dhandle("api.ban.check", $username) === false){
			return true;
		}elseif($this->banned->exists($username, true)){
			return true;
		}else{
			return false;
        }
	}

    /**
     * @param string $username
     *
     * @return boolean
     */
    public function inWhitelist($username){
		$username = strtolower($username);
		if($this->isOp($username)){
			return true;
		}elseif($this->server->api->dhandle("api.ban.whitelist.check", $username) === false){
			return true;
		}elseif($this->whitelist->exists($username, true)){
			return true;
		}
		return false;	
	}
}
