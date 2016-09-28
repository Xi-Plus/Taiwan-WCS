<?php
if(PHP_SAPI!="cli"){
	exit("No permission.");
}
require_once(__DIR__.'/config/config.php');
require_once(__DIR__.'/function/SQL-function/sql.php');

$time = date("Y-m-d H:i:s");

$html=file_get_contents("http://www.dgpa.gov.tw/nds.html");
if ($html===false) {
	exit("get fail");
}
$html=str_replace(array("\r\n","\n","\t"),"",$html);

$query = new query;
$query->table = 'city';
$city_list = $query->SELECT();
$all_msg="";
foreach ($city_list as $city) {
	echo $city['name'];
	if (preg_match("/".$city['name']."<\/FONT><\/TD><TD vAlign=center align=left width='70%'.*?>(.*?)<\/TD>/", $html, $match)) {
		$text = strip_tags($match[1]);
		$msg = $city['name']." 更新為「".$text."」\n";
	} else {
		echo " not found";
		$text = "無停班停課消息";
		$msg = $city['name']." 無停班停課消息\n";
	}
	if ($text != $city['text']) {
		$all_msg .= $msg;
		$data[$city['city']] = array(
			'update'=>true,
			'name'=>$city['name'],
			'text'=>$msg,
			'time'=>date("Y-m-d H:i:s")
		);
		$query = new query;
		$query->table = 'city';
		$query->value = array('text', $text);
		$query->where = array('city', $city['city']);
		$query->UPDATE();
	} else {
		$data[$city['city']] = array('update'=>false);
	}
	echo "\n";
}

$messages=array();
$query = new query;
$query->table = 'follow';
$result = $query->SELECT();
foreach ($result as $follow) {
	$city = $follow['city'];
	if ($data[$city]['update']) {
		@$messages[$follow['uid']] .= $data[$city]['text'];
	}
}

foreach ($messages as $uid => $message) {
	echo "Send to ".$uid;
	$messageData=array(
		"recipient"=>array("id"=>$uid),
		"message"=>array("text"=>$message."\n資料來源： http://www.dgpa.gov.tw/nds.html")
	);
	$commend = 'curl -X POST -H "Content-Type: application/json" -d \''.json_encode($messageData,JSON_HEX_APOS|JSON_HEX_QUOT).'\' "https://graph.facebook.com/v2.7/me/messages?access_token='.$cfg['page_token'].'"';
	system($commend);
	$query = new query;
	$query->table = 'log';
	$query->value = array(
		array('uid', $uid),
		array('text', $message)
	);
	$query->INSERT();
	echo "\n";
}

if ($all_msg != "") {
	$all_msg = "資料來源： http://www.dgpa.gov.tw/nds.html\n\n".$all_msg."\n本粉專依據行政院人事行政總處網站發布的內容轉載，一切以來源為準";
	$commend = 'curl -X POST -H "Content-Type: application/json" -d \'message='.$all_msg.'\' "https://graph.facebook.com/v2.7/'.$cfg['page_id'].'/feed?access_token='.$cfg['page_token'].'"';
	system($commend);
	$query = new query;
	$query->table = 'log';
	$query->value = array(
		array('uid', 'post'),
		array('text', $all_msg)
	);
	$query->INSERT();
}
?>
