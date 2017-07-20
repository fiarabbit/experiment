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
        'deleteUser',
        'setPointer'
    ];
    const USERNAME_ALIAS = 'username';
    const POINTER_ALIAS = 'pointer';
    const TIMELIMIT_ALIAS = 'timeLimit';
    private $smarty;

    //constructor
    function __construct() {
        $this->smarty = SmartyExtension::getSmarty();
        $this->smarty->assign([
            "ACTION" => self::ACTION,
            "USERNAME_ALIAS" => self::USERNAME_ALIAS,
            "POINTER_ALIAS" => self::POINTER_ALIAS,
            "TIMELIMIT_ALIAS" => self::TIMELIMIT_ALIAS,
            "MESSAGE" => $_GET['MESSAGE']??''
        ]);
    }

    public function showIndex() {
        $this->smarty->display('admin/showIndex.tpl');
    }

    public function deleteUser() { //for administration
        $mysql = new MySQL();
        switch (filter_input(INPUT_SERVER, 'REQUEST_METHOD')) {
            case 'GET':
                $this->smarty->assign('MESSAGE', '');
                break;
            case 'POST':
                $username = filter_input(INPUT_POST, self::USERNAME_ALIAS);
                if ($mysql->deleteUser($username)) {
                    $this->smarty->assign('MESSAGE', 'Deleted ' . $username . ' .');
                } else {
                    $this->smarty->assign('MESSAGE', 'Unable to delete ' . $username . ' .');
                }
                break;
            default:
                header("HTTP/1.0 404 Not Found");
                exit();
        }
        $this->smarty->assign('arr', $mysql->getAllUsername());
        $this->smarty->display('admin/deleteUser.tpl');
    }

    public function setPointer() {
        $mysql = new MySQL();
        switch (filter_input(INPUT_SERVER, 'REQUEST_METHOD')) {
            case 'GET':
                $this->smarty->assign('MESSAGE', '');
                break;
            case 'POST':
                $username = filter_input(INPUT_POST, self::USERNAME_ALIAS);
                $pointer = (int)filter_input(INPUT_POST, self::POINTER_ALIAS);
                $timeLimit=(int)filter_input(INPUT_POST, self::TIMELIMIT_ALIAS);
                if ($mysql->isUserExist($username)
                    && $mysql->insertAndUpdateUser($username, ['pointer' => $pointer], MySQL::UPDATE)
                ) {
                    if($timeLimit!==null){
                        $mysql->insertAndUpdateUser($username,['timeLimit'=>$timeLimit], MySQL::UPDATE);
                    }
                    Session::deleteSession();
                    Session::setUsername($username);
                    Redirect::redirectByPointer($pointer);
                } else {
                    $this->smarty->assign('MESSAGE', 'NoUser');
                }
                break;
            default:
                header("HTTP/1.0 404 Not Found");
                exit();
        }
        $this->smarty->assign('arr', $mysql->getAllUsername());
        $this->smarty->display('admin/setPointer.tpl');
    }

    public function deleteSession() {
        Session::deleteSession();
        Redirect::redirectWithParameter('admin', '', ['MESSAGE' => 'Session has been deleted.']);
    }
}