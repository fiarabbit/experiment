<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/8/2017
 * Time: 7:02 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\CalcData;
use Hashimoto\Experiment\Model\Constant;
use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\History;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;
use Smarty;

class AdjustController {
    private $smarty;
    private $point;
    const TIMES=0; // adjust is once only, so always zero
    function __construct() {
        $this->smarty=SmartyExtension::getSmarty();
        $this->point=Constant::getPointByController('adjust')[self::TIMES];
    }

    public function show() {
        $username=Session::getUsername()?:$_GET['username']?? (function(){throw new \Exception('NoUsername');})();
        $hash=bin2hex(random_bytes(Constant::HASH_LENGTH));
        Cookie::setJSONCookie([
            'username'=>$username,
            'times'=>self::TIMES,
            'hash'=>$hash
        ]);
        $mysql=new MySQL();
        if($mysql->insertAndUpdateUser($username,['hash'=>$hash],MySQL::UPDATE)){
            $this->smarty->display('adjust/adjust.tpl');
        }else{
            throw new \Exception('could not update hash');
        };

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
            if($e->getMessage()==='NaN argument error'){
                print_r('invalid argument');
                exit();
            }
            var_dump($e);
        }
    }
    public function finish(){
        //0. 重複を殺す
        //0.0 hash値をMySQL->Userに登録しようとする
        $username=Session::getUsername()?:$_GET['username']??(function(){throw new \Exception('NoUsername');})();
        $mysql=new MySQL();
        $hashSrv=$mysql->fetchUserInfo($username,['hash'])[0];
        $hashCli=$_GET['hash']??(function(){throw new \Exception('noHash');})();
        if($hashSrv!==$hashCli){
            print_r('invalid hash');
        }else {
            $mysql->insertAndUpdateUser($username,['hash'=>''],MySQL::UPDATE);
            // そのハッシュ値の実験の最終問題のtimeLimitを見る
            $mysql->insertHash($username,$this->point,$hashSrv);
            if(!$timeLimit = $mysql->getTimeLimit($hashSrv)){throw new \Exception('invalid interval');};
            if($mysql->insertAndUpdateUser($username,['timeLimit'=>$timeLimit,'pointer'=>$this->point+1],MySQL::UPDATE)){
                Redirect::redirectByPointer($this->point+1);
            }
            else{
                throw new \Exception('could not update');
            };
        }
    }
}