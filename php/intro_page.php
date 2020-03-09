<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 12/7/2018
 * Time: 1:04 PM
 */

if($_SERVER['REQUEST_METHOD']=="POST"){
    $file = $_FILES['file'];

    print_r($file);
}