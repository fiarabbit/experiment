<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/22/2017
 * Time: 4:13 PM
 */

namespace Hashimoto\Experiment\Model;


class Constant {
    const HASH_LENGTH=8;
    const PREFIX = 'http://experiment.va';
    const PROTOCOL = [
        ['login',0],
        ['adjust',0],
        ['questionnaire',0],
        ['another',0],
        ['experiment',0],
        ['questionnaire',1],
        ['another',1],
        ['experiment',1],
        ['questionnaire',2],
        ['another',2],
        ['finish',0]
    ];
    const pid=1;
    const DEFAULT_CONTROLLER = 'login';
    const DEFAULT_ACTION=[
        'login'=>'show',
        'adjust'=>'show',
        'admin'=>'showIndex',
        'questionnaire'=>'show',
        'another'=>'show',
        'experiment'=>'show',
        'finish'=>'show'
    ];
    static function getPointByController(string $query):array{
        $ret=[];
        foreach(self::PROTOCOL as $key=>$value){
            $controller=$value[0];
            // $count=$value[1];
            if($controller===$query){
                array_push($ret,$key);
            }
        }
        return $ret;
    }
    static function getController(int $pointer):string{
        return self::PROTOCOL[$pointer][0];
    }
    static function getTimes(int $pointer):int{
        return self::PROTOCOL[$pointer][1];
    }
}