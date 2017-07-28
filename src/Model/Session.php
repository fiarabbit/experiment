<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/31/2017
 * Time: 6:36 PM
 */

namespace Hashimoto\Experiment\Model;


class Session {
    const KEY_USERNAME='username';
    const SESSION_NAME = 'exp1';
    static private $session_started;

    static function sessionStart()
    {
        if (!self::$session_started) {
            session_name(self::SESSION_NAME);
            self::$session_started = session_start();
        }
        return self::$session_started;
    }
    static public function getUsername(){
    // usernameをSessionに持っているかを確認する．あったらusernameを返す．なければfalseを返す
        self::sessionStart();
        if (isset ($_SESSION[self::KEY_USERNAME])){
            return $_SESSION[self::KEY_USERNAME];
        }else{
            return false;
        }
    }
    static public function setUsername(string $username){
        self::sessionStart();
        $_SESSION[self::KEY_USERNAME]=$username;
    }
    static public function deleteSession()
    {
        self::sessionStart();
        session_destroy();
        $_SESSION = [];
        self::$session_started = false;
        self::sessionStart();
        session_regenerate_id();
    }

}