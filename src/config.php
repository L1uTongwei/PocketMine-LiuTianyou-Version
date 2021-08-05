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

set_time_limit(0);

date_default_timezone_set('PRC');

gc_enable();
error_reporting(E_ALL | E_STRICT);
ini_set("allow_url_fopen", 1);
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
ini_set("default_charset", "utf-8");
if(defined("POCKETMINE_COMPILE") and POCKETMINE_COMPILE === true){
	define("FILE_PATH", realpath(dirname(__FILE__))."/");
}else{
	define("FILE_PATH", realpath(dirname(__FILE__)."/../")."/");
}
set_include_path(get_include_path() . PATH_SEPARATOR . FILE_PATH);

ini_set("memory_limit", "128M"); //Default
define("LOG", true);
define("START_TIME", microtime(true));
define("MAJOR_VERSION", "Alpha_1.3.12 (Hack Version 1.2)"); //1.x表示汉化完全
define("CODENAME", "戈登·弗里曼（Gordon Freeman）");
define("CURRENT_MINECRAFT_VERSION", "v0.8.1 alpha");
define("CURRENT_API_VERSION", 12);
define("CURRENT_PHP_VERSION", "5.5");
$gitsha1 = false;
if(file_exists(FILE_PATH.".git/refs/heads/master")){ //Found Git information!
	define("GIT_COMMIT", strtolower(trim(file_get_contents(FILE_PATH.".git/refs/heads/master"))));
}else{ //Unknown :(
	define("GIT_COMMIT", str_repeat("00", 20));
}
