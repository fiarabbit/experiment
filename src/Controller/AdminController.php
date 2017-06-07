<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/2/2017
 * Time: 8:23 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;
use Hashimoto\Experiment\Model\UID;

class AdminController {
    const ACTION = [
        'showIndex',
        'showDatabase',
        'deleteSession',
        'deleteUser'
    ];
    const PREFIX='http://experiment.va';
    private $smarty;

    //constructor
    function __construct() {
        $this->smarty=SmartyExtension::getSmarty();
        $this->smarty->assign([
            "ACTION"=>self::ACTION,
            "PREFIX"=>self::PREFIX
        ]);
    }

    public function showIndex(){
        $this->smarty->display('admin/showIndex.tpl');
    }
    public function deleteUser(){ //for administration
        $mysql=new MySQL();
        try {
            $uid = UID::check(filter_input(INPUT_GET, "UID"));
            if($mysql->isUserExist($uid)){
                $mysql->deleteHistoryByUID($uid);
                print_r($uid ." has been deleted");
            }
        }catch(\Exception $e){
            if($e->getMessage()==='NullStringError'){
                $arr=$mysql->getAllUID();
                $this->smarty->assign([
                    "arr"=>$arr
                ]);
                $this->smarty->display('admin/deleteUser.tpl');
            }
        }
    }
    public function deleteSession() {
        Session::deleteSession(Session::SESSION_NAME);
    }

    public function showDatabase() {
        $mysql = new MySQL();
        $arr = $mysql->getAllHistory();
        print_r("All histories:");
        var_dump($arr);
    }
}