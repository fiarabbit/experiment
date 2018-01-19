<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/29/2017
 * Time: 7:23 PM
 */

namespace Hashimoto\Experiment\Model;

/**
 * Class MySQL
 * @package Hashimoto\Experiment\Model MySQLへのアクセスはすべてここを通す．
 * MySQL
 * User…Userの固有の情報だけが登録されている．
 */
class MySQL {
    const HOST = 'localhost';
    const USERNAME = 'www-data';
    const PASSWD = 'php_password';
    const DBNAME = 'experiment';
    const INSERT = 1;
    const UPDATE = 2;
    const DUPLICATE = 4;
    private $mysqli;
    private $errorMessage;

    function __construct() {
        $this->mysqli = mysqli_connect(MySQL::HOST, MySQL::USERNAME, MySQL::PASSWD, MySQL::DBNAME);
        $this->mysqli->set_charset("utf8");
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
    }

    private function escapeValue($value) {
        if(is_bool($value)){
            return (string)$value;
        }else {
            return '"' . mysqli_real_escape_string($this->mysqli, (string)$value) . '"';
        }
    }

    private function escapeColumn($columnName) {
        return "`${columnName}`";
    }

    private function assocToINSERT(array $assoc, string $table): string {
        $c = '';
        $v = '';
        foreach ($assoc as $key => $value) {
            $c = $c . $this->escapeColumn($key) . ',';
            $v = $v . $this->escapeValue($value) . ',';
        }
        $c = substr($c, 0, -1);
        $v = substr($v, 0, -1);
        return "INSERT INTO ${table} (${c}) VALUES (${v});";
    }

    private function assocToUPDATE(array $assoc, string $specifyingColName, string $table): string {
        $q = "UPDATE ${table} SET ";
        foreach ($assoc as $key => $value) {
            $k = $this->escapeColumn($key);
            $v = $this->escapeValue($value);
            $q = $q . "${k}=${v},";
        }
        $k = $this->escapeColumn($specifyingColName);
        $v = $this->escapeValue($assoc[$specifyingColName]);
        $q = substr($q, 0, -1) . " WHERE ${k}=${v};";
        return $q;
    }

    private function assocToDUPLICATE(array $assoc, string $table) {
        $q = substr($this->assocToINSERT($assoc, $table), 0, -1) . " ON DUPLICATE KEY UPDATE ";
        foreach ($assoc as $key => $value) {
            $k = $this->escapeColumn($key);
            $q = $q . "${k}=VALUES(${k}),";
        }
        $q = substr($q, 0, -1);
        return $q . ";";
    }

    public function getErrorMessage() {
        return $this->errorMessage??$this->mysqli->error;
    }

    public function isUserExist(string $username): bool {
        $username = $this->escapeValue($username);
        $query = "SELECT EXISTS (SELECT 1 FROM User WHERE `username`=${username} LIMIT 1);";
        if ($result = $this->mysqli->query($query)) {
            $r = $result->fetch_row();
            if ($r[0] === "1") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function fetchUserInfo(string $username, array $columns) {
        if ($this->isUserExist($username)) {
            $q = "";
            foreach ($columns as $_c) {
                $c = $this->escapeColumn($_c);
                $q = $q . $c . ",";
            }
            $q = substr($q, 0, -1);
            $username = $this->escapeValue($username);
            $query = "SELECT ${q} FROM User WHERE `username`=${username} LIMIT 1;";
            if ($result = $this->mysqli->query($query)) {
                $r = $result->fetch_row();
                return $r;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function insertAndUpdateUser(string $username, array $assoc, int $mode): bool {
        $assoc['username'] = $username;
        $queryUserTransaction = $this->assocToINSERT($assoc, 'UserTransaction');
        switch ($mode) {
            case self::INSERT:
                $queryUser = $this->assocToINSERT($assoc, 'User');
                break;
            case self::UPDATE:
                $queryUser = $this->assocToUPDATE($assoc, 'username', 'User');
                break;
            case self::DUPLICATE:
                $queryUser = $this->assocToDUPLICATE($assoc, 'User');
                break;
            default:
                throw new \Exception('\$mode must be chosen from MySQL::const');
        }
        $this->mysqli->begin_transaction();
        if ($qu_res = $this->mysqli->query($queryUser) && $qut_res = $this->mysqli->query($queryUserTransaction)) {
            $this->mysqli->commit();
            return true;
        } else {
            $this->errorMessage = $this->mysqli->error;
            $this->mysqli->rollback();
            return false;
        }
    }

    public function deleteUser(string $username): bool {
        if (!$this->isUserExist($username)) {
            return false;
        } else {
            $username = $this->escapeValue($username);
            $query = "DELETE FROM User WHERE `username`=${username};";
            if ($this->mysqli->query($query)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getAllUsername(): array {
        $ret = [];
        $query = "SELECT `username` FROM User;";
        $result = $this->mysqli->query($query);
        $_result = $result->fetch_all();
        foreach ($_result as $value) {
            array_push($ret, $value[0]??'');
        }
        return $ret;
    }

    public function insertServerSideCalcData(array $srvAssoc): bool {
        if (!isset($srvAssoc['username']) || !$this->isUserExist($srvAssoc['username'])) {
            throw new \Exception('NoUserSQL');
        }
        $query = $this->assocToINSERT($srvAssoc, 'AdjustTransaction');
        if ($this->mysqli->query($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function insertServerSideQuesData(array $srvAssoc): bool {
        if (!isset($srvAssoc['username']) || !$this->isUserExist($srvAssoc['username'])) {
            throw new \Exception('NoUserSQL');
        }
        $query = $this->assocToINSERT($srvAssoc, 'QuestionnaireTransaction');
        if ($this->mysqli->query($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function insertServerSideAnotherData(array $srvAssoc): bool {
        if (!isset($srvAssoc['username']) || !$this->isUserExist($srvAssoc['username'])) {
            throw new \Exception('NoUserSQL');
        }
        $query = $this->assocToINSERT($srvAssoc, 'AnotherTransaction');
        var_dump($query);exit();
        if ($this->mysqli->query($query)) {
            return true;
        } else {
            print_r($this->mysqli->error);
            return false;
        }
    }

    public function insertServerSideTmtData(array $srvAssoc): bool {
        if (!isset($srvAssoc['username']) || !$this->isUserExist($srvAssoc['username'])) {
            throw new \Exception('NoUserSQL');
        }
        $query = $this->assocToINSERT($srvAssoc, 'TmtTransaction');
        if ($this->mysqli->query(($query))){
            return true;
        } else{
            return false;
        }
    }

    public function getTimeLimit($hash) {
        $hash = $this->escapeValue($hash);
        $query = "SELECT `timeLimit` FROM AdjustTransaction WHERE `tid`=(SELECT max(`tid`) FROM AdjustTransaction WHERE `hash`=${hash});";
        if ($result = $this->mysqli->query($query)) {
            return (int)$result->fetch_row()[0];
        } else {
            return false;
        }
    }

    public function getQuestionnaire() {
        $retQ = [];
        $retA = [];
        $queryQ = "SELECT `text` FROM QuestionnaireQ";
        $queryA = "SELECT `text` FROM QuestionnaireA";
        $resultQ = $this->mysqli->query($queryQ);
        $resultA = $this->mysqli->query($queryA);
        if ($resultQ && $resultA) {
            while ($r = $resultQ->fetch_row()) {
                array_push($retQ, $r[0]);
            };
            while ($r = $resultA->fetch_row()) {
                array_push($retA, $r[0]);
            }
            return [$retQ, $retA];
        } else {
            return false;
        }
    }

    public function insertHash(string $username, int $pointer, string $hash): bool {
        $controller = Constant::getController($pointer);
        $times = Constant::getTimes($pointer);
        $query = $this->assocToINSERT(['username' => $username, 'controller' => $controller, 'times' => $times, 'hash' => $hash], 'hash');
        if ($this->mysqli->query($query)) {
            return true;
        } else {
            return false;
        }
    }
}