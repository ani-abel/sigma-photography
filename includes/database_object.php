<?php

/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/6/2018
 * Time: 4:39 PM
 */
require_once("initialize.php");
require_once(LIB_PATH.DS."database.php");

class DatabaseObject
{
    protected static $table_name = "users";

    //common database methods
    public static function find_all(){
        $result_set = static::find_by_sql("SELECT * FROM ".static::$table_name);
        return $result_set;
    }

    public static function find_by_id($id = 0){

        $result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE id='{$id}' LIMIT 1");
        return !(empty($result_array)) ? array_shift($result_array) : false;
    }

    public static function find_by_sql($sql=""){
        global $database;
        $result_set= $database->query($sql);
        $object_array = array();

        while($row = $database->fetch_array($result_set)){
            $object_array[] = static::instantiate($row);
        }

        return $object_array;
    }

    private static function instantiate(Array $record){
        //$class_name= get_called_class(); //this is used in conjunction with late static binding to get the called class
        $object = new static();
        /**simple but long-form approach*/
//        $object->id = $record['id'];
//        $object->username = $record['username'];
//        $object->password = $record['password'];
//        $object->first_name = $record['first_name'];
//        $object->last_name = $record['last_name'];

        foreach ($record as $attribute=>$value){
            if($object->has_attribute($attribute)){
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    private function has_attribute($attribute){
        /**
         * get_object_vars(): returns an 'assoc' array with all attributes,
         * including(private ones) as the keys & their current value as the value
         **/
        $object_vars = get_object_vars($this);
        //we don't care about the value, we just want to know if the array_key_exists()
        //will return 'true' or 'false'
        return array_key_exists($attribute, $object_vars);
    }
}

$dbO = new DatabaseObject();