<?php
require(__DIR__.'/config/config.php');
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}

require(__DIR__.'/function/log.php');

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}city` WHERE `fbpost` = 0 ORDER BY `no`");
$sth->execute();
$citys = $sth->fetchAll(PDO::FETCH_ASSOC);

if (count($citys) === 0) {
	exit("no change\n");
}

$msg = date("Y/m/d H:i")."\n\n";
foreach ($citys as $city) {
	$msg .= $city["city"]." 更新為「".$city["status"]."」\n\n";
}
$msg .= "資料來源：行政院人事行政總處";
echo $msg."\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v2.8/me/feed");
curl_setopt($ch, CURLOPT_POST, true);
$post = array(
	"message" => $msg,
	"access_token" => $C['FBpagetoken']
);
curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
$res = curl_exec($ch);
curl_close($ch);

$res = json_decode($res, true);
if (isset($res["error"])) {
	WriteLog("[fbpos][error] res=".json_encode($res));
} else {
	$sthok = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}city` SET `fbpost` = '1' WHERE `city` = :city");
	foreach ($citys as $city) {
		$sthok->bindValue(":city", $city["city"]);
		$res = $sthok->execute();
		if ($res === false) {
			WriteLog("[fbpos][error][updcit] city=".$city["name"]);
		}
	}
}
