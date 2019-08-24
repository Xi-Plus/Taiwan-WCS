<?php
require __DIR__ . '/config/config.php';
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}

require __DIR__ . '/function/log.php';

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}msg` WHERE `tgmessage` = 0 ORDER BY `time` ASC");
$sth->execute();
$msgs = $sth->fetchAll(PDO::FETCH_ASSOC);

if (count($msgs) === 0) {
	exit("no change\n");
}

$text = '';
foreach ($msgs as $msg) {
	$text .= date("Y/m/d H:i", strtotime($msg['time'])) . " " . $msg['msg'] . "\n";
}
echo $text . "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . $C['TGtoken'] . "/sendMessage");
curl_setopt($ch, CURLOPT_POST, true);
$post = array(
	"chat_id" => $C["TGchatid"],
	"disable_web_page_preview" => true,
	"text" => $text,
);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);

$res = json_decode($res, true);
if (!isset($res["ok"])) {
	WriteLog("[tgmsg][error] res=" . json_encode($res));
} else {
	$sthok = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}msg` SET `tgmessage` = '1' WHERE `city` = :city AND `msg` = :msg");
	foreach ($msgs as $msg) {
		$sthok->bindValue(":city", $msg["city"]);
		$sthok->bindValue(":msg", $msg["msg"]);
		$res = $sthok->execute();
		if ($res === false) {
			WriteLog("[tgmsg][error][updcit] city=" . $msg["name"] . " msg=" . $msg['msg']);
		}
	}
}
