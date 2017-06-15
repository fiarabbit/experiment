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
    public function isUserExist($UID):bool {
        $stmt = $this->mysqli->prepare("SELECT exists (SELECT * FROM History WHERE uid=?);");
        $stmt->bind_param('s', $UID);
        if($stmt->execute()){
            $stmt->bind_result($result);
            $stmt->fetch();
        }else{
            $result=false;
        }
        return $result;
    }

    public function registerHistory(History $history):bool{
        list($uid,$protocolJSON,$pointer)=[$history->getUID(),$history->encodeJSONProtocol(),$history->getPointer()];
        $stmt = $this->mysqli->prepare("INSERT INTO History(uid, protocol,pointer,unixmilli) VALUES (?,?,?,ROUND(UNIX_TIMESTAMP(CURTIME(4)) * 1000));");
        $stmt->bind_param('ssi',$uid,$protocolJSON,$pointer);
        $result=$stmt->execute();
        return $result;
    }
    public function registerServerSideData(array $srvArr):bool{
        list($uid,$qid,$var1,$var2,$answer,$correct,$rt)=[$srvArr['uid'],$srvArr['qid'],$srvArr['var1'],$srvArr['var2'],$srvArr['answer'],$srvArr['correct'],$srvArr['rt']];
        $stmt = $this->mysqli->prepare("INSERT INTO Adjust(uid, qid, var1, var2, answer, correct, rt, unixmilli) VALUES (?,?,?,?,?,?,?,ROUND(UNIX_TIMESTAMP(CURTIME(4))*1000));");
        $stmt->bind_param('siiiiii',$uid,$qid,$var1,$var2,$answer,$correct,$rt);
        $result=$stmt->execute();
        return $result;
    }
    public function deleteHistoryByUID(string $uid):bool{
        $stmt = $this->mysqli->prepare("DELETE FROM History WHERE uid=?;");
        $stmt->bind_param('s',$uid);
        $result=$stmt->execute();
        return $result;
    }

    // deprecated
    //    public function getCurrentPointerByUID(string $uid){
    //        $stmt=$this->mysqli->prepare("SELECT MAX(pointer) FROM History WHERE uid=?");
    //        $stmt->bind_param('s',$uid);
    //        if($stmt->execute()){
    //            $stmt->bind_result($result);
    //            $stmt->fetch();
    //        }else{
    //            $result=false;
    //        }
    //        return $result;
    //    }

    // below: just for debug
    public function getAllHistory():array {
        $arr=[];
        if($result=$this->mysqli->query("SELECT * FROM History")){
            while($row=$result->fetch_assoc()){
                array_push($arr,$row);
            }
        }
        return $arr;
    }

    public function getAllUID():array {
        $arr=[];
        if($result=$this->mysqli->query("SELECT uid FROM History")){
            while($row=$result->fetch_row()){
                array_push($arr,$row[0]);
            }
        }
        return $arr;
    }
}