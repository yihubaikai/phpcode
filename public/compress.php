<?php

   class ENCODE
   {

    //压缩
   	function webcompress($str)
   	{
       $outstr = gzcompress($str);
	   $outstr = base64_encode($outstr);
	   return $outstr;
   	}


   	//解压
   	function webuncompress($str)
   	{
   		$buf    = base64_decode($str);
		$outstr = gzuncompress( $buf );
		return $outstr;
   	}


   }






?>