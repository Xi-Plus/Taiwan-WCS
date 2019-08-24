<?php
require __DIR__ . '/config/config.php';
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}

require __DIR__ . '/function/log.php';

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}msg` WHERE `fbpost` = 0 ORDER BY `time` ASC");
$sth->execute();
$msgs = $sth->fetchAll(PDO::FETCH_ASSOC);

if (count($msgs) === 0) {
	exit("no change\n");
}

$text = '';
foreach ($msgs as $msg) {
	$text .= date("Y/m/d H:i", strtotime($msg['time'])) . " " . $msg['msg'] . "\n";
}
$text .= "\n資料來源：行政院人事行政總處";
echo $text . "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v2.8/me/feed");
curl_setopt($ch, CURLOPT_POST, true);
$post = array(
	"message" => $text,
	"access_token" => $C['FBpagetoken'],
);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);

$res = json_decode($res, true);
if (isset($res["error"])) {
	WriteLog("[fbpos][error] res=" . json_encode($res));
} else {
	$sthok = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}msg` SET `fbpost` = '1' WHERE `city` = :city AND `msg` = :msg");
	foreach ($msgs as $msg) {
		$sthok->bindValue(":city", $msg["city"]);
		$sthok->bindValue(":msg", $msg["msg"]);
		$res = $sthok->execute();
		if ($res === false) {
			WriteLog("[fbpos][error][updcit] city=" . $msg["name"] . " msg=" . $msg['msg']);
		}
	}
}
