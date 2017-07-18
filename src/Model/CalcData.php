<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/13/2017
 * Time: 7:09 PM
 */

namespace Hashimoto\Experiment\Model;


class CalcData {
    const CLIENT_NAN=99999;
    const SERVER_NAN=-1;
    const CLIENT_PROPERTY=[
        'qid',
        'timeOver',//
        'timeLimit',
        'displayTime',
        'answerTime',
        'var1',
        'var2',
        'answer',
        'times',
        'username',//
        'hash'//
    ];
    const SERVER_PROPERTY=[
        'username',
        'var1',
        'var2',
        'answer',
        'rt',
        'qid',
        'correct',
        'timeOver',
        'timeLimit',
        'hash',
        'times'
    ];
static public function getClientSideData():array {
    $cliAssoc=[];
    foreach (self::CLIENT_PROPERTY as $value){
        $v=filter_input(INPUT_GET,$value);
        if($v=='NaN'){
            throw new \InvalidArgumentException('NaN argument error');
        }
        switch ($value){
            case 'qid':
            case 'timeLimit':
            case 'displayTime':
            case 'answerTime':
            case 'var1':
            case 'var2':
            case 'answer':
            case 'times':
                $v=(int)$v;
                if($v===self::CLIENT_NAN){
                    $v=self::SERVER_NAN;
                }
                break;
            case 'timeOver':
                $v=($v==='true');
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
static public function convertClientToServer(array $cliAssoc):array {
    $srvAssoc=[];
    foreach (self::SERVER_PROPERTY as $value){
        switch ($value){
            case 'username':
            case 'hash':
            case 'qid':
            case 'timeOver':
            case 'timeLimit':
            case 'var1':
            case 'var2':
            case 'answer':
            case 'times':
                $srvAssoc[$value]=$cliAssoc[$value];
                break;
            case 'correct':
                $srvAssoc[$value]=($cliAssoc['var1']*$cliAssoc['var2'])===$cliAssoc['answer'];
                break;
            case 'rt':
                $v=$cliAssoc['answerTime']-$cliAssoc['displayTime'];
                if($v>0){
                    $srvAssoc[$value]=$v;
                }else{
                    $srvAssoc[$value]=self::SERVER_NAN;
                }
                break;
        }
    }
    return $srvAssoc;
}
}