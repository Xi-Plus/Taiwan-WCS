<?php
function msgparse($msg) {
	global $D;
	$citylist = $D["citylist"];
	foreach ($D["cityshortname"] as $city => $temp) {
		$citylist []= $city;
	}
	$regex = "(".implode("|", $citylist).")";
	if (preg_match("/(幹嘛|怎麼|為何|為什麼|為啥).*(不|沒|沒有).*(放假|停課|停班)/", $msg)) {
		return "幹嘛問我，去問政府啊";
	}
	if (preg_match("/(何|怎麼).*(不).*(放假|停課|停班)/", $msg)) {
		return "幹嘛問我，去問政府啊";
	}
	if (preg_match($regex, $msg, $m)) {
		$city = $m[0];
		if (isset($D["cityshortname"][$city])) {
			$city = $D["cityshortname"][$city];
		}
		return $city." 現在的公告是「".$D["city"][$city]["status"]."」(".date("Y/m/d H:i", strtotime($D["city"][$city]["time"])).")\n".
			"欲接收訊息通知請輸入 /add ".$city;
	}
	if (preg_match("/(上課|上班|停課|停班).*[ㄇ嗎？]/", $msg)) {
		return "想知道不會自己去查喔";
	}
	if (preg_match("/(為何|為什麼|為啥).*(未達|沒達).*(標準)/", $msg)) {
		return "想知道不會自己去查喔";
	}
	if (preg_match("/(何時|啥時|什麼時候|哪天|哪日|是否).*(放假|停課|停班)/", $msg)) {
		return "想知道不會自己去查喔";
	}
	if (preg_match("/(幾點|何時|啥時|什麼時候).*(宣布|宣佈|公布|公佈|公告|發布|發佈)/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(要不要).*(宣布|宣佈|公布|公佈|公告|發布|發佈|放假|停課|停班)/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(怎麼|為何|為什麼|為啥).*(還沒|尚未|沒有|沒|不).*(宣布|宣佈|公布|公佈|公告|發布|發佈)/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(不).*(宣布|宣佈|公布|公佈|公告|發布|發佈).*[ㄇ嗎？?]/", $msg)) {
		return "我哪知道，去問政府啊";
	}
	if (preg_match("/(宣布|宣佈|公布|公佈|公告|發布|發佈).*了.*[ㄇ嗎？]/", $msg)) {
		return "想知道不會自己去查喔";
	}
	if (preg_match("/(幹嘛|怎麼|為何|為什麼|為啥).*(不|還沒|還不|還沒).*(宣布|宣佈|公布|公佈|公告|發布|發佈)/", $msg)) {
		return "幹嘛問我，去問政府啊";
	}
	if (preg_match("/(查的到|能查到).*(嗎|喔|？|?)/", $msg)) {
		return "查的到";
	}
	if (preg_match("/(哪裡).*(可以|能).*(查到)/", $msg)) {
		return "人事行政總處網站";
	}
	if (preg_match("/(哪裡).*(查)/", $msg)) {
		return "人事行政總處網站";
	}
	if (preg_match("/(hi|hello|不好意思|你好|您好|妳好|嗨)/i", $msg)) {
		return "你好";
	}
	if (preg_match("/(幹嘛)/i", $msg)) {
		return "怎麼了";
	}
	if (preg_match("/(幹|靠北|操你)/i", $msg)) {
		return "幹嘛罵我，去罵政府啊";
	}
	if ($msg === "1") {
		return "2";
	}
	if ($msg === "12") {
		return "34";
	}
	if ($msg === "123") {
		return "456";
	}
	if ($msg === "1234") {
		return "5678";
	}
	if ($msg === "12345") {
		return "你有病嗎";
	}
	return "";
}