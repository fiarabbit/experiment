<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/2/2017
 * Time: 8:17 PM
 */

namespace Hashimoto\Experiment\Model;


class Redirect {
    static function redirect(string $controller, string $action) {
        header('Location: ' . '/' . $controller . '/' . $action);
        exit();
    }

    static function redirectWithParameter(string $controller, string $action, array $queryData) {
        $query = http_build_query($queryData);
        header('Location: ' . '/' . $controller . '/' . $action . '?' . $query);
        exit();
    }

    static function redirectByPointer($pointer) {
        $controller = Constant::getController($pointer);
        $action=Constant::DEFAULT_ACTION[$controller];
        $times = Constant::getTimes($pointer);
        header('Location: ' . '/' . $controller . '/' . $action . '?times=' . $times);
        exit();
    }
}
