<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 2018/01/19
 * Time: 2:30
 */

namespace Hashimoto\Experiment\Model;


class TmtData {
const CLIENT_PROPERTY=[
    'username',
    'times',
    'hash',
    'targetnumber',
    'qid',
    'start',
    'end',
    'mistake',
    'type'
];

const SERVER_PROPERTY = [
    'username',
    'times',
    'hash',
    'targetNumber',
    'qid',
    'startTimestamp',
    'endTimestamp',
    'mistake',
    'type'
];

static public function getClientSideData():array{
    $cliAssoc = [];
    foreach (self::CLIENT_PROPERTY as $value){
        $v=filter_input(INPUT_GET,$value);
        if($v==='NaN'){
            throw new \InvalidArgumentException('NaN argument error');
        }
        switch ($value) {
            case 'times':
            case 'qid':
            case 'targetnumber':
            case 'mistake':
                $v = (int)$v;
                break;
            case 'start':
            case 'end':
                $v = (float)$v / 1000;
                break;
            case 'username':
            case 'hash':
            case 'type':
            default:
                break;
        }
        $cliAssoc[$value]=$v;
    }
    return $cliAssoc;
}

static public function convertClientToServer (array $cliAssoc):array {
    $srvAssoc = [];
    foreach (self::SERVER_PROPERTY as $value){
        switch ($value){
            case 'username':
            case 'times':
            case 'hash':
            case 'qid':
            case 'mistake':
            case 'type':
                $srvAssoc[$value] = $cliAssoc[$value];
                break;
            case 'targetNumber':
                $srvAssoc[$value] = $cliAssoc["targetnumber"];
                break;
            case 'startTimestamp':
                $arrTime = explode('.', $cliAssoc["start"]);
                $srvAssoc[$value] = date('Y-m-d H:i:s', $arrTime[0]) . '.' .$arrTime[1];
                break;
            case 'endTimestamp':
                $arrTime = explode('.', $cliAssoc["end"]);
                $srvAssoc[$value] = date('Y-m-d H:i:s', $arrTime[0]) . '.' .$arrTime[1];
                break;
        }
    }
    return $srvAssoc;
}
}