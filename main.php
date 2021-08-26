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

//定义自己的__require_once以加快速度
function __require_once($filename, $mode = false){
    $macro = "__".basename($filename, ".php");
    if(!isset($GLOBALS[$macro])){
        if($mode == false) $filename = __DIR__ . $filename;
        echo "Loaded header file $filename... \n";flush();
        require($filename);
    }
}

/***REM_START***/
__require_once("/src/config.php");

__require_once("/src/functions.php");
__require_once("/src/dependencies.php");
/***REM_END***/

$server = new ServerAPI();

$GLOBALS['UserDatabase'] = new AuthAPI; //只有一个开启的数据库，避免数据不同步

$server->start();

kill(getmypid()); //Fix for ConsoleAPI being blocked
exit(0);
?>