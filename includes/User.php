<?php

/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/5/2018
 * Time: 8:05 PM
 */
require_once("initialize.php");
require_once(LIB_PATH.DS."database.php");

class User extends databaseobject
{
    public $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;

    public static function authenticate($username="", $password=""){
        global $database;
        $database->escape_value($username);
        $database->escape_value($password);

        $sql = "SELECT * FROM ".self::$table_name." WHERE username='{$username}' AND password='{$password}' LIMIT 1";
        $result_array = self::find_by_sql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public function full_name(){
        return (isset($this->first_name, $this->last_name)) ? $this->first_name ." ". $this->last_name :  "";
    }


}