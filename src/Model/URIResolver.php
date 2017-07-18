<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/20/2017
 * Time: 8:20 PM
 */

namespace Hashimoto\Experiment\Model;


class URIResolver {

    static public function resolve():array {
        $params=[];
        if(''!==$_SERVER['REQUEST_URI']){
            $path=explode('?',$_SERVER['REQUEST_URI'])[0];
            $params=explode('/',$path);
        }
        $controller = $params[1]?:Constant::DEFAULT_CONTROLLER; // login, adjust, ...etc
        $action = !empty($params[2]) ? $params[2] : Constant::DEFAULT_ACTION[$controller]??'';
        return [$controller,$action];
    }
}