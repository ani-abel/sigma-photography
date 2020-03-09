<?php

/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/14/2018
 * Time: 11:21 AM
 */
include("initialize.php");

class Registration
{
    private $username;
    private $profile_img = [];
    private $password;
    private $gender;
    private $email;
    private $first_name;
    private $last_name;
    /**
     * SETS THE DEFAULT TABLE NAME TO "users", TO CHECK IF THE USER EXISTS,
     * TO CHANGE SEARCH TABLE, RESET "self::$__table_name" TO A NEW VALUE
    */
    public static $__table_name = "users";
    public static $__reg_details_in_use = false;

    public function __construct($username, $password, $gender, $email, Array $profile_img_arr)
    {
        $this->setUsername($username);
        $this->split_username($username);
        $this->setEmail($email);
        $this->setGender($gender);
        $this->setProfileImg($profile_img_arr);
        //CHECK TO SEE OF THE NEW USER DETAILS ALREADY EXIST IN THE DB
        if(self::checkIfUserExists(self::$__table_name, $this->username, $this->email,$this->password)){
            self::$__reg_details_in_use = true;
        }
    }

    protected function setProfileImg(Array $img){
        global $file_obj;
        if($img == null || empty($img)){ die(output_err_message("ERROR: PROFILE IMAGE IS REQUIRED")); }
        else{
            if($file_obj->validateSingleFiles($img)){ $this->profile_img = $img; }
        }
    }

    protected function setUsername($username){
        if($this->validateFormFields("username", $username)){
            $this->username = $username;
        }
    }

    protected function setGender($gender){
        if($this->validateFormFields("gender", $gender)){
            $this->gender = $gender;
        }
    }

    protected function setEmail($email){
        if($this->validateFormFields("email", $email)){ $this->email = $email; }
    }

    protected function sanitizeField($field){ return sanitize(strtolower($field)); }

    protected static function checkIfUserExists($table_name="users", $username, $email, $password){
        global $database;
        $user_found = false;
        $sql ="SELECT * FROM ".$table_name." WHERE(email='{$email}' OR username='{$username}' OR password='{$password}')";
        $rs = $database->query($sql);
        if($rs){ if($database->num_rows($rs) > 0){ $user_found = true; self::$__reg_details_in_use = true; } }

        return $user_found;
    }

    protected function validateFormFields($field="username", $value){
        $rs= false;
        if($field == "username"){
            $this->sanitizeField($value);
            if(empty($value)) {
                die(output_err_message("ERROR: USERNAME IS REQUIRED"));
            }

            else{
                if(!is_string($field)){ die(output_err_message("ERROR: USERNAME MUST BE A STRING")); }
                else $rs = true;
            }
        }
        else if($field == "password"){
            $this->sanitizeField($value);
            if(empty($value)){ die(output_err_message("ERROR: PASSWORD IS REQUIRED")); }

//            else{
//                $random_string = "user_string"
//            }
        }
        else if($field=="gender"){
            if(empty($value)){ die(output_err_message("ERROR: GENDER IS REQUIRED")); }

            else {
                $rs = (preg_match("/(male|female)/i", $value)) ? true :
                    die(output_err_message("ERROR: WRONG VALUE FOR GENDER"));
            }

        }
        else if($field=="email"){
            $this->sanitizeField($value);
            if(empty($value)){ die(output_err_message("ERROR: EMAIL IS REQUIRED")); }
            else{
                if(filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $rs = true;
                }
                else die(output_err_message("ERROR: WRONG EMAIL FORMAT"));
            }
        }

        return $rs;
    }

    private function split_username($username){
        $arr = explode(" ", $username);

        (!empty($arr[0])) ? $this->first_name = $arr[0] : null;

        (!empty($arr[1])) ? $this->last_name = $arr[1] : null;

    }

    public function getUsername(){ return (String)$this->username; }

    public function getFirstName(){ return (String)$this->first_name; }

    public function getLastName(){ return (String)$this->last_name; }

    public function getGender(){ return (String)$this->gender; }

    public function getEmail(){ return (String)$this->email; }

    public function getProfileImg(){ return (Array)$this->profile_img; }

    public function getPassword(){ return (String)$this->password; }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        $rs =
            output_success_message("USERNAME: ".$this->username.", PASSWORD: ".$this->password.", EMAIL: ".$this->email.", GENDER: ".$this->gender);
        return $rs;
    }

}


if(isset($_POST['submit_btn'])){
    $profile_img = $_FILES['file'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];

    $reg = new Registration($username, $password, $gender, $email,$profile_img);
    FileUploads::$__init_file_size = 500;
    echo "IMAGE NAME: ".$reg->getProfileImg()["name"];
    echo "EMAIL: ".$reg->getEmail();
    echo "USERNAME: ".$reg->getUsername();
    echo "PASSWORD: ".$reg->getPassword();
    echo "FIRST NAME: ".$reg->getFirstName();

    echo $reg->__toString();

    echo (Registration::$__reg_details_in_use)?"true":"false";



}

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<!--<form action="" method="post" enctype="multipart/form-data">-->
<!--    <input type="file" name="file" accept="image/*" /><br>-->
<!--    <input type="text" name="username" placeholder="USERNAME"/><br>-->
<!--    <input type="password" name="password" placeholder="PASSWORD"/><br>-->
<!--    <input type="text" name="email" placeholder="EMAIL"/><br>-->
<!--    <select name="gender" id="">-->
<!--        <option value="">gender</option>-->
<!--        <option value="male">male</option>-->
<!--        <option value="female"> female</option>-->
<!--    </select><br><br>-->
<!--    <input type="submit" name="submit_btn" value="SUBMIT"/>-->
<!--</form>-->
</body>
</html>
