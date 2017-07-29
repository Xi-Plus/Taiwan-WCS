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
	if (preg_match("/(幹嘛|怎麼|為何|為什麼|為啥).*(不|沒|沒有).*(放假|停課|停班)/", $msg)) {
		return "幹嘛問我，去問政府啊";
	}
	if (preg_match("/(何|怎麼).*(不).*(放假|停課|停班)/", $msg)) {
		return "幹嘛問我，去問政府啊";
	}
	if (preg_match("/(查的到|能查到).*(嗎|喔|？|\?)/", $msg)) {
		return "查的到";
	}
	if (preg_match("/(哪裡).*(可以|能).*(查到)/", $msg)) {
		return "人事行政總處網站";
	}
	if (preg_match("/(哪裡).*(查)/", $msg)) {
		return "人事行政總處網站";
	}
	if (preg_match("/(放假|上課|上班|停課|停班).*(ㄇ|嗎|？|\?|了沒)/", $msg)) {
		return "想知道不會自己去查喔";
	}
	if (preg_match("/(為何|為什麼|為啥|怎麼).*(未達|沒達|沒有達).*(標準)/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(何時|啥時|什麼時候|哪天|那天|哪日|那日|哪裡|那裡|哪裏|那裏|幾號|是否|今天|明天|後天|有沒有).*(放假|停課|停班|上班|上課)/", $msg)) {
		return "想知道不會自己去查喔";
	}
	if (preg_match("/(幾點|何時|啥時|什麼時候).*(宣布|宣佈|公布|公佈|公告|發布|發佈)/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(要不要).*(宣布|宣佈|公布|公佈|公告|發布|發佈|放假|停課|停班)/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(到底).*(放假|停課|停班)/", $msg)) {
		return "想知道不會自己去查喔";
	}
	if (preg_match("/(怎麼|為何|為什麼|為啥).*(還沒|尚未|沒有|沒|不).*(宣布|宣佈|公布|公佈|公告|發布|發佈)/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(不).*(宣布|宣佈|公布|公佈|公告|發布|發佈).*[ㄇ嗎？?]/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(放假|停課|停班|宣布|宣佈|公布|公佈|公告|發布|發佈).*(ㄇ|嗎|？|\?|沒)/", $msg)) {
		return "想知道不會自己去查喔";
	}
	if (preg_match("/(幹嘛|怎麼|為何|為什麼|為啥).*(不|還沒|還不|還沒).*(宣布|宣佈|公布|公佈|公告|發布|發佈)/", $msg)) {
		return "幹嘛問我，去問政府啊";
	}
	if (preg_match("/(hi|hello|不好意思|你好|您好|妳好|嗨)/i", $msg)) {
		return "你好";
	}
	if (preg_match("/(幹嘛)/i", $msg)) {
		return "怎麼了";
	}
	if (preg_match("/(到底|是).*(怎樣|怎麼樣)/i", $msg)) {
		return "我哪知";
	}
	if (preg_match("/(好啦|好喔)/i", $msg)) {
		return "才不好";
	}
	if (preg_match("/(幹|靠北|操你)/i", $msg)) {
		return "幹嘛罵我，去罵政府啊";
	}
	if (preg_match("/test/i", $msg)) {
		return "測試個毛";
	}
	if (preg_match("/(測試)/", $msg)) {
		return "測試個毛";
	}
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
			$res .= "，我就大發慈悲的告訴你\n\n";
		}
		$res .= $cityinfo;
	}
	return $res;
}
