<?php
function getcityinfo($msg) {
	global $D;
	$citylist = $D["citylist"];
	foreach ($D["cityshortname"] as $city => $temp) {
		$citylist []= $city;
	}
	$regex = "(".implode("|", $citylist).")";
	if (preg_match($regex, $msg, $m)) {
		$city = $m[0];
		if (isset($D["cityshortname"][$city])) {
			$city = $D["cityshortname"][$city];
		}
		return $city."最新的公告是在".date("Y/m/d H:i", strtotime($D["city"][$city]["time"]))."的「".$D["city"][$city]["status"]."」\n".
			"欲接收訊息通知請輸入 /add ".$city;
	}
	return "";
}
function mainparse($msg) {
	return "";
}
function msgparse($msg) {
	$res = mainparse($msg);
	if (($cityinfo = getcityinfo($msg)) !== "") {
		$res .= $cityinfo;
	}
	return $res;
}
