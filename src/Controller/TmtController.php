<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 2018/01/19
 * Time: 2:09
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\Constant;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;
use Hashimoto\Experiment\Model\TmtData;

class TmtController {
    private $smarty;

    function __construct() {
        $this->smarty = SmartyExtension::getSmarty();
    }

    public function show(){
        $username = $_GET['username'] ?? Session::getUsername() ?: (function(){
            throw  new \Exception('NoUsername');
            });
        $mysql = new MySQL();
        $pointer = (int)$mysql->fetchUserInfo($username, ['pointer'])[0];
        $times = Constant::getTimes($pointer);
        $hash = bin2hex(random_bytes(Constant::HASH_LENGTH));
        if ($times == 0){
            $targetnumber = 6;
        } else{
            $targetnumber = 26;
        }
        $this->smarty->assign(["username"=>$username, "times"=>$times, "hash"=>$hash, "targetnumber"=>$targetnumber]);
        if ($mysql->insertAndUpdateUser($username, ['hash' => $hash], MySQL::UPDATE)){
            $this->smarty->display('tmt/tmt.tpl');
        } else {
            throw new \Exception('could not update hash');
        }
    }

    public function sendData() {
        try {
            $srvArr = TmtData::convertClientToServer(TmtData::getClientSideData());
            $mysql = new MySQL();
            if ($mysql->insertServerSideTmtData($srvArr)){
                print_r('success');
            } else{
                print_r('sql error');
            }
        } catch (\Exception $e){
            var_dump($e);
            exit();
        }
    }

    public function finish(){
        $username = $_GET['username'] ?? Session::getUsername() ?: (function(){throw new \Exception('NoUsername');})();
        $mysql = new MySQL();
        list($hashSrv, $pointerSrv) = $mysql->fetchUserInfo($username, ['hash', 'pointer']);
        $hashCli = $_GET['hash'] ?? (function(){throw new \Exception('noHash');})();
        $pointerCli = array_search(['tmt', $_GET['times']??(function(){throw new \Exception(('noTimes'));})()], Constant::PROTOCOL);
        if ($hashSrv!==$hashCli || $pointerSrv!==$pointerCli){
            print_r('invalid hash or pointer');
        } else {
            $mysql->insertHash($username, $pointerSrv, $hashSrv);
            if($mysql->insertAndUpdateUser($username, ['hash'=>''], MySQL::UPDATE)){
                Redirect::redirectByPointer($pointerSrv+1);
            };

        }

    }
}