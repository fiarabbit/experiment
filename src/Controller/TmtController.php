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
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;

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
        $this->smarty->assign(["username"=>$username, "times"=>$times, "hash"=>$hash]);
        if ($mysql->insertAndUpdateUser($username, ['hash' => $hash], MySQL::UPDATE)){
            $this->smarty->display('tmt/tmt.tpl');
        } else {
            throw new \Exception('could not update hash');
        }
    }

    public function sendData() {

    }

}