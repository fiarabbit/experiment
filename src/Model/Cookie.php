<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/6/2017
 * Time: 6:00 PM
 */

namespace Hashimoto\Experiment\Model;


class Cookie {
static public function setJSONCookie($arr){
foreach($arr as $key => $value){
    setcookie((string)$key,json_encode($value));
}
}
}