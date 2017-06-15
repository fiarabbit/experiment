<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/6/2017
 * Time: 3:43 PM
 */

namespace Hashimoto\Experiment\Model;


class SmartyExtension {
    const PREFIX = 'http://experiment.va';
    static public function getSmarty(){
        $smarty=new \Smarty();
        $smarty->assign('PREFIX',self::PREFIX);
        $smarty->setTemplateDir(__DIR__ . '/../View');
        return $smarty;
    }
}