<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/27/2017
 * Time: 1:05 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\Constant;
use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;
use Hashimoto\Experiment\Model\AnotherData;

class AnotherController {
    private $smarty;

    function __construct() {
        $this->smarty = SmartyExtension::getSmarty();
    }

    public function show() {
        $username = Session::getUsername() ?: $_GET['username']??(function () {
                throw new \Exception('NoUsername');
            })();
        $mysql = new MySQL();
        $pointer = (int)$mysql->fetchUserInfo($username, ['pointer'])[0];
        $times = Constant::getTimes($pointer);
        $hash = bin2hex(random_bytes(Constant::HASH_LENGTH));
        Cookie::setJSONCookie([
            'username' => $username,
            'times' => $times,
            'hash' => $hash
        ]);
        if ($mysql->insertAndUpdateUser($username, ['hash' => $hash], MySQL::UPDATE)) {
            $this->smarty->display('another/another.tpl');
        } else {
            throw new \Exception('could not update hash');
        }
    }

    public function start($type = 'start') {
        try {
            // ClientSide
            $clientSideData = AnotherData::getClientSideData();
            $username = $clientSideData['username'];
            $timesCli = $clientSideData['times'];
            $pointerCli = array_search(['another', $timesCli], Constant::PROTOCOL);
            $hashCli = $clientSideData['hash'];

            //ServerSide
            $mysql = new MySQL();
            list($pointerSrv, $hashSrv) = $mysql->fetchUserInfo($username, ['pointer', 'hash']);
            $pointerSrv = (int)$pointerSrv;

            //Consistency Check
            if ($hashSrv !== $hashCli) {
                throw new \Exception('invalid hash pair');
            }
            if ($pointerSrv !== $pointerCli) {
                throw new \Exception('invalid pointer pair');
            }

            $srvArr = AnotherData::convertClientToServer($clientSideData);
            $srvArr['typeid'] = ($type==='finish');
            if ($mysql->insertServerSideAnotherData($srvArr)) {
                $mysql->insertHash($username, $pointerSrv, $hashSrv);
                if ($type === 'start') {
                    print_r('success');
                } else if ($type === 'finish') {
                    $mysql->insertAndUpdateUser($username,['hash'=>'','pointer'=>$pointerSrv+1],MySQL::UPDATE);
                    Redirect::redirectByPointer($pointerSrv + 1);
                }
            } else {
                print_r('sql error');
            }
        } catch (\Exception $e) {
            if ($e->getMessage() === 'NaN argument error') {
                print_r('invalid argument');
                exit();
            }
            var_dump($e);
        }
    }

    public function finish() {
        $this->start('finish');
    }
}