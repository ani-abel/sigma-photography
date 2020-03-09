<?php

/**
 * Created by PhpStorm.
 * User: WILDcard
 * Date: 6/5/2018
 * Time: 10:05 PM
 */
class Session
{
    private $logged_in =false;
    public $user_id;

    public function __construct()
    {
        session_start();
        $this->check_login();

        if($this->logged_in){
            //carry out instructions if user is currently logged-in
        }
        else{
            //actions to be taken if the user is not currently logged-in
        }
    }

    public function is_logged_in(){
        return $this->logged_in;
    }

    public function login($user){
        //db should find user based on username & password
        if($user){
            $this->user_id = $_SESSION['user_id'] = $user->id;
            $this->logged_in = true;
        }
    }

    public function logout(){
        unset($_SESSION['user_id']);
        unset($this->user_id);
        $this->logged_in = false;
    }

    private function check_login(){
        if(isset($_SESSION['user_id'])){
            $this->logged_in = true;
            $this->user_id = $_SESSION['user_id'];
        }
        else{
            unset($this->user_id);
            $this->logged_in = false;
        }
    }

}

$session = new Session();
