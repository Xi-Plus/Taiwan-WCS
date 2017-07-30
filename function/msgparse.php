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
	$do_not_ask_me = "停班停課又不是我能決定的，你連權責單位是誰都不知道，放什麼假";
	$search_by_yourself = "都能找到這粉專了，想知道的話為什麼不自己去查，懶得查就別放假了";
	// 1
	if (preg_match("/(幹嘛|怎麼|為何|為甚麼|為什麼|為啥).*(放假|停班|停課)/i", $msg)) {
		return $do_not_ask_me;
	}
	// 2
	if (preg_match("/(何).*(不).*(放假|停班|停課)/i", $msg)) {
		return $do_not_ask_me;
	}
	// 3
	if (preg_match("/(幹嘛|怎麼|為何|為甚麼|為什麼|為啥).*(不|還沒|還不|尚未|沒有|沒).*(宣布|宣佈|公布|公佈|公告|發布|發佈)/i", $msg)) {
		return $do_not_ask_me;
	}
	// 4
	if (preg_match("/(不).*(宣布|宣佈|公布|公佈|公告|發布|發佈).*(ㄇ|嗎|？|\?)/i", $msg)) {
		return $do_not_ask_me;
	}
	// 5
	if (preg_match("/(宣布|宣佈|公布|公佈|公告|發布|發佈|出爐|放假|停班|停課|上班|上課).*(ㄇ|嗎|？|\?|沒)/i", $msg)) {
		return $search_by_yourself;
	}
	// 6
	if (preg_match("/(何時|啥時|什麼時候|甚麼時候|幾點).*(宣布|宣佈|公布|公佈|公告|發布|發佈|知道)/i", $msg)) {
		return $do_not_ask_me;
	}
	// 7
	if (preg_match("/(要不要|會不會).*(宣布|宣佈|公布|公佈|公告|發布|發佈)/i", $msg)) {
		return $do_not_ask_me;
	}
	// 8
	if (preg_match("/(哪裡|那裡|哪裏|那裏|早上|下午|晚上|今天|明天|後天|是否|有沒有|有無|要不要).*(宣布|宣佈|公布|公佈|公告|發布|發佈|放假|停班|停課|上班|上課)/i", $msg)) {
		return $search_by_yourself;
	}
	// 9
	if (preg_match("/(何時|啥時|什麼時候|甚麼時候|哪天|那天|哪日|那日|哪幾天|那幾天|哪幾日|那幾日|幾號|會不會).*(放假|停班|停課|上班|上課)/i", $msg)) {
		return $do_not_ask_me;
	}
	// 10
	if (preg_match("/(到底).*(放假|停班|停課|上班|上課)/i", $msg)) {
		return $search_by_yourself;
	}
	// 11
	if (preg_match("/(怎麼|為何|為甚麼|為什麼|為啥).*(未達|沒達|沒有達).*(標準)/i", $msg)) {
		return $do_not_ask_me;
	}
	// 12
	if (preg_match("/(哪裡).*(可以|能).*(查到)/", $msg)) {
		return "人事行政總處網站";
	}
	// 13
	if (preg_match("/(hi|hello|不好意思|你好|您好|妳好|嗨|哈囉)/i", $msg)) {
		return "你好";
	}
	// 14
	if (preg_match("/(到底|是).*(怎樣|怎麼樣)/", $msg)) {
		return "我哪知";
	}
	// 15
	if (preg_match("/(好啦|好喔)/", $msg)) {
		return "才不好";
	}
	// 16
	if (preg_match("/(查的到|能查到).*(ㄇ|嗎|？|\?|了沒)/", $msg)) {
		return "查的到";
	}
	// 17
	if (preg_match("/(test|測試)/i", $msg)) {
		return "測試個毛";
	}
	// 18
	if (preg_match("/(幹|靠北|操你|笨蛋|白癡|白痴)/", $msg)) {
		return "幹嘛罵我，去罵政府啊";
	}
	// 19
	if (preg_match("/(幹嘛)/", $msg)) {
		return "怎麼了";
	}
	///
	if ($msg === "放假") {
		return "放個毛";
	}
	if (in_array($msg, ["1", "１"])) {
		return "2";
	}
	if (in_array($msg, ["12", "１２"])) {
		return "34";
	}
	if (in_array($msg, ["123", "１２３"])) {
		return "456";
	}
	if (in_array($msg, ["1234", "１２３４"])) {
		return "5678";
	}
	if (in_array($msg, ["12345", "１２３４５"])) {
		return "你有病嗎";
	}
	return "";
}
function msgparse($msg) {
	$res = mainparse($msg);
	if (($cityinfo = getcityinfo($msg)) !== "") {
		if ($res !== "") {
			$res .= "，我只能告訴你\n";
		}
		$res .= $cityinfo;
	}
	return $res;
}
