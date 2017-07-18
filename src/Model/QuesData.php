<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/26/2017
 * Time: 7:53 PM
 */

namespace Hashimoto\Experiment\Model;


class QuesData {
const CLIENT_PROPERTY=[
    'username',
    'times',
    'hash',
    'qid',
    'value'
];
const SERVER_PROPERTY=[
    'username',
    'hash',
    'value',
    'times',
    'qid'
];
static public function getClientSideData():array{
    $cliAssoc=[];
    foreach (self::CLIENT_PROPERTY as $value){
        $v=filter_input(INPUT_GET,$value);
        if($v=='NaN'){
            throw new \InvalidArgumentException('NaN argument error');
        }
        switch ($value) {
            case 'times':
            case 'qid':
                $v = (int)$v;
                break;
            case 'value':
                $v=(float)$v;
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
static public function convertClientToServer(array $cliAssoc):array{
    $srvAssoc=[];
    foreach (self::SERVER_PROPERTY as $value){
        switch($value){
            case 'username':
            case 'times':
            case 'qid':
            case 'hash':
            case 'value':
                $srvAssoc[$value]=$cliAssoc[$value];
                break;
        }
    }
    return $srvAssoc;
}
}