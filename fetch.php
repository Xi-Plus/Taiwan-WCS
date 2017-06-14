<?php
require(__DIR__.'/config/config.php');
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}

require(__DIR__.'/function/curl.php');
require(__DIR__.'/function/log.php');
require(__DIR__.'/function/getlist.php');

$time = date("Y-m-d H:i:s");

$context = array(
    "ssl" => array(
        "verify_peer" => false,
        "verify_peer_name" => false,
    )
);  
$html = file_get_contents($C["fetch"], false, stream_context_create($context));
if ($html === false) {
	WriteLog("fetch fail");
	exit;
}
$html = str_replace(array("\r\n", "\n"), "", $html);

$sthcity = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}city` SET `status` = :status, `time` = :time, `fbpost` = 0, `fbmessage` = 0 WHERE `city` = :city");
foreach ($D["citylist"] as $city) {
	if (preg_match("/{$city}<\/FONT><\/TD>\s*<TD vAlign=center align=left width=[\"']70%[\"'][^>]*>(.*?)<\/TD>/", $html, $m)) {
		$status = strip_tags($m[1]);
	} else {
		$status = "無停班停課消息";
	}
	echo $city." ".$status."\n";
	
	if ($status != $D["city"][$city]["status"]) {
		$sthcity->bindValue(":status", $status);
		$sthcity->bindValue(":time", $time);
		$sthcity->bindValue(":city", $city);
		$res = $sthcity->execute();

		if ($res === false) {
			WriteLog("[fetch][error][updsta] city=".$city." status=".$status);
		}
	}
}
exec("php ".__DIR__."/fbpost.php > /dev/null 2>&1 &");
exec("php ".__DIR__."/fbmessage.php > /dev/null 2>&1 &");
WriteLog("[fetch][info] done");
