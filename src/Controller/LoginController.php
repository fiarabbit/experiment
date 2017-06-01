<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/29/2017
 * Time: 3:49 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\Protocol;
use Hashimoto\Experiment\Model\UID;

class LoginController {
    const MESSAGE=[
        'continue_error'=>'Server failed to continue your experiment',
        'duplication_error'=>'your name is already used'
    ];
    private $smarty;

    function __construct() {
        $this->smarty=new \Smarty();
        $this->smarty->setTemplateDir(__DIR__ . "/../View");
    }
    private function showMessage($msg){
        $this->smarty->assign("msg",$msg);
        $this->smarty->display('login/login.tpl');
    }
    public function show($msg=null){
        $msg=$msg?:(self::MESSAGE[filter_input(INPUT_GET,"MESSAGE")]??'');
        $this->smarty->assign("msg",$msg);
        $this->smarty->display('login/login.tpl');
    }
    public function newUser(){
        $Protocol=new Protocol(UID::check(filter_input(INPUT_GET,"UID")));
        $mysql=new MySQL();
        if(!$mysql->isUserExist($Protocol->getUID())){ // if not exist
            $mysql->registerProtocol($Protocol);
            print_r("registration succeeded");//todo redirect
        }else{
            $query=http_build_query(['MESSAGE'=>self::MESSAGE['duplication_error']]);
            header('Location: http://experiment.va/login/?MESSAGE=duplication_error');
            exit();
        };
    }
    public function deleteUser(){
        $uid=UID::check(filter_input(INPUT_GET,"UID"));
        $mysql=new MySQL();
        if($mysql->isUserExist($uid)){
           $mysql->deleteProtocol($uid);
        }
    }
}