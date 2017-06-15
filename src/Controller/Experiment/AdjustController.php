<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/8/2017
 * Time: 7:02 PM
 */

namespace Hashimoto\Experiment\Controller\Experiment;


use Hashimoto\Experiment\Model\CalcData;
use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\MySQL;
use Smarty;

class AdjustController {
    private $smarty;

    function __construct(smarty &$smarty) {
        $this->smarty = $smarty;
    }

    public function show() {
        Cookie::setJSONCookie([
            'uid' => 'hashimoto'
        ]);
        $this->smarty->display('experiment/adjust.tpl');
    }

    public function sendData() {
        try {
            $srvArr = CalcData::convertClientToServer(CalcData::getClientSideData());
            $sql = new MySQL();
            if ($sql->registerServerSideData($srvArr)) {
                print_r('success');
            } else {
                print_r('sql error');
            }
        } catch (\Exception $e) {
            if($e->getMessage()==='NaN argument error'){
                print_r('invalid argument');
                exit();
            }
            var_dump($e);
        }
    }
}