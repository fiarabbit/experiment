<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/2/2017
 * Time: 8:23 PM
 */

namespace Hashimoto\Experiment\Controller;


class AdminController {
public function clearSession(){
    Session::deleteSession(Session::SESSION_NAME);
}
}