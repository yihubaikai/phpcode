<?php
 class XMLCLASS
 {
  protected $xml     = null;
  protected $xmlpath = null;
  
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

  function __construct($ixmlpath, $imei) /*构造函数*/
  {
      $this->xmlpath = $ixmlpath;
      
    if( file_exists($this->xmlpath) )
    {
      //$text = file_get_contents($this->xmlpath);
      $text = $this->CReadFile($this->xmlpath);
      if( strlen($text)==0 )
      {
          @unlink ($this->xmlpath);
          $this->xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root />');
          $pt = $this->xml->addChild('imei');
          $pt->addAttribute("CreateTime",       date('Y-m-d H:i:s'));
    
      }
      else
      {
        try 
        {
          $this->xml = new SimpleXMLElement($text);
        } catch (Exception $e) 
        {
           @unlink ($this->xmlpath);
          $this->xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root />');
          $pt = $this->xml->addChild('imei');
          $pt->addAttribute("CreateTime",       date('Y-m-d H:i:s'));
        }
        
      }   
    }
    else
    {
      $this->xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root />');
      $pt = $this->xml->addChild('imei');
      $pt->addAttribute("CreateTime",       date('Y-m-d H:i:s'));
    }
  }


  function __destruct()                /*析构函数*/
  {
    try
    {
      $this->xml->asXml($this->xmlpath);//输出XML文件
    }
    catch(Exception $e) 
    {

    }
    
  }

  
  function SetKey($key, $val)
  {
  $this->xml->imei[$key] = $val;
  }
  
  function GetKey($key)
  { 
    return (string)$this->xml->imei["$key"];
  }
   function DelKey($key)
  { 
    return (string)$this->xml->imei["$key"];
  }
 }
 
 
 
 //$xml =  new XMLCLASS("D:\\phpStudy\\wwwbase\\xml2.xml", "888888888999999");
 //$key =  $xml->SetKey("serverta2342342343g", "val888");
 //$val =  $xml->GetKey("servertag");
 //echo $val;
?>