<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/6/2017
 * Time: 6:00 PM
 */

namespace Hashimoto\Experiment\Model;


class Cookie {
    const EXPIRE = 1577804400; // mktime(0,0,0,1,1,2020) [2020/1/1/0:0:0]
    const PATH = '/';
    static public function setJSONCookie($arr) {
        foreach ($arr as $key => $value) {
            setcookie((string)$key, json_encode($value),self::EXPIRE,self::PATH);
        }
    }
    static public function clearAllCookie(){
        foreach(array_keys($_COOKIE) as $key){
            setcookie($key,null,-1,self::PATH);
        }
        return true;
    }
}