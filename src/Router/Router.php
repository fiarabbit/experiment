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
use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\Redirect;

class Router {
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';

    const DEFAULT_CONTROLLER = 'login';
    public static function dispatcher()
    {
        // 0. URI分解
        $params = [];
        if ('' !== $_SERVER['REQUEST_URI']) {
            $path = explode('?', $_SERVER['REQUEST_URI'])[0];
            $params = explode('/', $path);
        }
        // URI: [PREFIX]/controller/action
        $controller = $params[1]?:self::DEFAULT_CONTROLLER; // login, experiment, ...etc

        // 1st priority: Information in Session['history']
        if($controller!=='admin'&&Session::checkHistory(Session::SESSION_NAME)){
            try{
                Redirect::redirectThis(Session::getHistory(Session::SESSION_NAME));
            }catch(\Exception $e){
                Session::deleteSession(Session::SESSION_NAME);
                if(!$_COOKIE["reload"]) {
                    Cookie::setJSONCookie(["reload" => true]);
                    Redirect::redirectSelf();
                }else{
                    var_dump($e);
                    exit();
                }
            }
        }
        // else
        if(!isset($params[2])){ // to avoid null action
            Redirect::redirect($controller,'');
        }
        $action = $params[2] ?? null;
        // パラメータより取得したコントローラー名によりクラス振分け
        $controllerInstance = null;
        switch ($controller) {
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
                    default:
                        print_r('invalid action');
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
                    case "adjust":
                        $controllerInstance->adjust();
                        break;
                    default:
                        print_r('invalid action');
                }
                break;
            default:
                print_r("${path} does not exist.");
//                header("HTTP/1.0 404 Not Found");
                exit();
                break;
        }
    }
}