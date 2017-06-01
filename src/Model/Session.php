<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/31/2017
 * Time: 6:36 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\Protocol;

class Session {
    const KEY_UID = 'uid';
    const KEY_PROTOCOL = 'protocol';

    static private $session_started;
    private static function sessionStart(string $session_name)
    {
        if (!self::$session_started) {
            session_name($session_name);
            self::$session_started = session_start();
        }
        return self::$session_started;
    }
    public static function checkUID(string $session_name){
        self::sessionStart($session_name);
        return isset($_SESSION[self::KEY_UID]);
    }
    public static function checkProtocol(string $session_name){
        self::sessionStart($session_name);
        return isset($_SESSION[self::KEY_PROTOCOL]);
    }
    public static function registerUID(string $session_name, string $user_id)
    {
        self::sessionStart($session_name);
        $_SESSION[self::KEY_UID] = $user_id;
        session_regenerate_id(true);
    }
    public static function registerProtocol(string $session_name, Protocol $protocol){
        self::sessionStart($session_name);
        $_SESSION[self::KEY_PROTOCOL] = $protocol;
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