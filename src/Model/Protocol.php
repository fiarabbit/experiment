<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/31/2017
 * Time: 6:27 PM
 */

namespace Hashimoto\Experiment\Model;


class Protocol {
    const PROTOCOL = [
        'login',
        'calc',
        'ques',
        'rest',
        'calc',
        'ques'
    ];
    private $pointer=0;
    private $UID;
    function __construct($UID) {
        $this->UID=$UID;
    }
    function getUID():string{
        return $this->UID;
    }
    function convertString():string{ // return array of string
        return implode(',',self::PROTOCOL);
    }
}