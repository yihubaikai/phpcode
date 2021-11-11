<?php
require_once 'public/common.php';
  require_once 'public/mysql.php';

class ClassName extends SQLPDO{
  protected $db;

  /*构造函数*/
  function __construct()
  {
    //$dsn = "mysql:host=127.0.0.1:3306;dbname=caiji";
    //$db_config = array("dsn"=>$dsn,"name"=>"caiji","password"=>"abcd1234");
    $dsn = "mysql:host=127.0.0.1:3306;dbname=laicaca";
    $db_config = array("dsn"=>$dsn,"name"=>"www","password"=>"Abcf8765D5");
    $this->db = new SQLPDO($db_config);
  }

  /*析构函数*/
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



$act = isset($_REQUEST["act"])?$_REQUEST["act"]:"";
$key = isset($_REQUEST["key"])?$_REQUEST["key"]:"";
$val = isset($_REQUEST["val"])?$_REQUEST["val"]:"";
$ret = array();
$ret["state"] = "1";
$ret["msg"] = "参数错误";

#取可以用的代理的代理IP
if($act == "proxy")
{

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


}


if($act == "get")
{
  $cli = new ClassName();
  $sqlcmd = "select * from proxy order by datatime limit 0,1;";
  echo $sqlcmd;
  $rs = $cli->getrs($sqlcmd);
  
  foreach ($rs as $key => $row) 
  {
     $ip = $row["ip"];
     $port = $row["port"];
     $xieyi = $row["xieyi"];
     $ret["state"] = "0";
     $ret["msg"]   = "succ";
     $ret["proxy"] = "$xieyi://$ip:$port";
     $ret["ip"]    = "$ip";
     $ret["xieyi"]    = "$xieyi";
     die(json_encode($ret));
     break;
  }

}

if($act == "set" && strlen($key)>0 && strlen($val)>0)
{
      $tm = gettime();
      $cli = new ClassName();
      $sqlcmd = "update proxy set flag='$val',datatime='$tm' where ip='$key';";
      echo $sqlcmd;
      $rs = $cli->dosql($sqlcmd);
      
       $ret["state"] = "0";
       $ret["msg"]   = "succ";
        die(json_encode($ret));
}


  die(json_encode($ret));



?>