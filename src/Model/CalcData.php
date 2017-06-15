<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/13/2017
 * Time: 7:09 PM
 */

namespace Hashimoto\Experiment\Model;


class CalcData {
    const CLIENT_PROPERTY=[
        'uid',
        'qid',
        'displayTime',
        'answerTime',
        'var1',
        'var2',
        'answer'
    ];
    const SERVER_PROPERTY=[
        'uid',
        'qid',
        'var1',
        'var2',
        'answer',
        'correct',
        'rt'
    ];
static public function getClientSideData():array {
    $cliArr=[];
    foreach (self::CLIENT_PROPERTY as $value){
        $v=filter_input(INPUT_GET,$value);
        if($v=='NaN'){
            throw new \InvalidArgumentException('NaN argument error');
        }
        switch ($value){
            case 'qid':
            case 'displayTime':
            case 'answerTime':
            case 'var1':
            case 'var2':
            case 'answer':
                $v=(int)$v;
                break;
            default:
                break;
        }
        $cliArr[$value]=$v;
    }
    return $cliArr;
}
static public function convertClientToServer(array $cliArr):array {
    $srvArr=[];
    foreach (self::SERVER_PROPERTY as $value){
        switch ($value){
            case 'uid':
            case 'qid':
            case 'var1':
            case 'var2':
            case 'answer':
                $srvArr[$value]=$cliArr[$value];
                break;
            case 'correct':
                $srvArr[$value]=($cliArr['var1']*$cliArr['var2'])===$cliArr['answer'];
                break;
            case 'rt':
                $srvArr[$value]=($cliArr['answerTime']-$cliArr['displayTime']);
                break;
        }
    }
    return $srvArr;
}
}