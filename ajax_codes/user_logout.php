<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/9/2018
 * Time: 11:28 PM
 */
require_once("../includes/initialize.php");

if($session->is_logged_in()){
    $session->logout();
    redirect_to("../index.php");
}
else redirect_to("../php/user_welcome.php");