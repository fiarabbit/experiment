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
        ['login','show'],
        ['experiment','adjust'],
        ['experiment','questionnaire'],
        ['experiment','calculation'],
        ['experiment','another'],
        ['experiment','questionnaire'],
        ['experiment','rest'],
        ['experiment','calculation'],
        ['experiment','another'],
        ['experiment','questionnaire']
    ];
    private $pointer=-1;
    private $UID;
    function __construct(string $UID) {
        $this->UID=$UID;
    }
    public function getUID():string{
        return $this->UID;
    }
    public function getPointer():int{
        return $this->pointer;
    }
    public function encodeJSONProtocol():string{ // return array of string
        return json_encode(self::PROTOCOL);
    }
    public function encodeJSONAll():string{
        $arr=[
            'uid'=>$this->UID,
            'protocol'=>self::PROTOCOL,
            'pointer'=>$this->pointer
        ];
        return json_encode($arr);
    }
//    public function decodeJSONAll(string $historyJSON):History{
//        $arr=json_decode($historyJSON);
//        $history=new History($arr['uid']);
//        $history->pointer=$arr['pointer'];
//        return $history;
//    }
    public function next(){
        $this->pointer+=1;
        $mysql=new MySQL();
        $mysql->registerHistory($this);
        Session::setHistory(Session::SESSION_NAME,$this);
        return self::PROTOCOL[$this->pointer];
    }
    public function this(){
        return self::PROTOCOL[$this->pointer];
    }
}