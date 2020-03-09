<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/8/2018
 * Time: 10:51 PM
 */
include("../includes/initialize.php");

if($_SERVER['REQUEST_METHOD']=="POST"){
   $file = $_FILES['files'];

   print_r($_FILES['file_2']);
   die;
   //initialize the @class FileUpload
    $fileUpload = new FileUploads(1200,"../images");
    //check to see if the credentials
    if($fileUpload->validateMultipleFiles($file)){
        //returns true
        //upload the files to the necessary __DIR__
        $fileUpload->uploadMultipleFiles(true, 500, 450);

        //get all files the user uploaded in the __toString() method "& escape them for sql
        $database->escape_value($fileUpload->__toString());

        //get the currently logged in user
        $uploaded_by = $session->user_id;
        $date_of_entry = date("D-m-d, Y");

        $rs = $database->query("INSERT INTO images(uploaded_by, img_uploaded, date_of_entry)VALUES('{$session->user_id}', '{$fileUpload->__toString()}', '{$date_of_entry}')");

        if($rs){
            echo "yes images added";
        }
    }
    else $fileUpload->validateMultipleFiles($file);
//
////    echo "SIZE: ".$file['size'] / 1024 ."kb";
////
//    $file_obj = new FileUploads(200,"../images");
////    $file_obj->validateSingleFiles($file);
////
////    if($file_obj->isFileValid){
////        echo "yes";
////    }
////    else{
////        echo "no";
////    }
////
////    echo @$file_obj->uploadSingleFile(true, 120, 100)[0];
//
//     @$file_obj->validateMultipleFiles($file);
//
//     $multiple = $file_obj->uploadMultipleFiles(true, 120, 100);
//    $all_multiple_img = "";
//    foreach ($multiple as $key=>$value){
//        //echo "<p>$items</p>";
//        $all_multiple_img .=($key > 0)? ", $value" : $value;
//    }
//
//    print_r($multiple);
//    //echo $all_multiple_img;
//
//    echo $file_obj->__toString();
//
//    //echo ($file_obj->is_file_here) ? "file exists": "file does not exist";
//
//    echo "<hr>".$file_obj2 = clone $file_obj;
}