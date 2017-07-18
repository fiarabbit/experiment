<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/2/2017
 * Time: 8:23 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\Constant;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;

class AdminController {
    const ACTION = [
        'showIndex',
        'showDatabase',
        'deleteSession',
        'deleteUser'
    ];
    const USERNAME_ALIAS='username';
    private $smarty;

    //constructor
    function __construct() {
        $this->smarty=SmartyExtension::getSmarty();
        $this->smarty->assign([
            "ACTION"=>self::ACTION,
            "USERNAME_ALIAS"=>self::USERNAME_ALIAS,
            "MESSAGE"=>$_GET['MESSAGE']??''
        ]);
    }

    public function showIndex(){
        $this->smarty->display('admin/showIndex.tpl');
    }
    public function deleteUser(){ //for administration
        $mysql=new MySQL();
        switch(filter_input(INPUT_SERVER,'REQUEST_METHOD')){
            case 'GET':
                $this->smarty->assign('MESSAGE','');
                break;
            case 'POST':
                $username=filter_input(INPUT_POST,self::USERNAME_ALIAS);
                if($mysql->deleteUser($username)){
                    $this->smarty->assign('MESSAGE','Deleted ' . $username . ' .');
                }else{
                    $this->smarty->assign('MESSAGE','Unable to delete ' . $username . ' .');
                }
                break;
            default:
                header("HTTP/1.0 404 Not Found");
                exit();
        }
        $this->smarty->assign('arr',$mysql->getAllUsername());
        $this->smarty->display('admin/deleteUser.tpl');
    }
    public function deleteSession() {
        Session::deleteSession();
        Redirect::redirectWithParameter('admin','',['MESSAGE'=>'Session has been deleted.']);
    }
}