<?php 
	$data = file_get_contents('php://input');
	file_put_contents(getcwd() . '/log.txt',$data . date('Y-m-d H:i:s')."\r\n",FILE_APPEND);
	//header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
	//exit;
	$data =  XmlToArray($data);
	if($data)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
	}else{
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
	}
	$message = $data['Message'];
	$post_data = array("data"=>base64_encode($message));
	$ch = curl_init();
	$url = 'http://vd.jjicar.com/vd/index.php?&m=Admin&c=CkbOrder&a=deal_ckb_order';
 	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_exec($ch) ;
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