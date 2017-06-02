<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/29/2017
 * Time: 7:23 PM
 */

namespace Hashimoto\Experiment\Model;


class MySQL {
    const HOST = 'localhost';
    const USERNAME = 'vagrant';
    const PASSWD = 'treating2u';
    const DBNAME = 'experiment';
    private $mysqli;

    function __construct() {
        $this->mysqli = mysqli_connect(MySQL::HOST, MySQL::USERNAME, MySQL::PASSWD, MySQL::DBNAME);
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
    }
    public function isUserExist($UID) {
        $stmt = $this->mysqli->prepare("SELECT exists (SELECT * FROM History WHERE uid=?);");
        $stmt->bind_param('s', $UID);
        if($stmt->execute()){$stmt->bind_result($result);
            $stmt->fetch();
        }else{
            $result=false;
        };
        return $result;
    }
    public function registerHistory(History $history){
        list($uid,$protocolJSON,$pointer)=[$history->getUID(),$history->encodeJSONProtocol(),$history->getPointer()];
        $stmt = $this->mysqli->prepare("INSERT INTO History(uid, protocol,pointer) VALUES (?,?,?);");
        $stmt->bind_param('ssi',$uid,$protocolJSON,$pointer);
        $result=$stmt->execute();
        return $result;
    }
    public function deleteHistoryByUID(string $uid){
        $stmt = $this->mysqli->prepare("DELETE FROM History WHERE uid=?;");
        $stmt->bind_param('s',$uid);
        $result=$stmt->execute();
        return $result;
    }
}