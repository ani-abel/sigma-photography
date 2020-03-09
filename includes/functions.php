<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/5/2018
 * Time: 3:34 PM
 */

//require("../Lib/API/class.phpmailer.php");
//require ("../Lib/API/php-image-magician/php_image_magician.php");
//require("../API/Facebook/autoload.php");
include("initialize.php");
/**
 * function for uploading images to DB
 * @param $image
 * @return String
 */
function imageFilePath($image)
{
    if($image ['error'] == UPLOAD_ERR_OK)
    {
        $err = 0;

        #get image properties...
        $image_name = $image ['name'];
        $image_size = $image ['size'];
        $temp_path = $image ['tmp_name'];
        $image_type = $image ['type'];

        $image_name_err = 0;
        $image_size_err = 0;
        $image_type_err = 0;
        $image_ext_err = 0;

        #check if file size less than 2mb
        if($image_size > 2048576 || $image ['error'] == UPLOAD_ERR_INI_SIZE)
        {
            $err = 1;
            $image_size_err = 1;
        }
        else
        {

            #restrict user form entering file that isn't an image
            $extension = array('jpeg', 'png', 'gif', 'jpg');
            $image_ext = explode("/", $image_type);
            $image_ext = strtolower(end($image_ext));

            #check if the extension is in the array list
            if(!in_array($image_ext, $extension))
            {
                $err = 1;
                $image_ext_err = 1;
            }
            $image_path = '../images/site_images/'. basename($image_name);
            $move_image = move_uploaded_file($temp_path, $image_path);
            if($move_image)
            {
                $move_image_err = 0;
            }
        }
    }
    return $image_path;
}


function cleanInput($input)
{
    $search = array(
        '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );

    $output = preg_replace($search, '', $input);
    return $output;
}

//function to sanitize or 'clean' data from the input fields
function sanitize($input)
{
    $host = "localhost";
    $uName  = "root";
    $uPass = "";
    $dbName = "kollabo.com";
    $output ="";
    $con = mysqli_connect($host, $uName, $uPass, $dbName);
    if (is_array($input))
    {
        foreach($input as $var=>$val)
        {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc())
        {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $output = mysqli_real_escape_string($con, $input);
    }
    return $output;
}


$host = "localhost";
$uName  = "root";
$uPass = "";
$dbName = "kollabo.com";
$con = mysqli_connect($host, $uName, $uPass, $dbName);

//set 'default timezone identifier' = "Africa/Lagos"
date_default_timezone_set('Africa/Lagos');


//function to get the end of the image path-> to be used in the img->'alt' attribute
function getImageName($image_path)
{
    $image_array = explode('/', $image_path);
    $image_name = end($image_array);
    return $image_name;
}

//function for default image path -> if an image is not selected by the user
function defaultImagePath(){
    return "../images/site_images/person_icon.png";
}

/*function to remove the first letter of a phone number and add a +234 area code prefix
basic logic -> get the first position
character which is expected to be no. '0' & replace with a +234 extension*/
function areaCode($phoneNo, $areaCode){
    $refined_phoneNo = "";
    if(is_string($phoneNo))    {
        if(preg_match('/^0\d/',$phoneNo)) {
            if(strlen($phoneNo) > 11) {
                $refined_phoneNo = error("All phone numbers must not be more 11 characters");
            }
            else {
                $phoneNo = ltrim($phoneNo,'0');
                $refined_phoneNo = $areaCode.$phoneNo;
            }
        } else
            $refined_phoneNo = $areaCode.$phoneNo;
    }
    else {
        $refined_phoneNo = error("Invalid phone number.");
    }
    return $refined_phoneNo;
}

//function to sanitize a name field in php removing special characters
function cleanName($nameField){
    if(is_string($nameField)){
        $specialChars = array('@','<','$','%','^','&','*','>','?');

        for($i = 0; $i < count($specialChars); $i++)
        {
            $nameField = str_replace($specialChars[$i],'',$nameField);
        }
    }
    return $nameField;
}

/*function to display a error msg in well designed manner*/
function error($msg){
    return "<div style='background:red; padding:10px; color:#fff; width: 100%;font-family: corbel;font-weight: bold;' class='tt'>".$msg."</div>";
}

/*function to display a success msg in well designed manner*/
function success($msg){
    return "<div style='background:teal; padding:10px; color:#fff; width: 100%;font-family: corbel;font-weight: bold;'>".$msg."</div>";
}

function clean_url($string)
{
    strtr($string, array(
            ' ' => '.')
    );
}

function showBadChars($nameField){
    $bad_values = '/[^a-z0-9 _]+$/i';
    if(preg_match($bad_values,$nameField)) {
        $err_msg = "Special characters are not allowed, use 0-9, or a-z";
        return error($err_msg);
    }
    else
        return true;
}


/*logIn function using cookies for getting number of tries;
this function return 3 types of values:
- true -> if account details check out,
- string "show hint"-> if the no. of false tries reaches 2 and
- false -> if the no. of tries exceeds 3
*/
function logIn($dbPass,$dbUname,$successUrl,$failUrl,$inputPass,$inputUname){
    $passCheck = false;
    $urlHead = "Location: ";
    //dbPass,dbUname,passHint should come from the database
    if($inputPass == $dbPass && $inputUname == $dbUname)
    {
        $passCheck = true;
        $urlHead .= $successUrl;
        $urlHead = header($urlHead);
        if($passCheck == 1)
            return true;
    }
    else
    {
        //use a help-block to tell the use that they have only 3 tries left
        if(isset($_COOKIE['count_tries']))
        {
            if($_COOKIE['count_tries'] < 3) {
                $attempts = $_COOKIE['count_tries'] + 1;
                setcookie('count_tries', $attempts, time()+60*3); //set the cookie for 3 minutes with the number of attempts stored
                if($_COOKIE['count_tries'] == 2) {
                    /*if logIn returns string->"error"
                    - use a condition to set a div which carries the password hint
                      in the external file
                    */
                    return "show hint";
                }
            }
            else {
                //echo 'You are banned for 3 minutes. Try again later<br>';
                $urlHead .= $failUrl;
                $urlHead = header($urlHead);
                return false;
            }
        }
        else {
            //set the cookie for 3 minutes with the initial value of 1
            setcookie('count_tries', 1, time()+60*3);
        }
    }
}


/*normal password log-in without using cookies
 returns 1 of 2 values:
true-> if account details check out
false-> if account details do not check out
*/
function NormalLogIn($dbPass,$dbUname,$inputPass,$inputUname)
{
    //dbPass,dbUname,passHint should come from the database
    if ($inputPass == $dbPass && $inputUname == $dbUname)
    {
        return true;
    }
    else
    {
        return (boolean)false;
    }
}


/**
 * ======================================================================
 * Function to check the occurrences of a certain entry into the DB table
 * ======================================================================
 * The function returns 'true' if the entry already exists, & 'false' if it does not exist.
 * =========================================================================================
 */

/**
 * @param $con
 * @return bool
 * @param $tableName
 * @param $whereColumn
 * @param $checkVar
 */
function checkOccurrences($con, $tableName, $whereColumn, $checkVar) {
    $check = false;
    $query = "SELECT * FROM $tableName WHERE $whereColumn = '{$checkVar}'";
    $rs = mysqli_query($con, $query);
    if($rs) {
        $occurrences = mysqli_num_rows($rs);
        if($occurrences >= 1) {
            $check = true;
        }
        else {
            $check = false;
        }
    }
    return (boolean)$check;
}

/**
 * ====================================================================================================
 * Function counts the number of entries that meet a certain condition using sql COUNT() agg. function
 * ====================================================================================================
 */

/**
 * @param $con
 * @return int
 * @param $columnToBeCounted
 * @param $tableName
 * @param $whereColumn
 * @param $checkVar
 */
function countSqlNo($con, $tableName, $columnToBeCounted, $whereColumn, $checkVar)
{
    $count_rs = 0;
    $query = "SELECT COUNT($columnToBeCounted) AS no_of_entries FROM $tableName WHERE $whereColumn = '{$checkVar}'";
    $query_rs = mysqli_query($con, $query);
    if($query_rs) {
        $row = mysqli_fetch_assoc($query_rs);
        $count_rs = $row['no_of_entries'];
    }
    return $count_rs;
}


/**
 * Function to upload the staff-images
 * ===================================
 */

/**
 * @return string
 * @param $image
 * @param $thumbnail_width
 * @param $thumbnail_height
 */
function staffImagePath($image, $thumbnail_width, $thumbnail_height) {
    $img_path = "";
    if(isset($thumbnail_width) && isset($thumbnail_height)) {
        intval($thumbnail_height); //convert $thumbnail_height to an integer
        intval($thumbnail_width); //convert $thumbnail_width to an integer
    }
    if($image['error'] == 0) {
        $img_name = $image['name'];
        $img_type = $image['type'];
        $img_size = $image['size'];
        $temp = $image['tmp_name'];
        $img_size_err = 0;
        $img_ext_err = 0;
        $invalid_dataTypeErr = 0;
        if($img_size > 5000000 || $image['error'] == UPLOAD_ERR_INI_SIZE)
        {
            $img_size_err = 1;
        }
        $extensions = array("jpg", "jpeg", "png", "gif");
        $img_ext = explode("/", $img_type);
        $img_ext = strtolower(end($img_ext));
        if(!in_array($img_ext, $extensions)) {
            $img_ext_err = 1;
        }
        if($image['error'] == 0 && $img_ext_err != 1 && $img_size_err != 1) {
            $img_path = "../images/".basename($img_name);
            $move_img = move_uploaded_file($temp, $img_path);
        }
        if(isset($thumbnail_height) && isset($thumbnail_width)) {
            if(is_int($thumbnail_width) && is_int($thumbnail_height)) {
                //crop smaller images here
                $magicObj = new imageLib($img_path);
                $magicObj->resizeImage($thumbnail_width, $thumbnail_height);
                $magicObj->saveImage("../images/images_medium/".basename($img_path), 100);
                $img_path = "../images/images_medium/".basename($img_path);
            }
            else {
                $invalid_dataTypeErr = 1;
            }
        }
    }
    return $img_path;
}

/**
 *@param $image
 *@param $thumbnail_width
 *@param $thumbnail_height
 *@param $copies
 *@return array
 */
function uploadImage($image, $thumbnail_width, $thumbnail_height, $copies){
    $img_path = [];
    if($image['error'] == 0) {
        $img_name = $image['name'];
        $img_type = $image['type'];
        $img_size = $image['size'];
        $temp = $image['tmp_name'];
        $img_size_err = 0;

        if ($img_size > 5000000  || $image['error'] == UPLOAD_ERR_INI_SIZE) {
            $img_size_err = 1;
        }
        $extensions = array("jpg", "jpeg", "png", "gif");
        $img_ext = explode("/", $img_type);
        $img_ext = strtolower(end($img_ext));

        if (in_array($img_ext, $extensions)) {
            $img_path[0] = "../images/".basename($img_name);
            move_uploaded_file($temp, $img_path[0]); //$image moved successfully
        }

        if(isset($thumbnail_height) && isset($thumbnail_width)) {
            intval($thumbnail_width);
            intval($thumbnail_height);//convert $thumbnail_with & height to integers

            if ($thumbnail_height > 0 && $thumbnail_width > 0) {
                $magicImageObj = new imageLib($img_path[0]);
                $magicImageObj->resizeImage($thumbnail_width, $thumbnail_height);
                $magicImageObj->saveImage("../images/images_medium/copy1".basename($img_path[0]), 100);
                $img_path[1] = "../images/images_medium/copy1".basename($img_path[0]);

                if (isset($copies) && is_bool($copies)) {

                    if ($copies == true) {
                        $magicImageObj2 = new imageLib($img_path[0]);
                        $magicImageObj2->resizeImage($thumbnail_width-100, $thumbnail_height-80);
                        $magicImageObj2->saveImage("../images/images_small/copy2".basename($img_path[0]), 100);
                        $img_path[2] = "../images/images_small/copy2".basename($img_path[0]);
                    }
                }
            }
        }
    }
    return $img_path;
}

/**
 * Function to validate which language a particular piece code @param $codeString belongs to.
 * @param $codeString
 * @return string
 * NOTE: NEVER USE sanitize() ALONG WITH codeValidate().IT COMPROMISES THE STRING @param $codeString
 */
function codeValidate($codeString)
{
    $codeLang = "";
    $codeString = trim(ltrim($codeString));
    if(is_string($codeString))
    {//if-1
        if(preg_match("/^<!doctype html>|^<html>|&lt; ?&#33; ?doctype html ?&gt;|^ ?&lt;html|^ ?&lt;body/i", $codeString))
        {//if-2
            //check for <html> code
            $codeLang = "HTML";
        }
        else if(preg_match("/^ ?< ?style ?>|^ ?&lt; ?style ?&gt;|^\.?#?[a-z]:?[a-z]\{|^&#46;[a-z]+ ?&#123;|^&#35;[a-z]+ ?&#123;|^&#35;[a-z]+ ?&#58; ?[a-z]+ ?&#123;|^&#35;[a-z]+ ?&#123;|^&#46;[a-z]+ ?&#58; ?[a-z]+ ?&#123;/i", $codeString))
        {//else-if-1
            //check for <css>
            $codeLang = "CSS";
        }
        else if(preg_match("/^ ?< ?script ?>|^&lt;script&gt;|^ ?&lt; ?script ?&gt;|^ ?&lt;script src&#61;/i", $codeString))
        {//else-if-2
            //check for <JS>
            $codeLang = "JAVASCRIPT";
        }
        else if(preg_match("/^< ?\?php|^&lt; ?&#63; ?php|^\$_?[a-z]+|^&#36;&#95;[a-z]+|^&#36;[a-z]+|^&#36;&#95;[a-z]+|^&#36;[a-z]+|^&#36;&#95;[a-z]+|^&#36;[a-z]+/i", $codeString))
        {//else-if-3
            //check for <php>
            $codeLang = "PHP";
        }
        else if(preg_match("/^< ?\?xml  ?version=\\\"\d+\.\d+\\\" encoding=\\\"utf-\d+\\\" ?\? ?>|^< ?\?xml ?version='\'?\d+\.\d+'\'? encoding='\'?utf-\d+'\'? ?\? ?>|^&lt; ?xml  ?version ?&#61; ?&#34;\d+&#46;\d+ ?&#34;|^&lt; ?xml  ?version ?&#61; ?&#34;\d+&#46;\d+ ?&#34; encoding ?&#61; ?&#34;utf ?&lowbar; ?\d+ ?&#34; ?\? ?&gt;/i", $codeString))
        {//else-if-4
            //check for <xml>
            $codeLang = "XML";
        }
        else if(preg_match("/^#include ?<iostream>|^using namespace  ?std|^&#35;include ?&lt;iostream&gt;/i", $codeString))
        {//else-if-5
            //check for <c++>
            $codeLang = "C++";
        }
        else if(preg_match("/^#include ?<|^int main()|^&#35;include ?&lt;|^int main ?&#40;&#41;/i", $codeString))
        {//else-if-6
            //check for <c>
            $codeLang = "C";
        }
        else if(preg_match("/^using System;?|^namespace [a-z]+/i", $codeString))
        {//else-if-7
            //check for c#
            $codeLang = "C#";
        }
        else if(preg_match("/^import [a-z]+\.[a-z]+|^public static void main\(|^public class [a-zA-Z]+|^protected class [a-zA-Z]+|^private class [a-zA-Z]+/i", $codeString))
        {//else-if-8
            //check for java
            $codeLang = "JAVA";
        }
        else if( (preg_match("/^&#123;/i", $codeString) && preg_match("/&#125;$/i", $codeString)) )
        {//else-if-9
            //check for JSON
            $codeLang = "JSON";
        }
        else $codeLang = "undefined";
    }
    else $codeLang = "Wrong data type format";

    return $codeLang;
}

//function codeValidate2($codeString){
//    $code_lang = [];
//
//    return $code_lang;
//}

/**
 * Function to clean <code> of any language removing all special characters and replacing them with html entities code
 * ...returns the clean version of @param $codeString
 * & false if code sample is not html
 * @return string
 * @param $codeString
 */
function codeCleaner($codeString)
{
    $codeString = strtr(
        $codeString,
        array(
            "<" => "&lt;",
            ">" => "&gt;",
            "&" => "&amp;",
            "#" => "&#35;",
            "*" => "&#42;",
            "^" => "&#94;",
            "=" => "&#61;",
            "%" => "&#37;",
            "!" => "&#33;",
            "\"" => "&#34;",
            "(" => "&#40;",
            ")" => "&#41;",
            ":" => "&#58;",
            "?" => "&#63;",
            "@" => "&#64;",
            "]" => "&#93;",
            "[" => "&#91;",
            "_" => "&#95;",
            ";" => "&#59;",
            "/" => "&#92;",
            "|" => "&#124;",
            "," => "&#44;",
            "~" => "&#126;",
            "{" => "&#123;",
            "}" => "&#125;",
            "+" => "&#43;",
            "." => "&#46;",
            "$" => "&#36;",
            "'" => "&#39;",
            "-" => "&lowbar;"
        )
    );
    return $codeString;
}//end of function codeCleaner()


/**
 * @return String
 * @param $httpLink
 * Function to clean a URL, if URL has "www.url.com", it adds "http://" to its beginning
 * If URL has "url.com", it adds "http://www" to its beginning
 * Final result looks link this "https://www.url.com"
 */
function addHttpWwwToDL($httpLink) {
    //validate Download Link here
    $regEx1 = "/^www\.[a-z]+\.[a-z]+\/[a-z]+|^www\.[a-z]+\.[a-z]+\.[a-z]+\/[a-z]+|^www\.[a-z]+\.[a-z]\.[a-z]+\.[a-z]+\/[a-z]+|^www\.[a-z]+\.[a-z]\.[a-z]+\/[a-z]+/i"; //MATCHES www.facebook.com/profiles
    $regEx2 = "/^[a-z]+\.[a-z]+\/[a-z]+|^[a-z]+\.[a-z]+\.[a-z]+\/[a-z]+|^[a-z]+\.[a-z]\.[a-z]+\.[a-z]+\/[a-z]+|^[a-z]+\.[a-z]\.[a-z]+\/[a-z]+/i"; //MATCHES facebook.com/profiles
    $regEx3 = "/^https?:\/\/www\.[a-z]+\.[a-z]+\/[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]+\.[a-z]+\/[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]\.[a-z]+\/[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]\.[a-z]+\.[a-z]+\/[a-z]+/i";
    $newLink = "";
    if(is_string($httpLink)) {
        $httpLink = ltrim($httpLink);
        if (preg_match($regEx1, $httpLink)) {
            $newLink = "http://".$httpLink;
        } else if (preg_match($regEx2, $httpLink)) {
            $newLink = "http://www.".$httpLink;
        } else if (preg_match($regEx3, $httpLink)) {
            $newLink = $httpLink;
        }
    }
    return strtolower($newLink);
}//end of function "addHttpWwwToDL()"

function addHttpWwwToDL2($httpLink){
    //validate Download Link here
    $regEx1 = "/^www\.[a-z]+\.[a-z]+|^www\.[a-z]+\.[a-z]+\.[a-z]+|^www\.[a-z]+\.[a-z]+\.[a-z]+|^www\.[a-z]+\.[a-z]+\.[a-z]+\.[a-z]+/i"; //MATCHES www.facebook.com
    $regEx2 = "/^[a-z]+\.[a-z]+|^[a-z]+\.[a-z]+\.[a-z]+|^[a-z]+\.[a-z]+\.[a-z]+|^[a-z]+\.[a-z]+\.[a-z]+\.[a-z]+/i"; //MATCHES facebook.com
    $regEx3 = "/^https?:\/\/www\.[a-z]+\.[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]+\.[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]+\.[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]+\.[a-z]+\.[a-z]+/i";
    $newLink = "";
    if(is_string($httpLink)) {
        $httpLink = ltrim($httpLink);
        if (preg_match($regEx1, $httpLink)) {
            $newLink = "http://".$httpLink;
        } else if (preg_match($regEx2, $httpLink)) {
            $newLink = "http://www.".$httpLink;
        } else if (preg_match($regEx3, $httpLink)) {
            $newLink = $httpLink;
        }
    }
    return strtolower($newLink);
}//END OF FUNCTION TO ADD 'https://www.website.com' OR 'https://www.website.com.ng'

/**
 * @param $httpLink
 * @return String
 */
function addDocsLink($httpLink){
    //validate Download Link here
    $regEx1 = "/^www\.[a-z]+\.[a-z]+|^www\.[a-z]+\.[a-z]+\.[a-z]+|^www\.[a-z]+\.[a-z]+\/[a-z]+|^www\.[a-z]+\.[a-z]+\.[a-z]+\/[a-z]+/i"; //MATCHES www.facebook.com
    $regEx2 = "/^[a-z]+\.[a-z]+|^[a-z]+\.[a-z]+\.[a-z]+|^[a-z]+\.[a-z]+\/[a-z]+|^[a-z]+\.[a-z]+\.[a-z]+\/[a-z]+/i"; //MATCHES facebook.com
    $regEx3 = "/^https?:\/\/www\.[a-z]+\.[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]+\.[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]+\/[a-z]+|^https?:\/\/www\.[a-z]+\.[a-z]+\.[a-z]+\/[a-z]+/i";
    $newLink = "";
    if(is_string($httpLink)) {
        $httpLink = ltrim($httpLink);
        if (preg_match($regEx1, $httpLink)) {
            $newLink = "http://".$httpLink;
        } else if (preg_match($regEx2, $httpLink)) {
            $newLink = "http://www.".$httpLink;
        } else if (preg_match($regEx3, $httpLink)) {
            $newLink = $httpLink;
        }
    }
    return strtolower($newLink);
}

/**
 * Function to greet users according to time of day
 * E.g "morning", "afternoon", "evening"
 * @return String
 */
function greetUser() {
    $timeOfDay = "";
    if(date('a') == 'am') {//begin 1st if stmt
        $timeOfDay = "morning";
    }//end 1st if stmt
    //breakdown the time from 12:00pm - 11:59pm into "afternoon" & "night"
    else if(date('a') == 'pm' && (date('h') >= 04 && date('h') <= 11)) {
        $timeOfDay = "evening";
    }
    else if(date('a') == 'pm') {
        $timeOfDay = "afternoon";
    }
    return $timeOfDay;
}//end of function "greetUser()"

/**
 * FACEBOOK SDK FUNCTION
 * NOTE: Always start a session on the client page using this function
 * NOTE: Always load this resource "Facebook/autoload.php" on the client page
 * @param String  ->   $app_id
 * @param String  ->   $app_secret
 * @param String  ->   $graph_version
 * @param String  ->   $loginUrl
 * @param String  ->   $fields
 * @return array  ->   $result
 */
function facebookSdk($app_id, $app_secret, $graph_version, $fields, $loginUrl){
    $result = [];
    if(isset($app_id) && isset($graph_version) && isset($urlString)){
        $fb = new Facebook\Facebook([
            "app_id" => $app_id,
            "app_secret" => $app_secret,
            "default_graph_version" => $graph_version
        ]);
        $permissions = [];
        $helper = $fb->getRedirectLoginHelper();
        try{
            $accessToken = $helper->getAccessToken();
        }
        catch(Facebook\Exceptions\FacebookResponseException $e){
            $result[0] = "<b>GRAPH RETURNED AN ERROR: </b>".$e->getMessage();
            $result[1] = false;
        }
        catch(Facebook\Exceptions\FacebookSDKException $e){
            $result[0] = "<b>FACEBOOK SDK RETURNED AN ERROR: </b>".$e->getMessage();
            $result[1] = false;
        }
        if(isset($accessToken)){
            //When using this function always copy from the word "fields" on the fb graph as the $urlString
            $url = "https://graph.facebook.com/".$graph_version."/me?".$fields."&access_token={$accessToken}";
            $headers = array("Content-type: application/json");

            $curl = curl_init(); //Initialize the curl server service
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
            curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla /5.0 (Windows  Nt)");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $jsonArray = curl_exec($curl); //Execute the curl service here
            $result = json_decode($jsonArray, TRUE); //$result opens a node into the json array on user data by facebook
        } else{
            $loginPage = $helper->getLoginUrl($loginUrl, $permissions);
            $result[0] = "<a href=\"$loginPage\">LOGIN WITH FACEBOOK</a>"; //this link will later be a button
            $result[1] = "Undefined";
        }
    }
    return $result;
}//end of facebookSdk function


/**
 *@link https://graph.facebook.com/v2.11/me?fields="friends,profile"
 *@param String => $APP_ID
 *@param String => $APP_SECRET
 *@param String => $GRAPH_VER
 *@param String => $FB_FIELDS //get values from e.g ->me?fields=email,friends,profile
 *@return mixed => $result
 */
function facebookFunc($APP_ID, $APP_SECRET, $GRAPH_VER, $FB_FIELDS){
    $fb = new Facebook\Facebook([
        "app_id" => $APP_ID,
        "app_secret" => $APP_SECRET,
        "default_graph_version" => $GRAPH_VER
    ]);

    $permissions = [];
    $result = "";
    $helper = $fb->getRedirectLoginHelper();
    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        //When facebook-graph returns an error
        $result = "<span style='color: teal; font-family: consolas;'>
          <b style='color: red;'>Graph returned an error: </b>".$e->getMessage()."</span>";
    }
    catch(Facebook\Exceptions\FacebookSDKException $e) {
        //when facebook-validation fails or other Local issues
        $result = "<span style='color: teal; font-family: consolas;'>
          <b style='color: red;'>Facebook SDK returned an error: </b>".$e->getMessage()."</span>";
        exit("<b style='font-family:Consolas;color:red;'>Facebook encountered an error</b>");
    }
    if(isset($accessToken)) {
        $url = "https://graph.facebook.com/v2.11/me?".$FB_FIELDS."&access_token={$accessToken}";
        $headers = array("Content-type: application/json");

        $ch = curl_init(); //Initialize the curl server service
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla /5.0 (Windows  Nt)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $exec = curl_exec($ch); //Execute the curl service here
        $result = json_decode($exec, TRUE); //$result opens a node into the json array on user data by facebook
    } else {
        $loginUrl = $helper->getLoginUrl("http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'], $permissions);
        $result = header("Location: ".filter_var($loginUrl, FILTER_SANITIZE_URL)); //if(!isset($access_token) goto facebook.com & grab details to this page)
    }
    return $result;
}//end of facebookFunc

/**
 *@link => https://www.developers.google.com
 *@param String => $CLIENT_ID
 *@param String => $CLIENT_SECRET
 *@param String => $REDIRECT_URI
 *@return array | String => $result
 */
function googleFunc($CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI){
    //initialize the Google package & support methods
    $client = new Google_Client();
    $client->setClientId($CLIENT_ID);
    $client->setClientSecret($CLIENT_SECRET);
    $client->setRedirectUri($REDIRECT_URI);
    $client->setScopes("email");

    $plus = new Google_Service_Plis($client);
    $result = "";
    //Actual process
    if(isset($_REQUEST["logout"])){
        session_unset();//if user requests a log-out, unset the session
    }

    if(isset($_GET['code'])){
        $client->authenticate($_GET['code']);
        $access_token = $client->getAccessToken();//get access_token from google
        $redirect = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        header("Location: ".filter_var($redirect, FILTER_SANITIZE_URL));
    }
    if(isset($access_token)){
        $client->setAccessToken($access_token);
        $result = $plus->people->get("me");//open node to get user-details from google in JSON format
    } else{
        $authUrl = $client->creatAuthUrl();
        $result = header("Location: ".filter_var($authUrl, FILTER_SANITIZE_URL));
    }
    return $result;
}//end of googleFunc

function getRealIpAddr(){
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 *@param String => $str1
 *@param String => $str2
 *@param String => $str3
 *@param String => $str4
 *@return String=> $result
 */
function strconcat($str1, $str2, $str3, $str4){
    $result = "";
    if(is_string($str1)){//if-1
        if(is_string($str2)){//if-2
            if(is_string($str3)){//if-3
                if(is_string($str4)){//if-4
                    if(isset($str1) && isset($str2)){//if-5
                        $result = $str1.$str2;
                        if(isset($str3) && isset($str4)){//if-6
                            if($str3 != ""){//if-7
                                $result.= $str3;
                                if($str4 != "")//if-8
                                    $result.= $str4;
                            }
                        }
                    }
                }else{//else-4
                    $result = "<span style='font-family: Courier New, Courier, monospace; color: red;'><b>Param 4</b> is not a string</b></span>";
                }
            }else{//else-3
                $result = "<span style='font-family: Courier New, Courier, monospace; color: red;'><b>Param 3</b> is not a string</b></span>";
            }
        }else{//else-2
            $result = "<span style='font-family: Courier New, Courier, monospace; color: red;'><b>Param 2</b> is not a string</b></span>";
        }
    }else{//else-1
        $result = "<span style='font-family: Courier New, Courier, monospace; color: red;'><b>Param 1</b> is not a string</b></span>";
    }
    return (string)$result;
}//end of strconcat()

/**
 *@param $old_date
 *NOTE: FORMAT for $old_date must be in this format => new DateTime("Y-m-d h:i:sa");
 *@param $accuracy
 *@param $now
 *@return NULL
 */
function dateDiff1(DateTime $old_date, $accuracy = 2, DateTime $now = NULL) {
    try {
        if ($now == NULL) {
            $now = new DateTime("now");
        }

        if (!is_int($accuracy) || $accuracy < 1 || $accuracy > 6) {
            throw new InvalidArgumentException("Date Accuracy should be an integer between 1 and 6");
        }

        if ($old_date > $now) {
            throw new InvalidArgumentException("The previous date cannot be greater than today's date");
        }

        $difference = $now->diff($old_date);

        $intervals = array('y' => 'Year', 'm' => 'Month', 'd' => 'Day', 'h' => 'Hour', 'i' => 'Minute', 's' => 'Second');

        $i = 0;
        $result = '';
        foreach ($intervals as $interval => $name) {
            if ($difference->$interval > 1) {
                $result .= $difference->$interval . $intervals[$interval] . 's ';
                $i++;
            } elseif ($difference->$interval == 1) {
                $result .= $difference->$interval . $intervals[$interval]." ";
                $i++;
            }
            if ($i == $accuracy) {
                break;
            }
        }

        return strconcat($result, "ago", "","");

    } catch (Exception $e) {
        echo $e;
        return NULL;
    }

}//End of dateDiff1()

/**
 *@param $old_date
 *@param $accuracy
 *@param $now
 *@return NULL
 */
function dateDiff2($old_date, $accuracy = 2, $now = Null)
{
//you can specify more periods starting from smaller to larger!
    $periods = array("second" => 1,
        "minute" => 60,
        "hour" => 3600,
        "day" => 86400,
        "week" => 604800,
        "month" => 2630880,
        "year" => 31570560,
        "decade" => 315705600);
//we inverse the array in order to check for the larger periods first
    $periods = array_reverse($periods);

    if ($now === Null) {
        $now = time();
    }

    if (!is_int($accuracy) || $accuracy < 1 || $accuracy > count($periods)) {
        throw new InvalidArgumentException("Date Accuracy should be an integer between 1 and 6");
    }

    $difference = $now - $old_date;
    if ($difference < 0) { throw new InvalidArgumentException('The previous date cannot be greater than today\'s date'); }
    $result = '';
    $i = 0;
    foreach($periods as $k=>$v){
//round the number of times a large period fits in the time difference
        $tmp = floor($difference/$v);
        if($tmp > 1){
            $result .= $tmp . $k . 's ';
            $i++;
        }elseif($tmp == 1){
            $result .= $tmp . $k;
            $i++;
        }
//subtract the reminder of the division if the difference can be divided by the current period in the loop
        $difference = $difference%$v;
//break the loop if desired accuracy is reached
        if($i == $accuracy){
            break;
        }
    }
    return $result;
}

/**
 * FUNCTION checkPageUrl() CHECKS TO SEE IF THE URL OF THE CURRENT PAGE MATCHES THE $_SERVER['HTTP_REFERER']
 * IF IT MATCHES THE CURRENT PAGE, IT RETURN 1(TRUE)
 * IF IT DOES NOT MATCH THE CURRENT PAGE, IT RETURNS 0(FALSE)
 * @return bool => $check
 */
function checkPageUrl(){
    $currentPageUrl1="http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $currentPageUrl2="https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $referer=@$_SERVER['HTTP_REFERER'];
    $check=0;

    if(($currentPageUrl1==$referer || $currentPageUrl2==$referer)){
        $check=1;
    }
    return $check;
}

/**
 * @function decodePassword
 * @param $password
 * @return string
 * FUNCTION TO CHANGE REMOVE THE STRINGS 'wildcard' FROM THE PASSWORD PREFIX &
 * 'kl&user' FROM THE PASSWORD SUFFIX
 */
function decodeLastPart($password){
    $clean_pass=null; $password=strtolower(base64_decode($password));

    if(preg_match("/kl&user$/i", $password))
        $clean_pass = preg_replace("/kl&user$/i", "", $password);

    return (string)$clean_pass;
}

/**
 * @param $password
 * @return string
 */
function decodePassword($password){
    $clean_pass=null;
    if(preg_match("/^wildcard/i", $password))
        $clean_pass=preg_replace("/^wildcard/i", "", $password);

    return (string)$clean_pass;
}

/**
 * @return string
 */

/**
 * @TODO
 * APPEND 'www.' TO THE ACTIVATION LINK WHEN UPLOADED TO A SERVER
 * REMOVE '/kollabo.com'
 */
function defaultEmailUrl(){
    return (string)"http://".$_SERVER['HTTP_HOST']."/kollabo.com/";
}


/**
 * @return array => $orderedArray
 * @param $unorderedArray
 */
function sortArray(Array $unorderedArray){
    $orderedArray=[];

    if(is_array($unorderedArray)){
        for($i=0; $i < count($unorderedArray); $i++){

            if( !@in_array($unorderedArray[$i], $orderedArray) && !@(is_array($unorderedArray[$i])) )
                @array_push($orderedArray, $unorderedArray[$i]);
        }
    }
    return (array)$orderedArray;
}


/**
 * @param $password
 * @return boolean
 * -> A VALID ADMIN PASSWORD MUST BE 7 IN LENGTH
 * -> MUST HAVE BETWEEN 4 & 6 ALPHABET CHARACTERS IN THE PASSWORD
 * -> MUST HAVE BETWEEN 2 & 3 DIGIT CHARACTERS IN THE PASSWORD
 */
function validAdminPassword($password){
    $valid=false; trim($password);
    if(strlen($password) == 7){
        if( (preg_match("/[a-z]{4,6}/i", $password) && preg_match("/[0-9]{1,3}/i", $password)) )
            $valid = true;
    }
    return (boolean)$valid;
}

function userLoggedIn(){
    $result = false;
    if( (isset($_COOKIE['kollabo_user_name']) && isset($_COOKIE['kollabo_user_password'])) ){
        $result = true;
    }
    return (boolean)$result;
}

function passwordStrength($password){

    $strength = ""; $passwordScore=0; trim($password);

    if(strlen($password) >= 6) $passwordScore+=20;

    if(preg_match("/\d+/", $password)) $passwordScore+=20;

    if(preg_match("/[a-z]+/", $password)) $passwordScore+=20;

    if(preg_match("/[A-Z]+/",$password)) $passwordScore+=20;

    $sp_characters = ["@", "#", "%", "^", "$", "&", "*", "_", "~", "?"];
    $password_array = explode(" ", $password);

    for($i=0; $i<$password_array; $i++){
        if(in_array($i, $sp_characters)){
            $passwordScore+=20;
            break;
        }
    }

    if($passwordScore >= 100) $strength = "STRONG";

    else if($passwordScore >= 80) $strength = "MEDIUM";

    else if($passwordScore >= 60) $strength = "WEAK";

    else $strength = "VERY WEAK";

    return (string) $strength;
}

function clean_Get($tags){
    $tags = base64_decode($tags);
    #CLEAN THE $_GET['tag'] OF HTML <TAGS> SENT BY 'REFERER PAGE' WITH PREG_REPLACE
    $tags = (preg_match("/^<span style='color:indianred;'>/i", $tags))?preg_replace("/^<span style='color:indianred;'>/i","", $tags):$tags;

    $tags = (preg_match("/<\/span>$/i", $tags))?preg_replace("/<\/span>$/i","", $tags):$tags;

    return $tags;
}

function updateAllTables(Array $tableDetails, $new_username, $old_username, $con){
    $queryWorks= false;
    for($i=0; $i< count($tableDetails); $i++){
        $sql = "UPDATE ".array_keys($tableDetails)[$i]." SET ".array_values($tableDetails)[$i]. "='{$new_username}' WHERE(".array_values($tableDetails)[$i]."='{$old_username}')";
        $sqlRs = mysqli_query($con, $sql);
        if( ($sqlRs) ) $queryWorks = true;
    }
    return $queryWorks;
}

/**
 * @param $date
 * @return String
 */
function formatDate($date){
    return date("h:i:sa", strtotime($date));
}

function formatDate2($date){
    return (date("Y-m-d, h:i:s", mktime($date)) === date("Y-m-d, h:i:s"))
        ? date("h:i:sa", strtotime($date)):
        date("D-m-d,Y", strtotime($date));
}

/**
 * @param $file
 * @return String
 */
function chatAttachment($file){
    $output_msg = "";
    if( (($file['error'] == 0) && !($file == "")) ) {

        #get image properties...
        $file_name = $file['name'];
        $file_size = $file['size'];
        $file_temp_path = $file['tmp_name'];
        $file_type = $file['type'];

        /**
         * Check if the file uploaded exists
         */
        if( !(file_exists($file_name)) ){
            /**
             * Check to see if the file is an executable file
             */
            if( !(is_executable($file_name))) {
                /**
                 * Check to see if file is directory
                 */
                if ( !(is_dir($file_name)) ) {
                    /**
                     * Check to see if the file size exceeds the specified limit=> 1mb
                     */
                    if (!($file_size > 1000000)) {
                        $allowed_file_types =
                            Array(
                                "jpg",
                                "png",
                                "gif",
                                "jpeg",
                                "pdf",
                                "mp3",
                                "txt",
                                "js",
                                "php",
                                "json",
                                "html",
                                "css",
                                "zip",
                                "rar",
                                "java",
                                "cs",
                                "cpp"
                            );
                        $split_file_names = explode("/", $file_type);

                        $file_ext = strtolower(end($split_file_names));
                        /**
                         * Check to see if the file meets the specified extension types
                         */
                        if (in_array($file_ext, $allowed_file_types)) {
                            /**
                             * Move the files into the dir=> '../attachments';
                             */
                            $output_msg = "../attachments/".basename($file_name);
                            move_uploaded_file($file_temp_path, $output_msg);

                        } else $output_msg = "error: file type not allowed";

                    } else $output_msg = "error: file size exceeds 1mb";

                } else $output_msg = "error: folders not allowed";

            } else $output_msg = "error: executable files not allowed";
        }
        else $output_msg="error: no such file found";
    }

    return $output_msg;
}

function format_dateTime(DateInterval $interval){
    try{
        $result = "";

        if($interval->y && $interval->y >0){
            $yPlural = false; $mPlural = false;

            if($interval->y >1) $yPlural = true;

            if($interval->m){
                if($interval->m >1) $mPlural = true;
            }

            if($yPlural==true && $mPlural==false){
                $result = $interval->format("%y years %m month");
            }
            else if($yPlural==false && $mPlural==true){
                $result = $interval->format("%y year %m months");
            }
            else if($yPlural==true && $mPlural==true){
                $result = $interval->format("%y years %m months");
            }
        }

        else if($interval->m && $interval->m >0){
            $mPlural = false; $dPlural = false;

            if($interval->m >1) $mPlural = true;

            if($interval->d){
                if($interval->d >1) $dPlural = true;
            }

            if($mPlural==true && $dPlural==false){
                $result = $interval->format("%m months %d day");
            }
            else if($mPlural==false && $dPlural==true){
                $result = $interval->format("%m month %d days");
            }
            else if($mPlural==true && $dPlural==true){
                $result = $interval->format("%m months %d days");
            }
        }
        else if($interval->d && $interval->d >0){
            $dPlural = false; $hPlural = false;

            if($interval->d >1) $dPlural = true;

            if($interval->h){
                if($interval->h >1) $hPlural = true;
            }

            if($dPlural==true && $hPlural==false){
                $result = $interval->format("%d days %h hour");
            }
            else if($dPlural==false && $hPlural==true){
                $result = $interval->format("%d day %h hours");
            }
            else if($dPlural==true && $hPlural==true){
                $result = $interval->format("%d days %h hours");
            }
        }
        else if($interval->h && $interval->h >0){
            $hPlural = false; $iPlural = false;

            if($interval->h >1) $hPlural = true;

            if($interval->i){
                if($interval->i >1) $iPlural = true;
            }

            if($hPlural==true && $iPlural==false){
                $result = $interval->format("%h hours %i minute");
            }
            else if($hPlural==false && $iPlural==true){
                $result = $interval->format("%h hour %i minutes");
            }
            else if($hPlural==true && $iPlural==true){
                $result = $interval->format("%h hours %i minutes");
            }
        }
        else if($interval->i && $interval->i >0){
            $iPlural = false; $sPlural = false;

            if($interval->i >1) $iPlural = true;

            if($interval->s){
                if($interval->s >1) $sPlural = true;
            }

            if($iPlural==true && $sPlural==false){
                $result = $interval->format("%i minutes %s second");
            }
            else if($iPlural==false && $sPlural==true){
                $result = $interval->format("%i minute %s seconds");
            }
            else if($iPlural==true && $sPlural==true){
                $result = $interval->format("%i minutes %s seconds");
            }
        }
        return $result;
    }
    catch(Exception $e){ return NULL; }
}

function display_dateTime(DateTime $first_date, DateTime $second_date){
    $result = "";
    if( !($first_date > $second_date) ){
        if( ($first_date==$second_date) ){
            $result = "just now";
        }
        else{
            $result = format_dateTime($first_date->diff($second_date)) ." ago";
        }
    }
    return $result;
}

function checkImage($image, $thumbnail_width, $thumbnail_height, $savePath){
    $result = "";
    //check to see if the upload went well
    if( (is_string($savePath) && is_int($thumbnail_height) && is_int($thumbnail_width)) ){

        if($image['error']== 0){
            $img_size = $image['size'];
            $img_name = $image['name'];
            $img_type = $image['type'];
            $img_tmp_path = $image['tmp_name'];

            if($img_size > 1000000){ $result = "image is too large"; }

            else{
                $valid_extensions = array("jpg", "jpeg", "gif", "png");
                $img_ext_arr = explode("/", $img_type);
                $img_ext = strtolower(end($img_ext_arr));
                /**RENAME THE IMAGE FILE HERE
                 * USE rand() AND time() FUNCTIONS TO GET A UNIQUE TIMESTAMP
                 * @var $new_img_name
                 **/
                $new_img_name = rand(8, 20)."_".time().".".$img_ext;

                if(!in_array($img_ext, $valid_extensions)){ $result = "wrong file type, only jpg, jpeg, png & gif"; }

                else{
                    $img_path = $savePath.basename($new_img_name); //CORRECT THE PATH TO SPE
                    move_uploaded_file($img_tmp_path, $img_path);

                    if(isset($thumbnail_width, $thumbnail_height) ){
                        //cropping of small images starts here
                        $magicObj = new imageLib($img_path);
                        $magicObj->resizeImage($thumbnail_width, $thumbnail_height);
                        $magicObj->saveImage($img_path, 100);
                        $result = $img_path;
                    }
                }
            }
        }
    }
    else $result = "parameters do not match the specified data types";

    return (string)$result;
}

//FUNCTIONS WRITTEN FROM EXTERNAL __OOP_PHP TRAINING
//this helper function strips out leading zeros from the strftime() function
/**
 * @param string $marked_date
 * @return string
 */
function strip_zero_from_date($marked_date = ""){
    //remove marked zeros
    $no_zeros = str_replace("*0", "", $marked_date);
    //remove any remaining marks
    $clean_string = str_replace("*","",$no_zeros);
    //return the $clean_string
    return (String)$clean_string;
}

function redirect_to($location = null){
    if( !($location ==null) ){
        header("Location:{$location}");
        exit();
    }
}


function output_message($message = ""){
    if(!empty($message)){
        return "<p class='message'>{$message}</p>";
    }
    else return "";
}

function output_err_message($message=""){
    if(!empty($message)){
        return "<h5 style='color:indianred;font-family:Consolas;font-weight:bold;'>$message</h5>";
    }
    else return "";
}

function output_success_message($message=""){
    if(!empty($message)){
        return "<h5 style='color:#00A07A;font-family: Consolas;font-weight: bold;'>$message</h5>";
    }
    else return "";
}

function explodeString($delimiter=",", $string=""){
    if(!empty($string)){
        return explode($delimiter, $string);
    }
    else return "";
}

//function automatically loads any class_file used in the page that makes a call to it, & requires it on the client page
//else: it dies with an error_message();
function __autoload($class_name){
    $class_name = strtolower($class_name);
    $path = LIB_PATH.DS."{$class_name}.php";

    if(file_exists($path)){ require_once($path); }

    else{
        $die_string = "<p style='font-family:corbel;color:indianred;'>The file: <span style='font-weight:bold;color:teal;'>'{$class_name}.php'</span>, could not be found</p>";

        die($die_string);
    }
}

function include_layout_template($template=""){
    include(LIB_PATH.DS.$template);
}

function makeMyDir($path){
    return (!file_exists($path)) ? mkdir($path) : output_err_message("__DIR__ already exists");
}

/**
 * THIS BULK SMS FUNCTION WORKS WITH ClockWork() API GATEWAY
 * @param $to
 * @param $api_key
 * @param $message
 * @return boolean
*/
function sendBulkSms($api_key, $message, $to){
    $clockwork = new Clockwork($api_key);
    $msg=[];
    if(isset($message, $to)){
        $msg = Array("to"=>$to, "message"=>$message);
    }
    return ($clockwork->send($msg)) ? true : false;
}