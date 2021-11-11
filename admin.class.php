<?php
require_once (dirname(__FILE__) . "/../public/common.php");
require_once (dirname(__FILE__) . "/../public/mysql.php");
require_once (dirname(__FILE__) . "/../class/table.class.php");
 
 //查看是否配置完成
  if(!file_exists(dirname(__FILE__) . "/../class/config.php"))
  {
    die("未找到数据库配置文件config.php");
  }
  require_once(dirname(__FILE__) . "/../class/config.php");


class adminclass {
  protected $db;
  /*构造函数*/
  /*function __construct() {
    global $USERBASE;
    $db_config = getspdo();
    $this->db = new SQLPDO($db_config);
  }*/
  function __construct()
  {
    global $CONFIGARR;
    $database = $CONFIGARR["database"];
    $username = $CONFIGARR["database_user"];
    $password = $CONFIGARR["database_pass"];
  //echo dirname(__FILE__) . "/config.php<br>";
  //die( json_encode($CONFIGARR));
    $dsn = "mysql:host=47.99.142.106:82;dbname=square";
    $db_config = array("dsn"=>$dsn,"name"=>"root","password"=>"root!@#");
  //die(json_encode($db_config));
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
  function check_tab_exists($tabname) {
    $bRet = false;
    try 
 {
    global $CONFIGARR;
    $database = $CONFIGARR["database"];
    $sql = "select * from `$database`.`$tabname` limit 0,1;";
    //echo $sql;
    $this->db->select($sql);
    $bRet = true;
  }
    catch(Exception $e) {
    }
       return $bRet;
  }
  function Get_Table_list() {
    $sql = "show tables from pay;";
    $rs = $this->db->select($sql);
    return $rs;
  }
  //获取数据
  function Get_item_data($tabname, $startno, $len) {
    $sql = "select * from `$tabname` order by id limit $startno, $len;";
    //echo $sql;
    $rs = $this->db->select($sql);
    return $rs;
  }
  //检查表格是否存在
  function getbaseinfo($username) {
    $sql = "select * from `config` where username='$username';";
    $rs = $this->db->select($sql);
    return $rs;
  }
  function Login($username, $password) 
  {
      //来个固定登陆
      $arr = array();
      $arr["status"] = "0";
      $arr["username"] = "$username";
      $arr["nick"] = "小胖纸";
      $arr["id"] = "0";
      $arr["lilv"] ="0.03";
      $arr["para_id"] = "gameb";
      $arr["resetmoney"] = "14900000";
      $arr["deldataday"] = "6";
      $arr["logintimes"] = "6";
      $arr["msg"] = "登陆成功欢迎使用";
      return $arr;


    $sql = "select nick, id,logintimes,para_id,lilv,resetmoney,deldataday from `config` where username='$username' and userpass='$password';";
    $rs = $this->db->select($sql);
    
    if (count($rs) > 0) {
      $arr["status"] = "0";
      $arr["username"] = "$username";
      $arr["nick"] = $rs[0]["nick"];
      $arr["id"] = $rs[0]["id"];
      $arr["lilv"] = $rs[0]["lilv"];
      $arr["para_id"] = $rs[0]["para_id"];
      $arr["resetmoney"] = $rs[0]["resetmoney"];
      $arr["deldataday"] = $rs[0]["deldataday"];
      $arr["msg"] = "登陆成功欢迎使用";
      $tm = gettime();
      $ip = getIP();
      $id = $arr["id"];
      $logintimesstr = $rs[0]["logintimes"];
      if (strlen($logintimesstr) == 0) {
        $logintimes = 1;
      } else {
        $logintimes = intval($logintimesstr) + 1;
      }
      $arr["logintimes"] = $logintimes . "";
      $sql = "update `config` set logintimes='$logintimes', ip='127.0.0.1', logintime='$tm' where id = '$id';";
      $this->db->exec($sql);
    } else {
      $arr["status"] = "1";
      $arr["msg"] = "用户名或者密码错误";
    }
    //echo $sql;
    return $arr;
  }
  //,payerusername, alipayaccount
  function GetOrderList($shanghu, $user, $nick, $begin, $end, $type) {
    $sql = "select * from `$shanghu` where  unix_timestamp('$begin')<unix_timestamp(endtime) and  unix_timestamp(endtime)<unix_timestamp('$end') order by endtime desc;";
    //echo $sql;
    $rs = $this->db->select($sql);
    return $rs;
  }
  function GetOrderno($shanghu, $order_no) {
    $sql = "select * from `$shanghu` where  order_no='$order_no' or transferno='$order_no';";
    //echo $sql;
    $rs = $this->db->select($sql);
    return $rs;
  }
  //获取上次结算日期
  function getlastjiesuantime($para_id) {
    $arr = array();
    $sql = "select id,txdate from _tixianrecord_$para_id where status='1' order by txdate desc limit 0, 1";
    try {
      $rs = $this->db->select($sql);
    }
    catch(Exception $e) {
      $cli = new tableclass();
      if (!$cli->check_tab_exists("_tixianrecord_$para_id")) {
        $cli->Create_tixianrecord("_tixianrecord_$para_id");
      }
      $rs = $this->db->select($sql);
    }
    //echo $sql;
    if (count($rs) > 0) //获取最近的支付日期
    {
      $arr["status"] = "0";
      $arr["txdate"] = $rs[0]["txdate"] . " 23:59:59";
      $arr["id"] = $rs[0]["id"];
      $arr["msg"] = "找到符合条件的日期";
    } else {
      $arr["status"] = "1";
      $arr["msg"] = "条件不符,可能一次也没有支付过";
      //      $arr["txdate"] = "2017-04-18 15:33:57";
      $arr["txdate"] = "";
    }
    return $arr;
  }
  //获取GAMEA中的所有的数据
  function GetRecord($shanghu, $begin) {
    $arr = array();
    $arr["status"] = "1";
    $endtime = gettime(); //substr(gettime(),0,10) . " 0:0:0";
    if (strlen($begin) == 0) {
      $sql = "select id, amount, endtime from `$shanghu` where unix_timestamp(endtime)<unix_timestamp('$endtime') and endtime is not null order by endtime desc;";
    } else {
      $sql = "select id, amount,endtime from `$shanghu` where  unix_timestamp('$begin')<unix_timestamp(endtime) and  unix_timestamp(endtime)<unix_timestamp('$endtime') and endtime is not null order by endtime desc;";
    }
    #echo "<div>$sql</div>";
    try 
    {
      $rs = $this->db->select($sql);
    }
    catch(Exception $e) {
      $cli = new tableclass();
      if (!$cli->check_tab_exists($shanghu)) {
        $cli->Create_used_Table($shanghu);
      }
      $rs = $this->db->select($sql);
    }
    if (count($rs) > 0) 
    {
      $arr["list"] = array();
      foreach ($rs as $key => $row) 
      {
        $endtime = substr($row["endtime"], 0, 10);
        if (!isset($arr["list"][$endtime])) 
        {
          $arr["list"][$endtime] = 0;
        }
        $arr["list"][$endtime]+= round($row["amount"]);
        $arr["status"] = "0";
      }
    }
    if ($arr["status"] == "1") {
      $arr["msg"] = "无记录";
    } else {
      $arr["msg"] = "获取:" . count($rs) . " 条记录";
    }
    return $arr;
  }
  function show_weijiesuan($shanghu) {
    $retarr = $this->getlastjiesuantime($shanghu);
    //echo $retarr["txdate"];
    $arr = $this->GetRecord($shanghu, $retarr["txdate"]);
//    echo json_encode($arr);
    return $arr;
  }
  function show_yitixian($para_id) {
    $sql = "select txdate,money,stmoney,dotime from _tixianrecord_$para_id where status='1' order by  txdate desc;";
    //echo $sql;
    $rs = $this->db->select($sql);
    return $rs;
  }
  function add_tixianrecord($para_id, $jsonarr) {
    $arr = array();
    $txdate = $jsonarr["txdate"];
    $money = $jsonarr["money"];
    $stmoney = $jsonarr["stmoney"];
    $sql = "select id, txdate,status from `_tixianrecord_$para_id` where txdate='$txdate';";
    $rs = $this->db->select($sql);
    if (count($rs) == 0) {
      $tm = gettime();
      $ip = getIP();
      $sql = "insert into  `_tixianrecord_$para_id`(txdate,money,stmoney,dotime,status,remark5) values('$txdate','$money','$stmoney','$tm','1','$ip');";
      $this->db->exec($sql);
      $arr["status"] = "0";
      $arr["msg"] = "已记录,请重新刷新本页面";
    } else {
      $status = $rs[0]["status"];
      if ($status == "1") {
        $arr["status"] = "0";
        $arr["msg"] = $txdate . "的提现记录已经支付";
      } else {
        $tm = gettime();
        $ip = getIP();
        $id = $rs[0]["id"];
        $sql = "update `_tixianrecord_$para_id` set money='$money', stmoney='$stmoney', dotime='$tm', status='1', remark5='$ip' where id='$id';";
        $this->db->exec($sql);
        $arr["status"] = "0";
        $arr["msg"] = $txdate . "的提现记录已记录";
      }
    }
    return $arr;
  }
  function add_alipay_tixianrecord($jsonarr) {
    $arr = array();
    $account = $jsonarr["account"];
    $toaccount = isset($jsonarr["toaccount"]) ? $jsonarr["toaccount"] : "";
    $money = round($jsonarr["money"]) * 100;
    $tm = gettime();
    $ip = getIP();
    $sql = "insert into  `_alipay_tixian`(txdate,money,shoukuanzhanghu,dotime,status,remark1,remark5) values('$tm','$money','$account','$tm','1','$toaccount','$ip');";
    //echo $sql;
    $this->db->exec($sql);
    $arr["status"] = "0";
    $arr["msg"] = "已记录,请重新刷新本页面";
    return $arr;
  }
  function Get_paralist() {
    $sql = "select para_id from `config` where  length(para_id)>0;";
    $rs = $this->db->select($sql);
    $arr = array();
    $arr["list"] = array();
    foreach ($rs as $key => $row) {
      array_push($arr["list"], $row["para_id"]);
    }
    return $arr;
  }
  function Get_paralist2() {
    $sql = "select para_id,username from `config` where  length(para_id)>0;";
    $rs = $this->db->select($sql);
    $arr = array();
    $arr["list"] = array();
    foreach ($rs as $key => $row) {
      $acc = $row["username"];
      $para_id = $row["para_id"];
      $arr["$para_id"] = $acc;
      //array_push($arr["list"], $row["para_id"]);
      //
      
    }
    return $arr;
  }
  function Get_paralist3() {
    $sql = "select para_id,username,lilv from `config` where  length(para_id)>0 order by id;";
    $rs = $this->db->select($sql);
    $arr = array();
    $arr["list"] = array();
    foreach ($rs as $key => $row) {
      $acc = $row["username"];
      $para_id = $row["para_id"];
      $lilv = $row["lilv"];
      if (!isset($arr["list"]["$para_id"])) {
        $arr["list"]["$para_id"] = array();
      }
      $arr["list"]["$para_id"]["name"] = $acc;
      $arr["list"]["$para_id"]["lilv"] = $lilv;
      //array_push($arr["list"], $row["para_id"]);
      
    }
    return $arr;
  }
  function Get_cardlist() {
    $arr = array();
    $arr["list"] = array();
    if(!$this->check_tab_exists('_card'))
    {
      return $arr;
    }
    $sql = "select account from `_card` order by id";
    $rs = $this->db->select($sql);
    
    foreach ($rs as $key => $row) {
      array_push($arr["list"], $row["account"]);
    }
    return $arr;
  }
  function Get_cardlist_flag() {
    $arr = array();
    $arr["list"] = array();
    if(!$this->check_tab_exists('_card'))
    {
      return $arr;
    }
    $sql = "select id,account,status,time,yue,warningmoney,warningtime,remark1 from `_card` order by id";
    $rs = $this->db->select($sql);
    
    foreach ($rs as $key => $row) 
    {
      //array_push($arr["list"], $row["account"]);
      #echo json_encode($row);
      $arr["list"][$row["account"]]["id"]= $row["id"];
      $arr["list"][$row["account"]]["account"]= $row["account"];
      $arr["list"][$row["account"]]["yue"]= $row["yue"];
      $arr["list"][$row["account"]]["state"]= $row["status"];
      $arr["list"][$row["account"]]["time"]= $row["time"];
      $arr["list"][$row["account"]]["warningmoney"]= $row["warningmoney"];
      $arr["list"][$row["account"]]["warningtime"]= $row["warningtime"];
      $arr["list"][$row["account"]]["remark1"]= $row["remark1"];
    }
    return $arr;
  }


  function set_cardlist_flag($json)
  {
    $json["state"] = "1";
    $json["msg"] = "数据错误";

    $key = isset($json["key"])?geshihua($json["key"]):"";
    $val = isset($json["val"])?geshihua($json["val"]):"";
    $id = isset($json["id"])?geshihua($json["id"]):"";
    if(strlen($key)==0 || strlen($val)==0 || strlen($id)==0)
    {
      return $json;
    }
    $sql = "update `_card` set $key='$val' where id='$id';";
    $this->dosql($sql);
    $json["state"] = "0";
    $json["msg"] = "完成更新:$val";
    return $json;
  }


  function Get_cardlistex() {
    $sql = "select account,remark2 from `_card` order by remark2";
    $rs = $this->db->select($sql);
    $arr = array();
    $arr["list"] = array();
    foreach ($rs as $key => $row) {
      if (!isset($arr["list"][$row["account"]])) {
        $arr["list"][$row["account"]] = array();
      }
      $arr["list"][$row["account"]] = $row["remark2"];
      //array_push($arr["list"], $row["account"]);
      
    }
    return $arr;
  }
  function Get_alipayinfo($account, $para_id = "") {
    //$sql = "SELECT DATE_FORMAT( time, '%Y-%m-%d' ) as day, sum( money ) as money,  '刘勇' as name    FROM `balancereport_刘勇` GROUP BY DATE_FORMAT( time, '%Y-%m-%d' ) order by day desc limit 0, 60";
    global $SHOWTIME;
    if ($para_id == "") {
      $sql = "SELECT DATE_FORMAT( time, '%Y-%m-%d' ) as day, sum( money ) as money,  '$account' as name    FROM `balancereport_$account` where unix_timestamp(time)>=unix_timestamp('$SHOWTIME') GROUP BY DATE_FORMAT( time, '%Y-%m-%d' ) order by day desc ";
    } else {
      $sql = "SELECT DATE_FORMAT( time, '%Y-%m-%d' ) as day, sum( money ) as money,  '$account' as name    FROM `balancereport_$account` where  unix_timestamp(time)>=unix_timestamp('$SHOWTIME') and para_id='$para_id' GROUP BY DATE_FORMAT( time, '%Y-%m-%d' ) order by day desc ";
    }
    //echo $sql;
    $rs = $this->db->select($sql);
    return $rs;
  }
  //按照日期获取金额
  function Get_para_alljine($para_id) {
    $arr = array();
    $sql = "SELECT DATE_FORMAT( endtime, '%Y-%m-%d' ) as day, sum( total_fee ) as money, count(id) as num, '$para_id' as name    FROM `$para_id` where state='1' GROUP BY DATE_FORMAT( endtime, '%Y-%m-%d' ) ";
    // echo $sql;
    $rs = $this->db->select($sql);
    foreach ($rs as $key => $row) {
      $day = $row["day"];
      $money = round($row["money"]) * 0.01;
      $num = $row["num"];
      if (!isset($arr["list"])) {
        $arr["list"] = array();
      }
      if (!isset($arr["list"][$day])) {
        $arr["list"][$day]["money"] = "" . $money;
        $arr["list"][$day]["num"] = "" . $num;
      }
    }
    //  echo json_encode($arr);
    return $arr;
  }
  function Get_para_alipayinfo($para_id) {
    $alipaylist = $this->Get_cardlist();
    $parajinelist = $this->Get_para_alljine($para_id);
    $allsql = "";
    $arr = array();
    global $SHOWTIME;
    //echo json_encode($alipaylist);
    if (isset($alipaylist["list"]) && count($alipaylist["list"]) > 0) {
      foreach ($alipaylist["list"] as $key => $val) {
        if (strlen($allsql) == 0) {
          $sql = "SELECT DATE_FORMAT( time, '%Y-%m-%d' ) as day, sum( money ) as money, count(id) as num, '$val' as name    FROM `balancereport_$val` where para_id='$para_id' and unix_timestamp(time)>=unix_timestamp('$SHOWTIME')  GROUP BY DATE_FORMAT( time, '%Y-%m-%d' ) ";
          //echo ($val . "<br>");
          
        } else {
          $sql = "union SELECT DATE_FORMAT( time, '%Y-%m-%d' ) as day, sum( money ) as money,  count(id) as num, '$val' as name    FROM `balancereport_$val` where para_id='$para_id' and unix_timestamp(time)>=unix_timestamp('$SHOWTIME')  GROUP BY DATE_FORMAT( time, '%Y-%m-%d' )  ";
          //echo ($sql . "<br>");
          
        }
        $allsql = $allsql . $sql;
      }
      $allsql = $allsql . " order by day desc;";
     #echo $allsql;
      $rs = $this->db->select($allsql);
      foreach ($rs as $key => $row) {
        $day = $row["day"];
        $money = round($row["money"])*0.01;
        $num = intval($row["num"]);
        if (!isset($arr["list"])) {
          $arr["list"] = array();
        }
        if (!isset($arr["list"][$day])) {
          $arr["list"][$day] = array();
          $arr["list"][$day]["money"] = 0;
          $arr["list"][$day]["num"] = 0;
        }
        $arr["list"][$day]["money"] = intval($arr["list"][$day]["money"]) + $money;
        $arr["list"][$day]["num"] = intval($arr["list"][$day]["num"]) + intval($num);
        if (isset($parajinelist["list"][$day]["money"])) {
          $arr["list"][$day]["para_money"] = "" . $parajinelist["list"][$day]["money"];
          $arr["list"][$day]["para_num"] = "" . $parajinelist["list"][$day]["num"];
        }
      }
    }
    //$sql = "SELECT DATE_FORMAT( time, '%Y-%m-%d' ) as day, sum( money ) as money,  '刘勇' as name    FROM `balancereport_刘勇` GROUP BY DATE_FORMAT( time, '%Y-%m-%d' ) order by day desc limit 0, 60";
    //}
    //  $rs = $this->db->select($sql);
    // die( json_encode($arr) );
    return $arr;
  }
  function Get_para_alipayinfo_all() {
    $paralist = $this->Get_paralist();
    $allsql = "";
     global $SHOWTIME;
    foreach ($paralist["list"] as $key => $val) {
      if (strlen($allsql) == 0) {
        $sql = "select '$val' as item, txdate,money from `_tixianrecord_$val` where unix_timestamp(dotime)>=unix_timestamp('$SHOWTIME')  ";
      } else {
        $sql = "union select '$val' as item, txdate,money from `_tixianrecord_$val` where unix_timestamp(dotime)>=unix_timestamp('$SHOWTIME')  ";
      }
      $allsql = $allsql . $sql;
    }
    $allsql = $allsql . " order by txdate desc ";
    #echo $allsql;
    try {
      $rs = $this->db->select($allsql);
    }
    catch(Exception $e) {
      $cli = new tableclass();
      foreach ($paralist["list"] as $key => $val) {
        if (!$cli->check_tab_exists("_tixianrecord_$val")) {
          $cli->Create_tixianrecord("_tixianrecord_$val");
        }
      }
      $rs = $this->db->select($allsql);
    }
    $retarr = array();
    foreach ($rs as $key => $row) {
      $item = $row["item"];
      $txdate = $row["txdate"];
      $money = round($row["money"])*0.01;
      if (!isset($retarr["list"])) {
        $retarr["list"] = array();
      }
      if (!isset($retarr["list"][$txdate])) {
        $retarr["list"][$txdate] = array();
      }
      $retarr["list"][$txdate]["$item"] = $money;
      //$retarr["list"][$txdate]["money"] =$money;
      
    }
    return $retarr;
  }
  function Get_para_alipayinfo_now_all() {
    $paralist = $this->Get_paralist();
    $allsql = "";
    global $SHOWTIME;
    foreach ($paralist["list"] as $key => $val) 
    {
      if (strlen($allsql) == 0) {
        $sql = "SELECT DATE_FORMAT( endtime, '%Y-%m-%d' ) as txdate, sum( total_fee ) as money,'$val' as item    FROM `$val` where (state='1' or state='补单') and  unix_timestamp(endtime)>unix_timestamp('$SHOWTIME')   GROUP BY DATE_FORMAT( endtime, '%Y-%m-%d' ) ";
      } else {
        $sql = "union SELECT DATE_FORMAT( endtime, '%Y-%m-%d' ) as txdate, sum( total_fee ) as money,  '$val' as item    FROM `$val` where (state='1' or state='补单') and unix_timestamp(endtime)>unix_timestamp('$SHOWTIME')   GROUP BY DATE_FORMAT( endtime, '%Y-%m-%d' ) ";
      }
      $allsql = $allsql . $sql;
    }
    $allsql = $allsql . " order by txdate desc";
    #echo $allsql;
    try {
      $rs = $this->db->select($allsql);
    }
    catch(Exception $e) {
      $cli = new tableclass();
      foreach ($paralist["list"] as $key => $val) {
        if (!$cli->check_tab_exists("$val")) {
          $cli->Create_used_Table("$val");
        }
      }
      $rs = $this->db->select($allsql);
    }
    $retarr = array();
    foreach ($rs as $key => $row) {
      $item = $row["item"];
      $txdate = $row["txdate"];
      $money = round($row["money"])*0.01;
      if (!isset($retarr["list"])) {
        $retarr["list"] = array();
      }
      if (!isset($retarr["list"][$txdate])) {
        $retarr["list"][$txdate] = array();
      }
      $retarr["list"][$txdate]["$item"] = $money;
      //$retarr["list"][$txdate]["money"] =$money;
      
    }
    return $retarr;
  }
  function Get_alipay_all() {
    $paralist = $this->Get_cardlist();
    $allsql = "";
    global $SHOWTIME;
    foreach ($paralist["list"] as $key => $val) {
      if (strlen($allsql) == 0) {
        //$sql = "select '$val' as item, txdate,money from `_tixianrecord_$val` ";
        $sql = "SELECT DATE_FORMAT( time, '%Y-%m-%d' ) as day, sum( money ) as money,  '$val' as name    FROM `balancereport_$val` where unix_timestamp(time)>unix_timestamp('$SHOWTIME') GROUP BY DATE_FORMAT( time, '%Y-%m-%d' ) ";
      } else {
        $sql = "union SELECT DATE_FORMAT( time, '%Y-%m-%d' ) as day, sum( money ) as money,  '$val' as name    FROM `balancereport_$val` where unix_timestamp(time)>unix_timestamp('$SHOWTIME') GROUP BY DATE_FORMAT( time, '%Y-%m-%d' ) ";
      }
      $allsql = $allsql . $sql;
    }
    $allsql = $allsql . "order by day desc";
    #echo $allsql;
    try {
      $rs = $this->db->select($allsql);
    }
    catch(Exception $e) {
      /* $cli = new tableclass();
             foreach ($paralist["list"] as $key => $val) 
             {
               if( !$cli->check_tab_exists("_tixianrecord_$val") )
               {
                 $cli->Create_tixianrecord("_tixianrecord_$val");
               }
             }
              $rs = $this->db->select($allsql);*/
              die("请先使用APP生成二维码");
    }
    //echo $allsql;
    $retarr = array();
    foreach ($rs as $key => $row) {
      $item = $row["name"];
      $txdate = $row["day"];
      $money = $row["money"];
      if (!isset($retarr["list"])) {
        $retarr["list"] = array();
      }
      if (!isset($retarr["list"][$txdate])) {
        $retarr["list"][$txdate] = array();
      }
      $retarr["list"][$txdate]["$item"] = $money;
      //$retarr["list"][$txdate]["money"] =$money;
      
    }
    //echo json_encode($retarr);
    return $retarr;
  }
  function Get_alipay_yue() {
    //select shoukuanzhanghu, sum(money) from _tixianrecord  group by shoukuanzhanghu
    //$alipaylist = $this->Get_cardlist();
    $arr = array();
    $arr["list"] = array();
    $sql = "SELECT DATE_FORMAT( txdate, '%Y-%m-%d' ) as day,  sum(money) as money,  shoukuanzhanghu   FROM `_alipay_tixian` GROUP BY DATE_FORMAT( txdate, '%Y-%m-%d' ),shoukuanzhanghu  order by txdate desc;";
    //echo $sql;
    try {
      $rs = $this->db->select($sql);
    }
    catch(Exception $e) {
      $cli = new tableclass();
      if (!$cli->check_tab_exists("_alipay_tixian")) {
        $cli->Create_tixianrecord("_alipay_tixian");
      }
      $rs = $this->db->select($sql);
    }
    foreach ($rs as $key => $row) {
      $account = $row["shoukuanzhanghu"];
      $date = $row["day"];
      $money = $row["money"];
      if (!isset($arr["list"]["$date"])) {
        $arr["list"]["$date"] = array();
      }
      $arr["list"]["$date"]["$account"] = $money;
    }
    //echo json_encode($rs);
    return $arr;
  }
  function Get_cardlist20in1() {
    $sql = "select distinct(remark2), count(account) as num from `_card`  group by remark2 order by remark2;";
    $rs = $this->db->select($sql);
    $arr = array();
    $arr["list"] = array();
    foreach ($rs as $key => $row) {
      $arr["list"][$row["remark2"]] = $row["num"];
      //array_push($arr["list"], $row["remark1"]);
      
    }
    return $arr;
  }
  function Get20in1() {
    $arr = array();
    $arr["list"] = array();
    $sql = "SELECT DATE_FORMAT( txdate, '%Y-%m-%d' ) as day,  sum(money) as money,  shoukuanzhanghu   FROM `_alipay_tixian20in1` GROUP BY DATE_FORMAT( txdate, '%Y-%m-%d' ),shoukuanzhanghu  order by txdate desc;";
    //echo $sql;
    try {
      $rs = $this->db->select($sql);
    }
    catch(Exception $e) {
      $cli = new tableclass();
      if (!$cli->check_tab_exists("_alipay_tixian20in1")) {
        $cli->Create_tixianrecord("_alipay_tixian20in1");
      }
      $rs = $this->db->select($sql);
    }
    foreach ($rs as $key => $row) {
      $account = $row["shoukuanzhanghu"];
      $date = $row["day"];
      $money = $row["money"];
      if (!isset($arr["list"]["$date"])) {
        $arr["list"]["$date"] = array();
      }
      $arr["list"]["$date"]["$account"] = $money;
    }
    //echo json_encode($rs);
    return $arr;
  }
  function Get20in12() {
    $arr = array();
    $arr["list"] = array();
    $sql = "SELECT DATE_FORMAT( txdate, '%Y-%m-%d' ) as day,  sum(money) as money,  remark1   FROM `_alipay_tixian` GROUP BY DATE_FORMAT( txdate, '%Y-%m-%d' ),remark1  order by txdate desc;";
    //echo $sql;
    try {
      $rs = $this->db->select($sql);
    }
    catch(Exception $e) {
      $cli = new tableclass();
      if (!$cli->check_tab_exists("_alipay_tixian")) {
        $cli->Create_tixianrecord("_alipay_tixian");
      }
      $rs = $this->db->select($sql);
    }
    foreach ($rs as $key => $row) {
      $account = $row["remark1"];
      $date = $row["day"];
      $money = $row["money"];
      if (!isset($arr["list"]["$date"])) {
        $arr["list"]["$date"] = array();
      }
      $arr["list"]["$date"]["$account"] = $money;
    }
    //echo json_encode($rs);
    return $arr;
  }
  function Get20in1ex($jsonarr) {
    $arr = array();
    $arr["list"] = array();
    $acc = isset($jsonarr["account"]) ? $jsonarr["account"] : "";
    if (strlen($acc) == 0) {
      return $arr;
    }
    $sql = "SELECT txdate, money,shoukuanzhanghu FROM `_alipay_tixian` where remark1='$acc'  order by dotime desc;";
    //echo $sql;
    try {
      $rs = $this->db->select($sql);
    }
    catch(Exception $e) {
      $cli = new tableclass();
      if (!$cli->check_tab_exists("_alipay_tixian")) {
        $cli->Create_tixianrecord("_alipay_tixian");
      }
      $rs = $this->db->select($sql);
    }
    foreach ($rs as $key => $row) {
      $date = $row["txdate"];
      $money = $row["money"];
      $shoukuanzhanghu = $row["shoukuanzhanghu"];
      if (!isset($arr["list"][$date])) {
        $arr["list"][$date] = array();
      }
      $arr["list"][$date]["money"] = $money;
      $arr["list"][$date]["poster"] = $shoukuanzhanghu;
    }
    //echo json_encode($rs);
    return $arr;
  }
  function add_alipay_tixianrecord20in1($jsonarr) {
    $arr = array();
    $account = $jsonarr["account"];
    $toaccount = isset($jsonarr["toaccount"]) ? $jsonarr["toaccount"] : "";
    $money = intval($jsonarr["money"]) * 100;
    $tm = gettime();
    $ip = getIP();
    $sql = "insert into  `_alipay_tixian20in1`(txdate,money,shoukuanzhanghu,dotime,status,remark5) values('$tm','$money','$account','$tm','1','$ip');";
    //echo $sql;
    $this->db->exec($sql);
    $arr["status"] = "0";
    $arr["msg"] = "已记录,请重新刷新本页面";
    return $arr;
  }



  function getconfig($para_id)
  {
    $arr = array();
    $arr["state"] = "1";
    $sql = "select id,userpass,lilv,maxmoney,resetmoney,mail,deldataday from `config` where para_id='$para_id';";
    $rs = $this->db->select($sql);
    if(count($rs)>0)
    {
        $arr["id"] = $rs[0]["id"];
        $arr["lilv"] = $rs[0]["lilv"];
        $arr["maxmoney"] = $rs[0]["maxmoney"];
        $arr["resetmoney"] = $rs[0]["resetmoney"];
        $arr["mail"] = $rs[0]["mail"];
        $arr["userpass"] = $rs[0]["userpass"];
        $arr["deldataday"] = $rs[0]["deldataday"];
        $arr["state"] = "0";
    }
    return $arr;
  }

  function setconfig($json)
  {
      $json["state"] = "1";
      $json["msg"] = "数据错误!";
      $key = isset($json["key"])?$json["key"]:"";
      $val = isset($json["val"])?$json["val"]:"";
      if($key=="maxmoney")
      {
        $text = "<?php\r\n\t\$MAX_MONEY=$val;\r\n?>\r\n";
        SaveLog (dirname(__FILE__) . "/../public/payconfig.php",$text, true);
      }
      if(strlen($key)>0 && strlen($val)>0)
      {
        $sql = "update `config` set $key='$val';";
        $this->dosql($sql);
        $json["state"] = "0";
        $json["msg"] = "修改完成:$val";
      }
      return $json;
  }
}
?>