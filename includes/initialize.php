<?php
/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/6/2018
 * Time: 7:50 AM
 */
//define the core paths
//define them as absolute paths to make sure that require_once works as expected

//DIRECTORY_SEPARATOR: is php defined constant
//for windows('\'),
//for unix('/')
defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR);

//get the absolute path from the c:// drive to the current project directory
//this constant defines 'path' for site files
defined("SITE_ROOT") ? null : define("SITE_ROOT", DS.'xampp'.DS.'htdocs'.DS.'sigma');

defined("LIB_PATH") ? null : define("LIB_PATH", SITE_ROOT.DS.'includes');

defined("RES") ? null : define("RES", "lib");

defined("IMG_MAGIC")? null : define("IMG_MAGIC", SITE_ROOT.DS.RES.DS.'php-image-magician');

//load the config file first
require_once(LIB_PATH.DS."config.php");

//load basic functions, so everything can use them
require_once(LIB_PATH.DS."functions.php");

//load core objects
require_once(LIB_PATH.DS."session.php");
require_once(LIB_PATH.DS."database.php");
require_once(LIB_PATH.DS."database_object.php");
require_once(LIB_PATH.DS."FileUploads.php");
require_once(LIB_PATH.DS."Registration.php");

//load DB-related classes
require_once(LIB_PATH.DS."user.php");

//require php-image-magician
require_once(IMG_MAGIC.DS."php_image_magician.php");