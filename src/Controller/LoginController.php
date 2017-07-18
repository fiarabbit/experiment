<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/29/2017
 * Time: 3:49 PM
 */

namespace Hashimoto\Experiment\Controller;


use Exception;
use Hashimoto\Experiment\Model\Constant;
use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\History;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;
use Hashimoto\Experiment\Model\UID;

class LoginController {
    const MESSAGE = [
        'continue_error' => 'Server failed to continue your adjust',
        'duplication_error' => 'your name is already used'
    ];
    const USERNAME = 'username';
    private $point;
    private $smarty;

    // constructor
    function __construct() {
        $this->smarty = SmartyExtension::getSmarty();
        $this->point = Constant::getPointByController('login')[0];
    }

    //public function
    public function show($msg = null) { // display
        $msg = $msg ?: (self::MESSAGE[filter_input(INPUT_GET, "MESSAGE")]??'');
        $this->smarty->assign(['msg' => $msg, 'USERNAME' => self::USERNAME]);
        $this->smarty->display('login/login.tpl');
    }

    public function newUser() { // receiver
        $username = filter_input(INPUT_GET, self::USERNAME);
        $mysql = new MySQL();
        if ($pointerArr = $mysql->fetchUserInfo($username, ['pointer'])) {
            $pointer = $pointerArr[0];
            $nextController = Constant::getController($pointer);
        } else {
            if ($mysql->insertAndUpdateUser($username, ['pid' => Constant::pid, 'pointer' => $this->point + 1], MySQL::INSERT)) {
                $nextController = Constant::getController($this->point + 1);
            } else {
                throw new \Exception("MySQL Error: " . $mysql->getErrorMessage());
            };
        };
        Session::setUsername($username);
        Redirect::redirect($nextController, '');
    }
}