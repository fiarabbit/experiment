<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/2/2017
 * Time: 8:17 PM
 */

namespace Hashimoto\Experiment\Model;


class Redirect {
    const DOMAIN='http://experiment.va';
    static function redirect(string $controller, string $action){
        header(self::DOMAIN . '/' . $controller . $action);
        exit();
    }
}