<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 7/3/2017
 * Time: 7:50 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;

class FinishController {
    private $smarty;

    function __construct() {
        $this->smarty=SmartyExtension::getSmarty();
    }

    public function show() {
        Session::deleteSession();
        Cookie::clearAllCookie();
        $this->smarty->display('finish/finish.tpl');
    }
}