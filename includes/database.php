<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/5/2018
 * Time: 6:11 PM
 */
require_once("initialize.php");
require_once(LIB_PATH.DS."config.php");

class MySQLDatabase{
    private $connection; //stores the mysqli_connection variable
    private $real_escape_string_exists;
    private $magic_quotes_active; //checks to see if the get_magic_quotes() is active current php version
    public $last_query; //gets & stores the last recorded 'mysqli_query'

    /**
     * set the @class : __construct() method here
    */
    public function __construct(){
        $this->open_connection();
        $this->real_escape_string_exists = function_exists("mysqli_real_escape_string");
        $this->magic_quotes_active = get_magic_quotes_gpc();
    }

    public function __clone(){
        // TODO: Implement __clone() method.
        $this->open_connection();
        $this->real_escape_string_exists = function_exists("mysqli_real_escape_string");
        $this->magic_quotes_active = get_magic_quotes_gpc();
    }

    public function open_connection(){
        $this->connection = mysqli_connect(DB_SERVER, DB_USER,DB_PASS, DB_NAME);

        if(!$this->connection){ die("Database Connection Failed: ".mysqli_error($this->connection)); }
    }

    public function close_connection(){
        if(isset($this->connection)){
            mysqli_close($this->connection);
            unset($this->connection);
        }
    }

    public function query($sql){
        $this->last_query = $sql;
        $result = mysqli_query($this->connection, $sql);
        $this->confirm_query($result);
        return $result;
    }

    public function fetch_array($result_set){
        return mysqli_fetch_assoc($result_set);
    }

    public function num_rows($result_set){
        return mysqli_num_rows($result_set);
    }

    public function insert_id(){
        //get the latest id inserted over the current db connection
        return mysqli_insert_id($this->connection);
    }

    public function affected_rows(){
        return mysqli_affected_rows($this->connection);
    }

    private function confirm_query($result){
        if(!$result) {
            $output = "<p style='font-family: corbel;'><b>Database query failed: </b>".mysqli_error($this->connection) ."</p>";
            $output .= "<p style='font-family: corbel;'><b>Last query string was: </b>".$this->last_query ."</p>";
            die($output);
        }
    }

    public function escape_value($value){

        if($this->real_escape_string_exists){//php version 4.3.0 or higher
            //undo any magic_quotes effect, so mysqli_real_escape_string() can work

            if($this->magic_quotes_active){ $value = stripcslashes($value); }
            $value = mysqli_real_escape_string($this->connection, $value);
        }
        else{//before php version 4.3.0

            if(!$this->magic_quotes_active){ addslashes($value); }
            //if magic_quotes are active, then the slashes already exist
        }
        return (String)$value;
    }

}

$database = new MySQLDatabase();
$db = &$database;