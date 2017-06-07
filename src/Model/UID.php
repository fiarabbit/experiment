<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/30/2017
 * Time: 12:54 PM
 */

namespace Hashimoto\Experiment\Model;


use InvalidArgumentException;

class UID {
static function check($candidate){
    $candidate=$candidate??"";
    if ($candidate===""){
        throw new InvalidArgumentException('NullStringError');
    }else{
        return $candidate;
    }
}
}