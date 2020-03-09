<?php

/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/8/2018
 * Time: 11:30 PM
 */
require_once("initialize.php");
/**
 * @TODO: ALWAYS REMEMBER TO LOAD PHP_IMAGE_MAGIC. THIS @CLASS: FileUploads ALWAYS NEEDS THIS LIBRARY
 */

class FileUploads
{
    public static $__init_file_size = 0;
    public $isFileValid = false;
    public $is_file_here= false;
    private $path;
    private $__single_file = [];
    private $__multiple_files = [];
    private $__all_file_paths = [];

    /**
     * @param $__init_file_size => THE MAX-SIZE OF THE FILES IN KILO-BYTES
     * @param $path => THE __DIR__ WHERE THE FILES SHOULD BE SAVED
    */
    public function __construct($__init_file_size=0, $path=""){
        self::$__init_file_size = $__init_file_size;
        $this->path = rtrim(preg_replace("/\/$/i", " ", $path));
    }

    public function validateSingleFiles(Array $file){
        if($file["error"] == 0){
            $file_size = $file["size"];

            if($file_size /1024 > self::$__init_file_size){
                $this->isFileValid = false;
                die(output_err_message("ERROR: FILE IS LARGER THAN ".self::$__init_file_size."kb"));
            }
            else{
                if(preg_match("/(jpe?g|png|gif)$/i", $file["type"])) {
                    $this->__single_file = $file;
                    $this->isFileValid = true;
                }
                else{
                    $this->isFileValid = false;
                    die(output_err_message("ERROR: FILE DOES NOT MATCH THE VALID FILE TYPES(jpg,jpeg,png,gif)"));
                }
            }
        }
        return $this->isFileValid;
    }

    public function uploadSingleFile($copies=false, $thumbnail_width, $thumbnail_height){
        $file_name = $this->__single_file["name"];
        $file_type = $this->__single_file["type"];
        $tmp_path = $this->__single_file["tmp_name"];

        $file_paths = [];

        if($this->isFileValid){
            //SPLIT THE FILE_TYPE IN AN ARRAY TO GET THE FILE EXTENSION
            $split_f_type = explode("/", $file_type);

            $new_f_name = uniqid(32).".".strtolower(end($split_f_type));
            if(file_exists($this->path.$file_name)){ $this->is_file_here = true; }

            else{
                $this->is_file_here = false;
                $file_path = $this->path."/".basename($new_f_name);
                move_uploaded_file($tmp_path, $file_path);

                if(!in_array($file_path, $file_paths)){
                    array_push($file_paths, $file_path);
                    array_push($this->__all_file_paths, $file_path);
                }
                else{
                    array_push($file_paths, null);
                    array_push($this->__all_file_paths, null);
                }

                if( (isset($thumbnail_width, $thumbnail_height) && ($thumbnail_height>0 && $thumbnail_width>0)) ){
                    $magicObj = new imageLib($file_path);
                    $magicObj->resizeImage($thumbnail_width, $thumbnail_height);
                    $magicObj->saveImage($file_path, 100);

                    if($copies){
                        $magicObj2 = clone $magicObj;
                        $magicObj2->resizeImage($thumbnail_width-($thumbnail_width/2), $thumbnail_height-($thumbnail_height/2));
                        $copy_path = $this->path."/copies/".basename($file_path);
                        $magicObj2->saveImage($copy_path);

                        if(!in_array($copy_path, $file_paths)){
                            array_push($file_paths, $copy_path);
                            array_push($this->__all_file_paths, $copy_path);
                        }
                        else{
                            array_push($file_paths, null);
                            array_push($this->__all_file_paths, null);
                        }
                    }
                }
            }
        }
        return $file_paths;
    }

    public function validateMultipleFiles(Array $file){
        foreach ($file["name"] as $key=>$value){
            if($file["error"][$key]==0){
                $file_size = $file["size"][$key];

                if($file_size /1024 > self::$__init_file_size) {
                    $this->isFileValid = false;
                    $key = ++$key;
                    die(output_err_message("ERROR: FILE ".$key.".'".$file["name"][$key]."' EXCEEDS ".self::$__init_file_size ."KB"));
                    break;
                }
                else{
                    if(preg_match("/(jpe?g|png|gif)$/i", $file["type"][$key])) {
                        $this->__multiple_files = $file;
                        $this->isFileValid = true;
                    }
                    else {
                            $this->isFileValid = false;
                            $key = ++$key;
                            die(output_err_message("ERROR: FILE ".$key.".'".$file["name"][$key]."' does not match the valid file types(jpg,jpeg,png,gif)"));
                            break;
                        }
                }
            }
        }
        return $this->isFileValid;
    }

    public function uploadMultipleFiles($copies=false, $thumbnail_width, $thumbnail_height){
        $file_paths = [];

        if($this->isFileValid) {
            foreach ($this->__multiple_files["name"] as $key => $value) {
                $file_type = $this->__multiple_files["type"][$key];
                $tmp_path = $this->__multiple_files["tmp_name"][$key];

                //SPLIT THE FILE_TYPE IN AN ARRAY TO GET THE FILE EXTENSION
                $split_f_type = explode("/", $file_type);

                $new_f_name = uniqid(32) . "." . strtolower(end($split_f_type));

                //check if the file already exists in the image_uploads __DIR__
                if (file_exists($this->path."/".$this->__multiple_files["name"][$key]) || file_exists($this->path."/copies/".$this->__multiple_files["name"][$key])) $this->is_file_here = true;

                else {
                    $this->is_file_here = false;
                    $file_path = $this->path . "/" . basename($new_f_name);
                    move_uploaded_file($tmp_path, $file_path);

                    $magic_obj = new imageLib($file_path);
                    $magic_obj->resizeImage($thumbnail_width, $thumbnail_height);
                    $magic_obj->saveImage($file_path, 100);

                    if(!in_array($file_path, $file_paths)){
                        array_push($file_paths, $file_path);
                        array_push($this->__all_file_paths, $file_path);
                    }
                    else{
                        array_push($file_paths, null);
                        array_push($this->__all_file_paths, null);
                    }

                    //work on a copy of the image, to be stored in an inner __DIR__ called "/copy/"
                    if ($copies) {
                        $magic_obj2 = clone $magic_obj;
                        $magic_obj2->resizeImage($thumbnail_width - ($thumbnail_width / 2), $thumbnail_height - ($thumbnail_height / 2));
                        $copy_path = $this->path . "/copies/" . basename($file_path);
                        $magic_obj2->saveImage($copy_path, 100);

                        if(!in_array($copy_path, $file_paths)){
                            array_push($file_paths, $copy_path);
                            array_push($this->__all_file_paths, $copy_path);
                        }
                        else{
                            array_push($file_paths, null);
                            array_push($this->__all_file_paths, null);
                        }
                    }
                }
            }
        }
        return $file_paths;
    }

    public function __toString() {
        // TODO: Implement __toString() method.
        return (!$this->__all_file_paths == null ) ? (String)implode(", ", $this->__all_file_paths) : "";
    }
} //END OF CLASS: FileUploads


$file_obj = new FileUploads();