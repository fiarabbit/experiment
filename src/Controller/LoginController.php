<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/29/2017
 * Time: 3:49 PM
 */

namespace Hashimoto\Experiment\Controller;


use Exception;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\History;
use Hashimoto\Experiment\Model\UID;

class LoginController {
    const MESSAGE=[
        'continue_error'=>'Server failed to continue your experiment',
        'duplication_error'=>'your name is already used'
    ];
    private $smarty;

    // constructor
    function __construct() {
        $this->smarty=new \Smarty();
        $this->smarty->setTemplateDir(__DIR__ . "/../View");
    }

    //private function
    private function showMessage($msg){
        $this->smarty->assign("msg",$msg);
        $this->smarty->display('login/login.tpl');
    }

    //public function
    public function show($msg=null){ // display
        $msg=$msg?:(self::MESSAGE[filter_input(INPUT_GET,"MESSAGE")]??'');
        $this->smarty->assign("msg",$msg);
        $this->smarty->display('login/login.tpl');
    }


    public function newUser(){ // receiver
        $history=new History(UID::check(filter_input(INPUT_GET,"UID")));
        $mysql=new MySQL();
        if(!$mysql->isUserExist($history->getUID())){ // if not exist
            try{
                $mysql->registerHistory($history);
                print_r("registration succeeded");
                Session::setHistory(Session::SESSION_NAME,$history);//todo redirect
            }catch (Exception $e){
                var_dump($e);
            }
        }else{
            header('Location: http://experiment.va/login/?MESSAGE=duplication_error');
            exit();
        };
    }
    public function deleteUser(){ //for administration
        $uid=UID::check(filter_input(INPUT_GET,"UID"));
        $mysql=new MySQL();
        if($mysql->isUserExist($uid)){
           $mysql->deleteHistoryByUID($uid);
        }
    }
}