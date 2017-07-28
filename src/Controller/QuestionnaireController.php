<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 6/26/2017
 * Time: 12:22 PM
 */

namespace Hashimoto\Experiment\Controller;


use Hashimoto\Experiment\Model\Constant;
use Hashimoto\Experiment\Model\Cookie;
use Hashimoto\Experiment\Model\MySQL;
use Hashimoto\Experiment\Model\QuesData;
use Hashimoto\Experiment\Model\Redirect;
use Hashimoto\Experiment\Model\Session;
use Hashimoto\Experiment\Model\SmartyExtension;

class QuestionnaireController {
    private $smarty;

    function __construct() {
        $this->smarty = SmartyExtension::getSmarty();
    }

    public function show() {
        $username = Session::getUsername() ?: $_GET['username']?? (function () {
                throw new \Exception('NoUsername');
            })();
        $mysql = new MySQL();
        list($pointer, $qid) = $mysql->fetchUserInfo($username, ['pointer', 'qid']);
        $qid = $qid??0;
        $times = Constant::getTimes($pointer);
        $hash = bin2hex(random_bytes(Constant::HASH_LENGTH));
        list($arrArr,$answerChoice)= $mysql->getQuestionnaire();
        $this->smarty->assign([
            'arrArr' => $arrArr,
            'answerChoice' => $answerChoice
        ]);
        Cookie::setJSONCookie([
            'username' => $username,
            'times' => $times,
            'qid' => $qid,
            'hash' => $hash
        ]);
        if ($mysql->insertAndUpdateUser($username, ['hash' => $hash], MySQL::UPDATE)) {
            $this->smarty->display('questionnaire/questionnaire.tpl');
        } else {
            throw new \Exception('could not update hash');
        }
    }

    public function sendData() {
        try {
            $srvArr = QuesData::convertClientToServer(QuesData::getClientSideData());
            $mysql = new MySQL();
            if ($mysql->insertServerSideQuesData($srvArr)) {
                print_r('success');
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
        $username = Session::getUsername() ?: $_GET['username']??(function () {
                throw new \Exception('NoUsername');
            })();
        $mysql = new MySQL();
        $hashSrv = $mysql->fetchUserInfo($username, ['hash'])[0];
        $hashCli = $_GET['hash']??(function () {
                throw new \Exception('noHash');
            })();
        if ($hashSrv !== $hashCli) {
            print_r('invalid hash');
        } else {
            $pointerSrv = (int)$mysql->fetchUserInfo($username, ['pointer'])[0];
            $timesCli = (int)$_GET['times']??(function () {
                    throw new \Exception('invalid times');
                })();
            $pointerCli = array_search(['questionnaire', $timesCli], Constant::PROTOCOL, true);
            if ($pointerSrv !== $pointerCli) {
                throw new \Exception('inconsistent pointer');
            }
            $mysql->insertHash($username, $pointerCli, $hashSrv);
            if ($mysql->insertAndUpdateUser($username, ['pointer' => $pointerCli + 1, 'hash' => ''], MySQL::UPDATE)) {
                Redirect::redirectByPointer($pointerCli + 1);
            } else {
                throw new \Exception('could not update');
            };
        }
    }
}