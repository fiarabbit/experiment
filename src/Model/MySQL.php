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
        $stmt = $this->mysqli->prepare("SELECT exists (SELECT * FROM Protocol WHERE uid=?);");
        $stmt->bind_param('s', $UID);
        if($stmt->execute()){$stmt->bind_result($result);
            $stmt->fetch();
        }else{
            $result=false;
        };
        return $result;
    }
    public function registerProtocol(Protocol $protocol){
        $uid=$protocol->getUID();
        $protocolString=$protocol->convertString();
        $stmt = $this->mysqli->prepare("INSERT INTO Protocol(uid, protocol) VALUES (?,?);");
        $stmt->bind_param('ss',$uid,$protocolString);
        $result=$stmt->execute();
        return $result;
    }
    public function deleteProtocol(string $uid){
        $stmt = $this->mysqli->prepare("DELETE FROM Protocol WHERE uid=?;");
        $stmt->bind_param('s',$uid);
        $result=$stmt->execute();
        return $result;
    }
}