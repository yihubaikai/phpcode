<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original Author <author@example.com>                        |
// |          Your Name <you@example.com>                                 |
// +----------------------------------------------------------------------+
class SQLPDO {
    protected $pdo;
    protected $res;
    protected $config;
    /*构造函数*/
    function __construct($config) {
        $this->Config = $config;
        $this->connect();
    }
    /*数据库连接*/
    public function connect() {
        $this->pdo = new PDO($this->Config['dsn'], $this->Config['name'], $this->Config['password']);
        $this->pdo->query('set names utf8;');
        //把结果序列化成stdClass
        //$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        //自己写代码捕获Exception
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    /*数据库关闭*/
    public function close() {
        $this->pdo = null;
    }
    public function query($sql) {
        $res = $this->pdo->query($sql);
        if ($res) {
            $this->res = $res;
        }
    }
    public function exec($sql) {
        $res = $this->pdo->exec($sql);
        if ($res) {
            $this->res = $res;
        }
    }
    public function fetchAll()
		{
			return $this->res->fetchAll();
		}
    public function select($sqlcmd, $mode = 0) {
        $ret = null;
        $this->query($sqlcmd);
        if ($mode === 0) {
            $ret = $this->fetchAll();
        } else {
            $ret = $this->fetchAll();
            $ret = count($ret);
        }
        return $ret;
    }
    function dosql($sql) {
        $this->exec($sql);
    }
    function getrs($sql) {
        $rs = $this->select($sql);
        return $rs;
    }
    public function check_tab_exists($tabname) {
        $bRet = false;
        try {
            $sql = "select * from `$tabname` limit 0,1;";
            $this->select($sql,0);
            $bRet = true;
        }
        catch(Exception $e) {
        }
        return $bRet;
    }
    function SaveLog($filepath, $text, $bNewFile=false)
    {
          
          if($bNewFile)
          {
            $myfile2  = fopen($filepath, "w");
          }
          else
          {
            $myfile2  = fopen($filepath, "a");
          }

          if($myfile2)
          {
            fwrite($myfile2, $text);
            fclose($myfile2);
          }
    }
}
?>


<?php
/*
class ClassName extends SQLPDO{
  protected $db;

  //构造函数
  function __construct()
  {
    //$dsn = "mysql:host=127.0.0.1:3306;dbname=caiji";
    //$db_config = array("dsn"=>$dsn,"name"=>"caiji","password"=>"abcd1234");
    $dsn = "mysql:host=127.0.0.1:3306;dbname=laicaca";
    $db_config = array("dsn"=>$dsn,"name"=>"www","password"=>"Abcf8765D5");
    $this->db = new SQLPDO($db_config);
  }

  //析构函数
  function __destruct() {
    if ($this->db != null) { //print "Destroying \n";
      $this->db->close();
    }
  }

  function dosql($sql) {
    $this->db->exec($sql);
  }

  function getrs($sql) {
    $rs = $this->db->select($sql);
    return $rs;
  }
}




 $cli = new ClassName();
  $sqlcmd = "select * from proxy where flag='1' order by datatime limit 0,1;";
  #echo "$sqlcmd";
  $rs = $cli->getrs($sqlcmd);
  foreach ($rs as $key => $row) 
  {
     $ip = $row["ip"];
     $port = $row["port"];
     $xieyi = $row["xieyi"];
     #echo $ip . ":" . $port . "@" . $xieyi . "\r\n";
     $ret["state"] = "0";
     $ret["msg"]   = "succ";
     $ret["proxy"] = "$xieyi://$ip:$port";
     $ret["ip"]    = "$ip";
     $ret["xieyi"]    = "$xieyi";
     die(json_encode($ret));
     break;
  }

*/
?>