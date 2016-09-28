<?php
if(PHP_SAPI!="cli"){
	exit("No permission.");
}
require_once(__DIR__.'/config/config.php');
require_once(__DIR__.'/function/SQL-function/sql.php');

$time = date("Y-m-d H:i:s");

$html=file_get_contents("http://www.dgpa.gov.tw/");
if ($html===false) {
	exit("get fail");
}
$html=str_replace(array("\r\n","\n","\t"),"",$html);

$query = new query;
$query->table = 'city';
$city_list = $query->SELECT();
foreach ($city_list as $city) {
	if (preg_match("/".$city['name']."<\/FONT><\/TD>    <TD vAlign=center align=left width=\"70%\".*?>(.*?)<\/TD>/", $html, $match)) {
		$text=strip_tags($match[1]);
		if ($text != $city['text']) {
			$data[$city['city']] = array(
				'update'=>true,
				'name'=>$city['name'],
				'text'=>$text
			);
			$query = new query;
			$query->table = 'city';
			$query->value = array('text', $text);
			$query->where = array('city', $city['city']);
			$query->UPDATE();
		} else {
			$data[$city['city']] = array('update'=>false);
		}
	} else {
		$data[$city['city']] = array(
			'update'=>false
		);
	}
}

$messages=array();
$query = new query;
$query->table = 'follow';
$result = $query->SELECT();
foreach ($result as $follow) {
	$city = $follow['city'];
	if ($data[$city]['update']) {
		@$messages[$follow['uid']] .= $data[$city]['name']." 更新為「".$data[$city]['text']."」\n";
	}
}

foreach ($messages as $uid => $message) {
	$messageData=array(
		"recipient"=>array("id"=>$uid),
		"message"=>array("text"=>$message."\n資料來源： http://www.dgpa.gov.tw/nds.html")
	);
	$commend = 'curl -X POST -H "Content-Type: application/json" -d \''.json_encode($messageData,JSON_HEX_APOS|JSON_HEX_QUOT).'\' "https://graph.facebook.com/v2.7/me/messages?access_token='.$cfg['page_token'].'"';
	system($commend);
	$messages=array();
	$query = new query;
	$query->table = 'log';
	$query->value = array(
		array('uid', $uid),
		array('text', $message)
	);
	$query->INSERT();
}
?>
