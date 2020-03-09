<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/9/2018
 * Time: 3:43 PM
 */
require_once("../includes/initialize.php");

if($_SERVER['REQUEST_METHOD']=="POST"){
    //get the form fields here
    $username = strtolower(sanitize($_POST['username']));
    $password = strtolower(sanitize($_POST['password']));

    //Run method to remove escape strings before using them in sql
    $database->escape_value($username);
    $database->escape_value($password);

    $found_user = User::authenticate($username, $password);
    if($found_user){
        $session->login($found_user);
        //if($session->is_logged_in()){ echo "logged in"; }

        //if user checks 'remember_me', set a cookie to remember their username & password
        if(isset($_POST['remember_me'])){
            setcookie("sigma_username", $username, time()+(60*60*24*365),"/");
            setcookie("sigma_password", $password, time()+(60*60*24*365),"/");
        }
        die("user correct");
    }
    else die("<h5 style='color:indianred;font-family:Consolas;font-weight:bold;'>ERROR: WRONG LOGIN CREDENTIALS</h5>");
}