<?php
echo 'a';exit;
	$name = iconv('utf-8','gb2312','±¨±íÃ÷Ï¸.csv');
	$str = "±àºÅ\n";
	for($i=0;$i<200000)
	{
		$str .= $i."\n";
	}
	header("Content-type:text/csv");
	header("Content-Disposition:attachment;filename=".$name);
	header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	header('Expires:0');
	header('Pragma:public');
	echo $str;
?>