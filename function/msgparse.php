<?php
function getcityinfo($msg) {
	global $D;
	$citylist = $D["citylist"];
	foreach ($D["cityshortname"] as $city => $temp) {
		$citylist[] = $city;
	}
	$regex = "(" . implode("|", $citylist) . ")";
	if (preg_match($regex, $msg, $m)) {
		$city = $m[0];
		if (isset($D["cityshortname"][$city])) {
			$city = $D["cityshortname"][$city];
		}
		return $D["city"][$city]["status"] . "\n" .
			"欲接收訊息通知請輸入 /add " . $city . " （包含斜線跟空白）";
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
