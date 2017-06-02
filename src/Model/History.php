<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/31/2017
 * Time: 6:27 PM
 */

namespace Hashimoto\Experiment\Model;


class History {
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
    function __construct(string $UID) {
        $this->UID=$UID;
    }
    function getUID():string{
        return $this->UID;
    }
    function getPointer():int{
        return $this->pointer;
    }
    function encodeJSONProtocol():string{ // return array of string
        return json_encode(self::PROTOCOL);
    }
    function encodeJSONAll():string{
        $arr=[
            'uid'=>$this->UID,
            'protocol'=>self::PROTOCOL,
            'pointer'=>$this->pointer
        ];
        return json_encode($arr);
    }
    function decodeJSONAll(string $historyJSON):History{
        $arr=json_decode($historyJSON);
        $history=new History($arr['uid']);
        $history->pointer=$arr['pointer'];
        return $history;
    }
}