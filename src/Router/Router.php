<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/29/2017
 * Time: 3:01 PM
 */

namespace Hashimoto\Experiment\Router;

use Hashimoto\Experiment\Controller\AdminController;
use Hashimoto\Experiment\Controller\AdjustController;
use Hashimoto\Experiment\Controller\ExperimentController;
use Hashimoto\Experiment\Controller\FinishController;
use Hashimoto\Experiment\Controller\LoginController;
use Hashimoto\Experiment\Controller\QuestionnaireController;
use Hashimoto\Experiment\Controller\AnotherController;
use Hashimoto\Experiment\Controller\TmtController;
use Hashimoto\Experiment\Model\Constant;
use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\URIResolver;

class Router {
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';

    /**
     *
     */
    public static function dispatcher() {
        // 0. URI分解
        list($controller, $action) = URIResolver::resolve();

        if (!in_array($controller, ['admin', 'login'])) {
            if (Constant::DEFAULT_ACTION[$controller]??null === $action) {
                $mysql = new MySQL();
                if ($pointer = (int)$mysql->fetchUserInfo(Session::getUsername(), ['pointer'])[0]) {
                    if ($controller !== Constant::getController($pointer)) {
                        Redirect::redirectByPointer($pointer);
                    }
                }
            }
        }
        // パラメータより取得したコントローラー名によりクラス振分け
        $controllerInstance = null;
        try {
            switch ($controller) {
                case 'login':
                    $controllerInstance = new LoginController();
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
                    switch ($action) {
                        case "showIndex":
                            $controllerInstance->showIndex();
                            break;
                        case "deleteUser":
                            $controllerInstance->deleteUser();
                            break;
                        case "deleteSession":
                            $controllerInstance->deleteSession();
                            print_r('Session has been deleted');
                            break;
                        case "setPointer":
                            $controllerInstance->setPointer();
                            break;
                        default:
                            print_r('invalid action');
                    }
                    break;
                case 'adjust':
                    $controllerInstance = new AdjustController();
                    $action = $action ?: "show";
                    switch ($action) {
                        case "show":
                            $controllerInstance->show();
                            break;
                        case "sendData":
                            $controllerInstance->sendData();
                            break;
                        case "finish":
                            $controllerInstance->finish();
                            break;
                        default:
                            print_r('invalid action');
                    }
                    break;
                case 'questionnaire':
                    $controllerInstance = new QuestionnaireController();
                    $action = $action ?: 'show';
                    switch ($action) {
                        case 'show':
                            $controllerInstance->show();
                            break;
                        case 'sendData':
                            $controllerInstance->sendData();
                            break;
                        case "finish":
                            $controllerInstance->finish();
                            break;
                        default:
                            print_r('invalid action');
                    }
                    break;
                case 'another':
                    $controllerInstance = new AnotherController();
                    $action = $action ?: 'show';
                    switch ($action) {
                        case 'show':
                            $controllerInstance->show();
                            break;
                        case 'start':
                            $controllerInstance->start();
                            break;
                        case 'finish':
                            $controllerInstance->finish();
                            break;
                        default:
                            print_r('invalid action');
                    }
                    break;
                case 'experiment':
                    $controllerInstance = new ExperimentController();
                    $action = $action ?: 'show';
                    switch ($action) {
                        case 'show':
                            $controllerInstance->show();
                            break;
                        case 'sendData':
                            $controllerInstance->sendData();
                            break;
                        case 'finish':
                            $controllerInstance->finish();
                            break;
                        default:
                            print_r('invalid action');
                    }
                    break;
                case 'finish':
                    $controllerInstance = new FinishController();
                    $action = $action ?: 'show';
                    switch ($action) {
                        case 'show':
                            $controllerInstance->show();
                            break;
                        default:
                            print_r('invalid action');
                    }
                    break;
                case 'tmt':
                    $controllerInstance = new TmtController();
                    $action = $action ?: 'show';
                    switch ($action) {
                        case 'show':
                            $controllerInstance->show();
                            break;
                        case 'sendData':
                            $controllerInstance->sendData();
                            break;
                        case 'finish':
                            $controllerInstance->finish();
                            break;
                        default:
                            print_r('invalid action');
                    }
                    break;
                default:
                    print_r("${controller} does not exist.");
//                header("HTTP/1.0 404 Not Found");
                    exit();
                    break;
            }
        }catch(\Exception $e){
            var_dump($e);exit();
//            switch($e->getMessage()){
//                case 'NoUsername':
//                    $controller=Constant::DEFAULT_CONTROLLER;
//                    $action=Constant::DEFAULT_ACTION[$controller];
//                    Redirect::redirect($controller,$action);
//            }
        }
    }
}