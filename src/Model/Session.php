<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/31/2017
 * Time: 6:36 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\History;

class Session {
    const KEY_UID = 'uid';
    const KEY_HISTORY = 'history';
    const SESSION_NAME = 'exp1';
    static private $session_started;
    private static function sessionStart(string $session_name)
    {
        if (!self::$session_started) {
            session_name($session_name);
            self::$session_started = session_start();
        }
        return self::$session_started;
    }
    public static function checkHistory(string $session_name){ // check if $_SESSION['protocol'] is set
        self::sessionStart($session_name);
        return isset($_SESSION[self::KEY_HISTORY]);
    }
    public static function setHistory(string $session_name, History $history){
        self::sessionStart($session_name);
        $_SESSION[self::KEY_HISTORY] = $history->encodeJSONAll();
        session_regenerate_id(true);
    }
    public static function deleteSession(string $session_name)
    {
        self::sessionStart($session_name);
        session_destroy();
        $_SESSION = [];
        self::$session_started = false;
        if (isset($_COOKIE[$session_name])) {
            setcookie($session_name, '', time() - 1800, '/');
        }
    }

}