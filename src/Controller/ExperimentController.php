<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/6/2017
 * Time: 3:00 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Controller\Experiment\AdjustController;
use Hashimoto\Experiment\Model\SmartyExtension;

class ExperimentController {
    const PREFIX='http://experiment.va';
    private $smarty;
    function __construct() {
        $this->smarty=SmartyExtension::getSmarty();
    }
    public function adjust(){
        $smarty=$this->smarty;
        $controller=new AdjustController($smarty);
        $params=[];
        if ('' !== $_SERVER['REQUEST_URI']) {
            $path = explode('?', $_SERVER['REQUEST_URI'])[0];
            $params = explode('/', $path);
        }
        $action=empty($params[3])?'show':$params[3];
        switch($action){
            case 'show':
                $controller->show();
                break;
            case 'sendData':
                $controller->sendData();
                break;
            default:
                print_r('invalid action parameter');
        }
    }
}