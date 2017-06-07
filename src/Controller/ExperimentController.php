<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/6/2017
 * Time: 3:00 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\SmartyExtension;

class ExperimentController {
    const PREFIX='http://experiment.va';
    private $smarty;
    function __construct() {
        $this->smarty=SmartyExtension::getSmarty();
    }
    public function start(){
        Redirect::redirect('experiment','adjust');
    }
    public function adjust(){
        Cookie::setJSONCookie([
            'var1'=>[71,72,73],
            'var2'=>[7,6,5]
        ]);
        $this->smarty->display('experiment/adjust.tpl');
    }
}