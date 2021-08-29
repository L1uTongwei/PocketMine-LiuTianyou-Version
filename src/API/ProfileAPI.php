<?php
$GLOBALS['__AuthAPI'] = "defined";
class AuthAPI extends SQLite3{
    public $server;
    function __construct(){
        $this->server = ServerAPI::request();
        $this->open("./profile.db");
        
        @$this->query("CREATE TABLE profiles (
            `caseusername` varchar(20) PRIMARY KEY NOT NULL,
            `position` varchar(50) DEFAULT NULL,
            `spawn` varchar(50) DEFAULT NULL,
            `inventory` varchar(1024) DEFAULT NULL,
            `armor` varchar(512) DEFAULT NULL,
            `gamemode` tinyint DEFAULT NULL,
            `health` tinyint DEFAULT NULL,
            `lastIP` varchar(20) DEFAULT NULL,
            `lastID` varchar(50) DEFAULT NULL,
            `hotbar` varchar(512) DEFAULT NULL
            )
        ");
    }
    public function user_exists($username){
        $username = strtolower($username);
        @$result = $this->query("SELECT * FROM profiles WHERE caseusername='$username'");
        if($result == false or $result->numColumns() == 0 or $result->fetchArray(SQLITE3_ASSOC)['caseusername'] == NULL){
            return false;
        }
        return true;
    }
    public function init_user($username, $level, $gamemode, $ip, $id){
        $username = strtolower($username);
        $pos = json_encode(new Position(0, 0, 0, $level));
        if($this->user_exists($username, "caseusername") == false){
            $this->query("INSERT INTO profiles VALUES ('$username', '$pos', '$pos', '[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]]', '[[0,0],[0,0],[0,0],[0,0]]', $gamemode, 20, '$ip', '$id', '[]', '[0,0,0,0,0,0,0,0,0]')");
        }
    }
    public function set($username, $field, $value, $no_decode = false){
        $username = strtolower($username);
        if(is_array($value) and !$no_decode){
            $value = json_encode($value);
        }
        if(is_int($value)) $this->query("UPDATE profiles SET '$field'=$value WHERE caseusername='$username'");
        else $this->query("UPDATE profiles SET '$field'='$value' WHERE caseusername='$username'");
        return true;
    }
    public function get($username, $field){
        $username = strtolower($username);
        $result = $this->query("SELECT * FROM profiles WHERE caseusername='$username'");
        if($result == false){
            return false;
        }
        if($field == "position" or $field == "spawn" or $field == "inventory" or $field == "hotbar" or $field == "armor"){
            return json_decode($result->fetchArray(SQLITE3_ASSOC)[$field], true);
        }
        return $result->fetchArray(SQLITE3_ASSOC)[$field];
    }
    public function getAll($username){
        $username = strtolower($username);
        $result = $this->query("SELECT * FROM profiles WHERE caseusername='$username'");
        if($result == false){
            return false;
        }
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row;
    }
    public function exists($username, $field){
        $username = strtolower($username);
        $result = $this->query("SELECT * FROM profiles WHERE '$field' is NULL OR '$field' is '[-1,-1,-1,-1,-1,-1,-1,-1,-1]'
        OR '$field' is '[[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]]'
        OR '$field' is '[[0,0],[0,0],[0,0],[0,0]]'");
        if($result == false){
            return false;
        }
        return false;
    }
    function __destruct(){
        $this->close();
    }
}