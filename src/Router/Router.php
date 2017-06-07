<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/29/2017
 * Time: 3:01 PM
 */

namespace Hashimoto\Experiment\Router;

use Hashimoto\Experiment\Controller\AdminController;
use Hashimoto\Experiment\Controller\ExperimentController;
use Hashimoto\Experiment\Controller\LoginController;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\Redirect;

class Router {
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    public static function dispatcher()
    {
        // 0. URI分解
        $params = [];
        if ('' !== $_SERVER['REQUEST_URI']) {
            $path = explode('?', $_SERVER['REQUEST_URI'])[0];
            $params = explode('/', $path);
        }
        // /controller/action
        $controller = $params[1] ?: 'index'; // login, experiment, "else" index
        if(!isset($params[2])){ // to avoid null action
            Redirect::redirect($controller,'');
        }
        $action = $params[2] ?? null;
        if(Session::checkHistory(Session::SESSION_NAME)){
            Redirect::redirect('experiment','continue');
        };
        // パラメータより取得したコントローラー名によりクラス振分け
        $controllerInstance = null;
        switch ($controller) {
            case 'index':
                if(Session::checkHistory(Session::SESSION_NAME)){
                    Redirect::redirect('experiment','continue');
                };
                print_r('index was called');
                break;
            case 'login':
                $controllerInstance = new LoginController();
                if(in_array($action,[null,''],true)){
                    $action="show";
                }
                switch ($action) {
                    case "show":
                        $controllerInstance->show();
                        break;
                    case "newUser":
                        $controllerInstance->newUser(); // $_GETからパラメータを入手して登録する
                        break;
                    case "deleteUser":
                        $controllerInstance->deleteUser();
                        break;
                    default:
                    throw new \Exception('invalid action parameter');
                }
                break;
            case 'admin':
                $controllerInstance = new AdminController();
                $action=$action?:"showIndex";
                switch ($action){
                    case "showIndex":
                        $controllerInstance->showIndex();
                        break;
                    case "showDatabase":
                        $controllerInstance->showDatabase();
                        break;
                    case "deleteUser":
                        $controllerInstance->deleteUser();
                        break;
                    case "deleteSession":
                        $controllerInstance->deleteSession();
                        print_r('Session has been deleted');
                        break;

                    default:
                        print_r('invalid action');
                }
                break;
            case 'experiment':
                $controllerInstance = new ExperimentController();
                switch ($action){
                    case "start":
                        $controllerInstance->start();
                        break;
                    case "adjust":
                        $controllerInstance->adjust();
                        break;
                    default:
                        print_r('invalid action');
                }
                break;
            default:
                print_r($path);
//                header("HTTP/1.0 404 Not Found");
                exit();
                break;
        }
    }
}