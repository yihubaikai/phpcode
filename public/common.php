<?php
require_once(dirname(__FILE__) . "/../public/payconfig.php");
require_once(dirname(__FILE__) . "/../public/payshowtime.php");
#$TEAMNAME = "server"; //客户字符串
#$USERBASE = "jkuser";
#$DATABASE = "jkbase";
#$MAX_MONEY = 14900000;
#$lilv      = 0.03;

 $KEYARR = array(
    "body"           =>"商品详情",
    "total_fee"      =>"用户支付的金额",
    "para_id"        =>"商品渠道", 
    "app_id"         =>"APP编号", 
    "order_no"       =>"订单编号", 
    "notify_url"     =>"异步回调地址", 
    "attach"         =>"自定义备注", 
    "sign"           =>"签名校验", 
    "device_id"      =>"设备ID", 
    "mch_app_id"     =>"应用ID", 
    "mch_app_name"   =>"应用名", 
    "useridentity"   =>"用户标识", 
    "child_para_id"  =>"子渠道ID");

  $PLARR = array(
    "isSc"           => "isSc",
    "mct"            => "1491377228950",
    "mk"             => "170445152708200000",
    "st"             => "1",
    "amount"         =>"用户支付的金额(元)",
    "payerLoginId"   =>"payerLoginId:15590856980", 
    "payerSessionId" =>"payerSessionId:COLLECT_MONEY_PAY_2088622509373302_1491377031693", 
    "payerUserId"    =>"payerUserId:2088622509373302", 
    "payerUserName"  =>"支付者:姓名", 
    "sessionId"      =>"sessionId:COLLECT_MONEY_RECEIVER_2088222186807291", 
    "state"          =>"state:1", 
    "transferNo"     =>"transferNo交易流水号", 
    "userId"         =>"支付这ID:2088222186807291",
    "alipayaccount"  => "收款人"
     );
header("Content-type: text/html; charset=utf-8");
#$SHOWTIME = "2018-07-29 00:00:00";
date_default_timezone_set('PRC'); 

 /*网页跳转，因为跳转的时候网页打开需要时间，这里加了提示*/
  function pagejmp($txt, $url)
  {
   //$str = "<script>document.write('".$txt."<br>如果长时间未跳转页面，请确认网络是否连通再次尝试.');window.location.href='".$url."';< /script>";
    $str = "<script>window.location.href='".$url."';</script>";
   return $str;
  }


function getspdo()
{
   global $USERBASE;
    $dsn = "mysql:host=127.0.0.1;dbname=alipaypay20";
    $db_config = array('dsn' => $dsn, 'name' => 'alipayer', 'password' => 'alipayer000%5%');
    return $db_config;
}

function check_system()
{
  if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad'))
  {
    return "ios";
  }
  else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android'))
  {
    return "android";
  }
  else
  {
    return "windows";
  }
}

//过滤字符串
function filterstr( $instr )
{
  $msg = $instr;
  $msg = str_replace("<","&lt;",$msg);
  $msg = str_replace(">","&gt;",$msg);
  $msg = str_replace("'","&#039",$msg);
  //$msg = str_replace("%","％",$msg); 
  return $msg;
}


 function is_true_float($mVal)
{
  return ( is_float($mVal)
           || ( (float) $mVal != round($mVal)
                || strlen($mVal) != strlen( (int) $mVal) )
              && $mVal != 0 );
}

  function is_true_int( $val ) 
 {
  if(is_numeric($val))
  {
    return true;
  }
  else
  {
    return false;
  }
}
function CReadFile($filepath)
{
  $ret = "";
  if(file_exists($filepath))
  {
    $tempfile = $filepath . "_" . microtime(true) . ".txt";
    copy($filepath, $tempfile); 
    $ret = file_get_contents($tempfile);
    @unlink ($tempfile);
  }
  return $ret;
}
function formatfloat($num, $len=2)
  {
    $formattext = "%." . $len . "f";
     $str = sprintf("$formattext",$num);
     return $str;
  }
function Getfloat($str)
  { 
  $ret = str_replace(" ", "", $str);
  $ret = str_replace("+", "", $ret);
  $ret = str_replace("-", "", $ret);
   if(is_true_float($ret) )
  {
    $fmoney =sprintf("%.2f",floatval($ret) );
    return $fmoney;
  }

    if(is_true_int($ret))  
    {
       //echo $ret . "\r\n";
       if(strstr($ret, "."))
       {
        return $ret;
       }
       else
       {
        return $ret.".00";
       }
       
    }
     return "";
  }

  function Getint($str)
  {
    return intval($str);
  }

function geshihua($str)
{
   $outstr = $str;
  if( strlen($str) > 0)
  {
    $outstr = str_replace("'", "", $str);
    $outstr = str_replace('"', '', $outstr);
    $outstr = str_replace("\\", "＼", $outstr);
  }
   
   return $outstr;
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
function jiemi($data)
{
	  if(isset($_POST["gzcompress"]) && $_POST["gzcompress"]=="1")
	  {
	      $data = base64_decode($data);
	      $data = gzuncompress($data);
	  }
	  else
	  {
	  	  $data = urldecode($data);
	  }
	   return $data;
}


	function jiami($data)
	{
	   if(isset($_POST["gzcompress"]) && $_POST["gzcompress"]=="1")
	   {
	     $outstr = gzcompress($data);
	     $data   = base64_encode($outstr);
	     $data   = urlencode($data);
	   }
	  return $data;
	}
 
 //打印过滤JSON 所有数据封装
 function printjson($jsonarr)
 {
    $arr = array();
	 if(isset($jsonarr["code"]) && !is_null($jsonarr["code"]) )
	 {
	   $json          = jiami(json_encode($jsonarr));
	   $arr["code"]   = $jsonarr["code"];   
	   $arr["crcode"] = md5($json);
	   $arr["data"]   = $json;
	 }
	 $json = json_encode($arr);
  
    return $json;
 }


function Mk_Folder($Folder)
{
if(  !is_readable($Folder)   )
 {
Mk_Folder( dirname($Folder) );
if(!is_file($Folder)) 
  {mkdir($Folder,0777);}}
}
 
//获取IP
function getIP()
{
	#return "127.0.0.1";
	global $ip;
	if (getenv("HTTP_CLIENT_IP"))
	$ip = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR"))
	$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("REMOTE_ADDR"))
	$ip = getenv("REMOTE_ADDR");
	else $ip = "Unknow";
	return $ip;
}

function gettime()
{
	$time  = date('Y-m-d H:i:s');
	return $time;
}
function gettimecuo()
{
  $timei =   microtime(true) * 1000;
  $times =  $timei . ".";
  $times = substr($times,0,13);
  return $times;
}

 function getIsPostRequest()
{
  return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST');
}

function ispost()
{
  return getIsPostRequest();
}

 //转发get请求
   function get_url_content($url)
	{    
		try
		{


	    $user_agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)";     
	    $ch = curl_init();    
	    //curl_setopt ($ch, CURLOPT_PROXY, $proxy);    
	    curl_setopt ($ch, CURLOPT_URL, $url);//设置要访问的IP    
	    curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);//模拟用户使用的浏览器     
	    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转      
	    curl_setopt ($ch, CURLOPT_TIMEOUT, 160); //设置超时时间    
	    curl_setopt ($ch, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer      
	    curl_setopt ($ch, CURLOPT_COOKIEJAR, 'c:\cookie.txt');    
	    curl_setopt ($ch, CURLOPT_HEADER,0); //显示返回的HEAD区域的内容    
	    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);    
	    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);    
	    curl_setopt ($ch, CURLOPT_TIMEOUT, 10);    
	    $result = curl_exec($ch);    
	    curl_close($ch);
	    }
		catch(Exception $e)
		{
			echo "访问网页超时";
		}
	    return $result;    
	}


     //转发POST请求
	function post_url_content($url,$post_data)
	{
		$user_agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)";     
	    $ch = curl_init();    
	    //curl_setopt ($ch, CURLOPT_PROXY, $proxy);    
	    curl_setopt ($ch, CURLOPT_URL, $url);//设置要访问的IP    
	    curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);//模拟用户使用的浏览器     
	    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转      
	    curl_setopt ($ch, CURLOPT_TIMEOUT, 60); //设置超时时间    
	    curl_setopt ($ch, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer      
	    curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');    
	    curl_setopt ($ch, CURLOPT_HEADER,0); //显示返回的HEAD区域的内容    
	    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);    
	    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);    
	    curl_setopt ($ch, CURLOPT_TIMEOUT, 10);   
	
		// post数据
		curl_setopt($ch, CURLOPT_POST, 1);
		// post的变量
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    $result = curl_exec($ch);    
	    curl_close($ch);    
	    return $result;
	}
//end
//

function GetMoneylist()
{
  $morearr = array();
  /*$morearr["1000"]=10;
  $morearr["10000"]=10;
  $morearr["100000"]=10;
  $morearr["50000"]=10;
  $morearr["20000"]=10;
  $morearr["5000"]=10;
  $morearr["2000"]=10;
  $morearr["30000"]=10;
  $morearr["150000"]=10;
  $morearr["190000"]=10;
  $morearr["3000"]=10;
  $morearr["15000"]=10;
  $morearr["40000"]=10;
  $morearr["1500"]=10;
  $morearr["80000"]=10;
  $morearr["60000"]=10;
  $morearr["8000"]=10;
  $morearr["6000"]=10;
  $morearr["70000"]=10;
  $morearr["4000"]=10;
  $morearr["7000"]=10;
  $morearr["199900"]=10;
  $morearr["90000"]=10;
  $morearr["9000"]=10;
  $morearr["180000"]=10;
  $morearr["25000"]=10;
  $morearr["35000"]=10;
  $morearr["12000"]=10;
  $morearr["1200"]=10;
  $morearr["120000"]=10;
  $morearr["195000"]=10;
  $morearr["19000"]=10;
  $morearr["2500"]=10;
  $morearr["1300"]=10;
  $morearr["1800"]=10;
  $morearr["1400"]=10;
  $morearr["160000"]=10;
  $morearr["1600"]=10;
  $morearr["110000"]=10;
  $morearr["2200"]=10;
  $morearr["140000"]=10;
  $morearr["130000"]=10;
  $morearr["1100"]=10;
  $morearr["14000"]=10;
  $morearr["13000"]=10;
  $morearr["18000"]=10;
  $morearr["11000"]=10;
  $morearr["170000"]=10;
  $morearr["45000"]=10;
  $morearr["5500"]=10;
  $morearr["1700"]=10;
  $morearr["4400"]=10;
  $morearr["17000"]=10;
  $morearr["6600"]=10;
  $morearr["16000"]=10;
  $morearr["1900"]=10;
  $morearr["3500"]=10;
  $morearr["8800"]=10;
  $morearr["2300"]=10;
  $morearr["3300"]=10;
  $morearr["95000"]=10;
  $morearr["9900"]=10;
  $morearr["55000"]=10;
  $morearr["2100"]=10;
  $morearr["28000"]=10;
  $morearr["9500"]=10;
  $morearr["65000"]=10;
  $morearr["6500"]=10;
  $morearr["199000"]=10;
  $morearr["75000"]=10;
  $morearr["2400"]=10;
  $morearr["85000"]=10;
  $morearr["9800"]=10;
  $morearr["8500"]=10;
  $morearr["100"]=10;
  $morearr["2600"]=10;
  $morearr["2700"]=10;
  $morearr["30600"]=10;
  $morearr["4500"]=10;
  $morearr["24000"]=10;
  $morearr["99000"]=10;
  $morearr["22000"]=10;
  $morearr["26000"]=10;
  $morearr["49000"]=10;
  $morearr["2800"]=10;
  $morearr["7500"]=10;
  $morearr["105000"]=10;
  $morearr["7700"]=10;
  $morearr["32000"]=10;
  $morearr["165000"]=10;
  $morearr["19700"]=10;
  $morearr["3800"]=10;
  $morearr["8900"]=10;
  $morearr["23000"]=10;
  $morearr["2900"]=10;
  $morearr["6800"]=10;
  $morearr["9700"]=10;
  $morearr["19900"]=10;
  $morearr["33000"]=10;
  $morearr["10500"]=10;
  $morearr["31000"]=10;
  $morearr["7800"]=10;
  $morearr["6200"]=10;
  $morearr["21000"]=10;
  $morearr["5200"]=10;
  $morearr["3900"]=10;
  $morearr["9600"]=10;
  $morearr["4200"]=10;
  $morearr["5700"]=10;
  $morearr["19800"]=10;
  $morearr["38000"]=10;
  $morearr["6300"]=10;
  $morearr["12500"]=10;
  $morearr["4800"]=10;
  $morearr["4900"]=10;
  $morearr["5800"]=10;
  $morearr["3400"]=10;
  $morearr["29000"]=10;
  $morearr["6900"]=10;
  $morearr["27000"]=10;
  $morearr["78000"]=10;
  $morearr["7200"]=10;
  $morearr["3100"]=10;
  $morearr["5900"]=10;
  $morearr["9300"]=10;
  $morearr["19400"]=10;
  $morearr["36000"]=10;
  $morearr["48000"]=10;
  $morearr["115000"]=10;
  $morearr["3200"]=10;
  $morearr["39000"]=10;
  $morearr["47000"]=10;
  $morearr["49900"]=10;
  $morearr["7600"]=10;
  $morearr["14500"]=10;*/
$morearr["1000"]=10;
$morearr["2000"]=10;
$morearr["5000"]=10;
$morearr["10000"]=10;
$morearr["20000"]=10;
$morearr["30000"]=10;
$morearr["40000"]=10;
$morearr["50000"]=10;
$morearr["60000"]=10;
$morearr["70000"]=10;
$morearr["80000"]=10;
$morearr["90000"]=10;
$morearr["100000"]=10;
$morearr["150000"]=10;
$morearr["200000"]=10;
$morearr["250000"]=10;
$morearr["300000"]=10;
$morearr["350000"]=10;
$morearr["400000"]=10;
$morearr["450000"]=10;
$morearr["500000"]=10;

  $moneylist = array();
  foreach ($morearr as $key => $value) 
  {
    #echo $key . "<br>\r\n";
    for ($j=0; $j<20;  $j++) 
      { 
        $moneylist[$key-$j] = 0;
      }
   }

   /*for ($i=100; $i <= 300000 ; $i+=100) 
   { 
    if( isset($morearr[$i]) )
    {
      for ($j=0; $j<5;  $j++) 
      { 
        $moneylist[$i-$j] = 0;
        //有几个固定之外的结果
        $moneylist[350000-$j] = 0;
        $moneylist[400000-$j] = 0;
        $moneylist[450000-$j] = 0;
        $moneylist[500000-$j] = 0;
      }
    }
    else
    {
      $moneylist[$i] = 0;
    }
   }*/

return $moneylist;
   //echo json_encode($moneylist);
}


function GetMoneylistex()
{
  $morearr = array();
  /*$morearr["1000"]=10;
  $morearr["10000"]=10;
  $morearr["100000"]=10;
  $morearr["50000"]=10;
  $morearr["20000"]=10;
  $morearr["5000"]=10;
  $morearr["2000"]=10;
  $morearr["30000"]=10;
  $morearr["150000"]=10;
  $morearr["190000"]=10;
  $morearr["3000"]=10;
  $morearr["15000"]=10;
  $morearr["40000"]=10;
  $morearr["1500"]=10;
  $morearr["80000"]=10;
  $morearr["60000"]=10;
  $morearr["8000"]=10;
  $morearr["6000"]=10;
  $morearr["70000"]=10;
  $morearr["4000"]=10;
  $morearr["7000"]=10;
  $morearr["199900"]=10;
  $morearr["90000"]=10;
  $morearr["9000"]=10;
  $morearr["180000"]=10;
  $morearr["25000"]=10;
  $morearr["35000"]=10;
  $morearr["12000"]=10;
  $morearr["1200"]=10;
  $morearr["120000"]=10;
  $morearr["195000"]=10;
  $morearr["19000"]=10;
  $morearr["2500"]=10;
  $morearr["1300"]=10;
  $morearr["1800"]=10;
  $morearr["1400"]=10;
  $morearr["160000"]=10;
  $morearr["1600"]=10;
  $morearr["110000"]=10;
  $morearr["2200"]=10;
  $morearr["140000"]=10;
  $morearr["130000"]=10;
  $morearr["1100"]=10;
  $morearr["14000"]=10;
  $morearr["13000"]=10;
  $morearr["18000"]=10;
  $morearr["11000"]=10;
  $morearr["170000"]=10;
  $morearr["45000"]=10;
  $morearr["5500"]=10;
  $morearr["1700"]=10;
  $morearr["4400"]=10;
  $morearr["17000"]=10;
  $morearr["6600"]=10;
  $morearr["16000"]=10;
  $morearr["1900"]=10;
  $morearr["3500"]=10;
  $morearr["8800"]=10;
  $morearr["2300"]=10;
  $morearr["3300"]=10;
  $morearr["95000"]=10;
  $morearr["9900"]=10;
  $morearr["55000"]=10;
  $morearr["2100"]=10;
  $morearr["28000"]=10;
  $morearr["9500"]=10;
  $morearr["65000"]=10;
  $morearr["6500"]=10;
  $morearr["199000"]=10;
  $morearr["75000"]=10;
  $morearr["2400"]=10;
  $morearr["85000"]=10;
  $morearr["9800"]=10;
  $morearr["8500"]=10;
  $morearr["100"]=10;
  $morearr["2600"]=10;
  $morearr["2700"]=10;
  $morearr["30600"]=10;
  $morearr["4500"]=10;
  $morearr["24000"]=10;
  $morearr["99000"]=10;
  $morearr["22000"]=10;
  $morearr["26000"]=10;
  $morearr["49000"]=10;
  $morearr["2800"]=10;
  $morearr["7500"]=10;
  $morearr["105000"]=10;
  $morearr["7700"]=10;
  $morearr["32000"]=10;
  $morearr["165000"]=10;
  $morearr["19700"]=10;
  $morearr["3800"]=10;
  $morearr["8900"]=10;
  $morearr["23000"]=10;
  $morearr["2900"]=10;
  $morearr["6800"]=10;
  $morearr["9700"]=10;
  $morearr["19900"]=10;
  $morearr["33000"]=10;
  $morearr["10500"]=10;
  $morearr["31000"]=10;
  $morearr["7800"]=10;
  $morearr["6200"]=10;
  $morearr["21000"]=10;
  $morearr["5200"]=10;
  $morearr["3900"]=10;
  $morearr["9600"]=10;
  $morearr["4200"]=10;
  $morearr["5700"]=10;
  $morearr["19800"]=10;
  $morearr["38000"]=10;
  $morearr["6300"]=10;
  $morearr["12500"]=10;
  $morearr["4800"]=10;
  $morearr["4900"]=10;
  $morearr["5800"]=10;
  $morearr["3400"]=10;
  $morearr["29000"]=10;
  $morearr["6900"]=10;
  $morearr["27000"]=10;
  $morearr["78000"]=10;
  $morearr["7200"]=10;
  $morearr["3100"]=10;
  $morearr["5900"]=10;
  $morearr["9300"]=10;
  $morearr["19400"]=10;
  $morearr["36000"]=10;
  $morearr["48000"]=10;
  $morearr["115000"]=10;
  $morearr["3200"]=10;
  $morearr["39000"]=10;
  $morearr["47000"]=10;
  $morearr["49900"]=10;
  $morearr["7600"]=10;
  $morearr["14500"]=10;*/
$morearr["1000"]=10;
$morearr["2000"]=10;
$morearr["5000"]=10;
$morearr["10000"]=10;
$morearr["20000"]=10;
$morearr["30000"]=10;
$morearr["40000"]=10;
$morearr["50000"]=10;
$morearr["60000"]=10;
$morearr["70000"]=10;
$morearr["80000"]=10;
$morearr["90000"]=10;
$morearr["100000"]=10;
$morearr["150000"]=10;
$morearr["200000"]=10;
$morearr["250000"]=10;
$morearr["300000"]=10;
$morearr["350000"]=10;
$morearr["400000"]=10;
$morearr["450000"]=10;
$morearr["500000"]=10;
  

/*  $moneylist = array();

   for ($i=100; $i <= 300000 ; $i+=100) 
   { 
    if( isset($morearr[$i]) )
    {
      for ($j=($i-$morearr[$i]); $j<=$i;  $j++) 
      { 
        $moneylist[$j] = 0;
      }
    }
    else
    {
      $moneylist[$i] = 0;
    }
   }*/

return $morearr;
   //echo json_encode($moneylist);
}

?>