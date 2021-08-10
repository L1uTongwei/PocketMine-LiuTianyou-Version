<?php
$GLOBALS['__AuthAPI'] = "defined";
class AuthAPI extends SQLite3{
    function __construct(){
        $this->open("./password.db");
        @$this->query("CREATE TABLE users (
            `username` VARCHAR(20) PRIMARY KEY NOT NULL,
            `password` VARCHAR(200) NOT NULL,
            `salt` VARCHAR(100) NOT NULL
            )
        ");

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
            `achievements` varchar(512) DEFAULT NULL,
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
    private function get_column($username){
        $username = strtolower($username);
        $result = $this->query("SELECT * FROM users WHERE caseusername='$username'");
        if($result->numColumns() != false){
            return $result->fetchArray();
        }
        return false;
    }
    public function exists($username, $field){
        $username = strtolower($username);
        $result = $this->query("SELECT * FROM profiles WHERE '$field' is NULL");
        if($result == false){
            return false;
        }
        return false;
    }
    private static function parse_password($password, $salt){
        return md5($password.$salt);
    }
    public function check_password($username, $password){
        $username = strtolower($username);
        if(($row = $this->get_column($username)) == false){
            return false;
        }
        $salt = $this->get_column($username)['salt'];
        $Password = $this->get_column($username)['password'];
        return $this->parse_password($password, $salt) == $Password;
    }
    public function newUser($username, $password){
        $username = strtolower($username);
        if($this->get_column($username) != false){
            return false;
        }
        $salt = random_bytes(64);
        $pass = $this->parse_password($password, $salt);
        $this->query("INSERT INTO users VALUES (\"$username\", \"$pass\", \"$salt\")");
        return true;
    }
    public function RemoveUser($username){
        $username = strtolower($username);
        if($this->get_column($username) != false){
            return false;
        }
        $this->query("DELETE FROM users WHERE username='$username'");
        return true;
    }
    function __destruct(){
        $this->close();
    }
}