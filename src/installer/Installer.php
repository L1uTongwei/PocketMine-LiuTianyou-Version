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

/***REM_START***/

	class Installer{
		const DEFAULT_NAME = "Minecraft: PE Server";
		const DEFAULT_PORT = 19132;
		const DEFAULT_MEMORY = 128;
		const DEFAULT_PLAYERS = 20;
		const DEFAULT_GAMEMODE = SURVIVAL;
		
		private $lang, $config;
		public function __construct(){
			echo "[?] 您想跳过安装向导吗? (y/N): ";
			if(strtolower($this->getInput()) === "y"){
				return;
			}
			echo "\n";
			$this->welcome();
			$this->generateBaseConfig();
			$this->generateUserFiles();
			
			$this->networkFunctions();
			
			$this->endWizard();
		}
		
		private function welcome(){
			echo "欢迎来到PocketMine-MP!\n在开始使用您的新服务器之前您需要接受以下协议\nPocketMine-MP使用了LGPL协议,\n你可以在这个文件夹中找到LICENCE文件.\n";
			echo <<<LICENSE

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

LICENSE;
			echo "\n[?] 您接受协议内容吗?  (y/N): ";
			if(strtolower($this->getInput("n")) != "y"){
				echo "[!] 您必须接受LGPL协议来继续使用PocketMine-MP\n";
				sleep(5);
				exit(0);
			}
			echo "[*] 你现在要开始设置您的服务器了\n";
			echo "[*] 如果您希望留住默认设置请直接按下回车键. \n";
			echo "[*] 您以后可以在server.properties中修改设置.\n";
			
		}
		
		private function generateBaseConfig(){
			$config = new Config("./server.properties", CONFIG_PROPERTIES);
			echo "[?] 命名您的服务器:  (".self::DEFAULT_NAME."): ";
			$config->set("server-name", $this->getInput(self::DEFAULT_NAME));
			echo "[*] 尽量不要改变端口如果这是您第一次设置服务器. \n";
			do{
				echo "[?] 服务器端口:  (".self::DEFAULT_PORT."): ";
				$port = (int) $this->getInput(self::DEFAULT_PORT);
				if($port <= 0 or $port > 65535){
					echo "[!] 不正确的服务器端口. \n";
				}
			}while($port <= 0 or $port > 65535);
			$config->set("server-port", $port);
			echo "[*] RAM是PocketMine-MP可用的最大内存. 推荐范围: 128-256 MB\n";
			echo "[?] 分配给服务器的内存(MB):  (".self::DEFAULT_MEMORY."): ";
			$config->set("memory-limit", ((int) $this->getInput(self::DEFAULT_MEMORY))."M");
			echo "[*] 选择模式: (1)生存模式 或 (2)创造模式\n";
			do{
				echo "[?] 默认游戏模式: (".self::DEFAULT_GAMEMODE."): ";
				$gamemode = (int) $this->getInput(self::DEFAULT_GAMEMODE);
			}while($gamemode < 0 or $gamemode > 3);
			$config->set("gamemode", $gamemode);
			echo "[?] 最大在线人数 (".self::DEFAULT_PLAYERS."): ";
			$config->set("max-players", (int) $this->getInput(self::DEFAULT_PLAYERS));
			echo "[*] 出生点保护可以在出生点范围内保护所有方块不被摆放/破坏.\n";
			echo "[?] 启用出生点保护? (Y/n): ";
			if(strtolower($this->getInput("y")) == "n"){
				$config->set("spawn-protection", -1);
			}else{
				$config->set("spawn-protection", 16);
			}
			$config->save();
		}
		
		private function generateUserFiles(){
			echo "[*] 白名单可以只允许在其列表内的玩家加入. \n";
			echo "[?] 您想启用白名单吗?  (y/N): ";
			$config = new Config("./server.properties", CONFIG_PROPERTIES);
			if(strtolower($this->getInput("n")) === "y"){
				echo "[!] 你可以用\"/whitelist add <用户名>\"把别人加入白名单. \n";
				$config->set("white-list", true);
			}else{
				$config->set("white-list", false);
			}
			$config->save();
		}
		
		private function networkFunctions(){
			$config = new Config("./server.properties", CONFIG_PROPERTIES);
			echo "[!] 请求是一个用于不同的程序的协议用来获取您服务器数据和登录的玩家. \n";
			echo "[!] 如果您禁止了它, 您将不能使用服务器列表. \n";
			echo "[?] 您希望禁用Query请求吗?  (y/N): ";
			if(strtolower($this->getInput("n")) === "y"){
				$config->set("enable-query", false);
			}else{
				$config->set("enable-query", true);
			}
			

			echo "[*] 匿名数据让我们可以获得全球的PocketMine-MP和它的插件的统计信息. 您可以在 stats.pocketmine.net 查看统计信息. \n";
			echo "[?] 您希望禁用匿名数据吗?  (y/N): ";
			if(strtolower($this->getInput("n")) === "y"){
				$config->set("send-usage", false);
			}else{
				$config->set("send-usage", true);
			}
			$config->save();
			
			
			echo "[*] 获得你的外部IP和内部IP\n";
			
			$externalIP = Utils::getIP();
			$internalIP = gethostbyname(trim(`hostname`));
			
			echo "[!] 您的外部IP是 $externalIP. 您可能需要端口转发到您的内网IP $internalIP .\n";
			echo "[!] 请确认您检查了它, 如果您直接进入下一步并跳过这一步, 没有外部的玩家可以加入. [按\"回车\"键]";
			$this->getInput();
		}
		
		private function endWizard(){
			echo "[*] 您已经成功完成了服务器设置向导. 提示：更改server.properties以获得更多配置.\n";
			echo "[*] 请查看插件源来添加新的功能, 迷你游戏或者对服务器的高级保护. \n";
			echo "[*] PocketMine-MP现在开始运行. 输入 \"/help\" 来看所有可用的命令. \n\n\n";
			sleep(4);
		}
		
		private function getInput($default = ""){
			$input = trim(fgets(STDIN));
			return $input === "" ? $default:$input;
		}


	}
?>