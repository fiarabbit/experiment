<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/27/2017
 * Time: 9:06 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\CalcData;
use Hashimoto\Experiment\Model\Constant;
use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;

class ExperimentController {
    private $smarty;

    function __construct() {
        $this->smarty = SmartyExtension::getSmarty();
    }

    public function show() {
        $username = Session::getUsername() ?: $_GET['username']??(function () {
                throw new \Exception('NoUsername');
            })();
        $mysql = new MySQL();
        list($timeLimit, $pointer) = $mysql->fetchUserInfo($username, ['timeLimit','pointer']);
        $timeLimit = (int)$timeLimit;
        $pointer = (int)$pointer;
        $times = Constant::getTimes($pointer);
        $hash = bin2hex(random_bytes(Constant::HASH_LENGTH));
        Cookie::setJSONCookie([
            'username' => $username,
            'times' => $times,
            'hash' => $hash,
            'timeLimit' => $timeLimit
        ]);
        if ($mysql->insertAndUpdateUser($username, ['hash' => $hash], MySQL::UPDATE)) {
            $this->smarty->display('experiment/experiment.tpl');
        } else {
            throw new \Exception('could not update hash');
        }
    }

    public function sendData() {
        try {
            $srvArr = CalcData::convertClientToServer(CalcData::getClientSideData());
            $mysql = new MySQL();
            if ($mysql->insertServerSideCalcData($srvArr)) {
                print_r('success');
            } else {
                print_r('sql error');
            }
        } catch (\Exception $e) {
            if ($e->getMessage() === 'NaN argument error') {
                print_r('invalid argument');
                exit();
            }
            var_dump($e);
        }
    }
    public function finish(){
        $username=Session::getUsername()?:$_GET['username']??(function(){throw new \Exception('NoUsername');})();
        $mysql=new MySQL();
        list($hashSrv,$pointerSrv)=$mysql->fetchUserInfo($username,['hash','pointer']);
        $pointerSrv=(int)$pointerSrv;
        $hashCli=$_GET['hash']??(function(){throw new \Exception('noHash');})();
        $pointerCli=array_search(['experiment',$_GET['times']??(function(){throw new \Exception(('noTimes'));})()],Constant::PROTOCOL);
        //pointerCliをtimesの情報だけから引っ張ってくる
        if($hashSrv!==$hashCli||$pointerSrv!==$pointerCli){
            print_r('invalid hash or pointer');
        }else{
            $mysql->insertHash($username,$pointerSrv,$hashSrv);
            if($mysql->insertAndUpdateUser($username,['hash'=>'','pointer'=>$pointerSrv+1],MySQL::UPDATE)){
                Redirect::redirectByPointer($pointerSrv+1);
            }
        }
    }
}