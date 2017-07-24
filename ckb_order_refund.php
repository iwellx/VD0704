<?php 
	$data = file_get_contents('php://input');
	file_put_contents(getcwd() . '/b.txt',$data . date('Y-m-d H:i:s')."\r\n",FILE_APPEND);
	//header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
	//exit;
	$data =  XmlToArray($data);
	$message = $data['Message'];
	$post_data = array("data"=>base64_encode($message));
	$ch = curl_init();
	$url = 'http://test.jjicar.com:8088/index.php?&m=Admin&c=CkbOrder&a=deal_refund_order';
 	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$info = curl_exec($ch) ;
	$info = json_decode($info,true);
	if($info['code']== 200)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
	}else{
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
	}
	curl_close ( $ch );
	/**
	 * @author by jason 2016-01-21  
	 * @desc 将xml数据转换成数组
	 * @param $xml 需要转换的xml数据
	 * */
	function XmlToArray($xml){
		$xml_parser = xml_parser_create();   
	    if(!xml_parse($xml_parser,$xml,true)){   
	        xml_parser_free($xml_parser);   
	        return false;   
	    }else {   
	        return json_decode(json_encode((array)simplexml_load_string($xml)),true);  
	    }
	}