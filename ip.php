<?php

header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('PRC'); 

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
//en
  ?>
<?php
   $ip = getIP();
   echo $ip;

?>