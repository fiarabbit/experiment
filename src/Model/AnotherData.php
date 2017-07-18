<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/27/2017
 * Time: 2:34 PM
 */

namespace Hashimoto\Experiment\Model;


class AnotherData {
const CLIENT_PROPERTY=[
    'username',
    'times',
    'hash',
    'type'
];
const SERVER_PROPERTY=[
    'username',
    'times',
    'hash',
    'type'
];
static public function getClientSideData():array{
    $cliAssoc=[];
 foreach (self::CLIENT_PROPERTY as $value){
     $v=filter_input(INPUT_GET,$value);
     if($v==='NaN'){
         throw new \InvalidArgumentException('NaN argument error');
     }
     switch ($value) {
         case 'times':
             $v = (int)$v;
             break;
         case 'username':
         case 'hash':
         default:
             break;
     }
     $cliAssoc[$value]=$v;
 }
 return $cliAssoc;
}
static public function convertClientToServer (array $cliAssoc):array{
    $srvAssoc=[];
    foreach (self::SERVER_PROPERTY as $value){
        switch ($value){
            case 'username':
            case 'times':
            case 'hash':
                $srvAssoc[$value]=$cliAssoc[$value];
                break;
        }
    }
    return $srvAssoc;
}
}