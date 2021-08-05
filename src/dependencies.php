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
$GLOBALS['__dependencies'] = "defined";

/***REM_START***/
__require_once("/src/config.php");
__require_once("/src/utils/TextFormat.php");
__require_once("/src/functions.php");
/***REM_END***/
define("DATA_PATH", realpath(arg("data-path", FILE_PATH))."/");

if(arg("enable-ansi", strpos(strtoupper(php_uname("s")), "WIN") === 0 ? false:true) === true and arg("disable-ansi", false) !== true){
	define("ENABLE_ANSI", true);
}else{
	define("ENABLE_ANSI", false);
}

set_error_handler("error_handler", E_ALL);

$errors = 0;

if(version_compare("5.4.0", PHP_VERSION) > 0){
	console("[ERROR] PHP 版本 >= 5.4.0", true, true, 0);
	++$errors;
}

if(php_sapi_name() !== "cli"){
	console("[ERROR] 你必须在命令行内使用PocketMine-MP。", true, true, 0);
	++$errors;
}

if(!extension_loaded("sockets") and @dl((PHP_SHLIB_SUFFIX === "dll" ? "php_":"") . "sockets." . PHP_SHLIB_SUFFIX) === false){
	console("[ERROR] 找不到socket扩展。", true, true, 0);
	++$errors;
}

if(!extension_loaded("pthreads") and @dl((PHP_SHLIB_SUFFIX === "dll" ? "php_":"") . "pthreads." . PHP_SHLIB_SUFFIX) === false){
	console("[ERROR] 找不到pthreads扩展。", true, true, 0);
	++$errors;
}else{
	$pthreads_version = phpversion("pthreads");
	if(substr_count($pthreads_version, ".") < 2){
		$pthreads_version = "0.$pthreads_version";
	}
	if(version_compare($pthreads_version, "0.1.0") < 0){
		console("[ERROR] pthreads 版本必须 >= 0.1.0，但你的版本是 $pthreads_version.", true, true, 0);
		++$errors;
	}	
}

if(!extension_loaded("curl") and @dl((PHP_SHLIB_SUFFIX === "dll" ? "php_":"") . "curl." . PHP_SHLIB_SUFFIX) === false){
	console("[ERROR] 找不到curl扩展。", true, true, 0);
	++$errors;
}

if(!extension_loaded("sqlite3") and @dl((PHP_SHLIB_SUFFIX === "dll" ? "php_":"") . "sqlite3." . PHP_SHLIB_SUFFIX) === false){
	console("[ERROR] 找不到sqlite3扩展。", true, true, 0);
	++$errors;
}

if(!extension_loaded("yaml") and @dl((PHP_SHLIB_SUFFIX === "dll" ? "php_":"") . "yaml." . PHP_SHLIB_SUFFIX) === false){
	console("[ERROR] 找不到yaml扩展。", true, true, 0);
	++$errors;
}

if(!extension_loaded("zlib") and @dl((PHP_SHLIB_SUFFIX === "dll" ? "php_":"") . "zlib." . PHP_SHLIB_SUFFIX) === false){
	console("[ERROR] 找不到zlib扩展。", true, true, 0);
	++$errors;
}

if($errors > 0){
	console("[ERROR] 请前往pecl.php.net安装扩展，或重新编译PHP（需要线程安全版本）。", true, true, 0);
	exit(1); //Exit with error
}

$sha1sum = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
/***REM_START***/
__require_once("/src/math/Vector3.php");
__require_once("/src/world/Position.php");
__require_once("/src/pmf/PMF.php");

require_all(FILE_PATH . "src/");

$inc = get_included_files();
$inc[] = array_shift($inc);
$srcdir = realpath(FILE_PATH."src/");
foreach($inc as $s){
	if(strpos(realpath(dirname($s)), $srcdir) === false and strtolower(basename($s)) !== "pocketmine-mp.php"){
		continue;
	}
	$sha1sum ^= sha1_file($s, true);
}
/***REM_END***/
define("SOURCE_SHA1SUM", bin2hex($sha1sum));

/***REM_START***/
if(!file_exists(DATA_PATH."server.properties") and arg("no-wizard", false) != true){
	$installer = new Installer();
}
/***REM_END***/