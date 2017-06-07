<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/2/2017
 * Time: 8:17 PM
 */

namespace Hashimoto\Experiment\Model;


class Redirect {
    const PREFIX='http://experiment.va';
    static function redirect(string $controller, string $action){
        header('Location: ' . self::PREFIX . '/' . $controller . '/' . $action);
        exit();
    }
    static function redirectWithParameter(string $controller, string $action, array $queryData){
        $query=http_build_query($queryData);
        header('Location: ' . self::PREFIX . '/' . $controller . '/' . $action . '?' . $query);
    }
}