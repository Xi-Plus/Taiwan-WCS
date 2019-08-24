<?php
require __DIR__ . '/config/config.php';
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}

require __DIR__ . '/function/log.php';
require __DIR__ . '/function/curl.php';
require __DIR__ . '/function/sendmessage.php';
require __DIR__ . '/function/getlist.php';
require __DIR__ . '/function/timediff.php';

$time = date("Y-m-d H:i:s");

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}msg` WHERE `fbmessage` = 0 ORDER BY `time` ASC");
$sth->execute();
$msgs = $sth->fetchAll(PDO::FETCH_ASSOC);

$sthfol = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}follow` WHERE `city` = :city");
$sthok = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}msg` SET `fbmessage` = '1' WHERE `city` = :city");
$sthmsg = $G["db"]->prepare("INSERT INTO `{$C['DBTBprefix']}msgqueue` (`tmid`, `message`, `time`, `hash`) VALUES (:tmid, :message, :time, :hash)");
foreach ($msgs as $msg) {
	$text = date("Y/m/d H:i", strtotime($msg['time'])) . " " . $msg["msg"];
	echo "$text\n";

	$sthfol->bindValue(":city", $msg["city"]);
	$sthfol->execute();
	$users = $sthfol->fetchAll(PDO::FETCH_ASSOC);
	foreach ($users as $user) {
		echo "\tPrepare to send to " . $user["tmid"] . "\n";
		$hash = md5(json_encode(array("tmid" => $user["tmid"], "message" => $text, "time" => $time)));
		$sthmsg->bindValue(":tmid", $user["tmid"]);
		$sthmsg->bindValue(":message", $text);
		$sthmsg->bindValue(":time", $time);
		$sthmsg->bindValue(":hash", $hash);
		$res = $sthmsg->execute();
		if ($res === false) {
			WriteLog("[fbmsg][error][insque] tmid=" . $user["tmid"] . " msg=" . $text . " " . json_encode($sthmsg->errorInfo()));
		}
	}

	$sthok->bindValue(":city", $msg["city"]);
	$res = $sthok->execute();
	if ($res === false) {
		WriteLog("[fbmsg][error][updcit] city=" . $msg["city"]);
	}
}

echo "-----------------\n";

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}msgqueue` ORDER BY `time` ASC");
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);

$sthdel = $G["db"]->prepare("DELETE FROM `{$C['DBTBprefix']}msgqueue` WHERE `hash` = :hash");
$sthread = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}user` SET `lastread` = :lastread WHERE `tmid` = :tmid");
foreach ($row as $msg) {
	echo "Send to " . $msg['tmid'] . " with msg: " . $msg['message'] . "\n";
	$res = SendMessage($msg["tmid"], $msg["message"]);
	$sthread->bindValue(":lastread", date("Y-m-d H:i:s"));
	$sthread->bindValue(":tmid", $msg["tmid"]);
	$sthread->execute();
	if ($res === true || $res["code"] == 230) {
		$sthdel->bindValue(":hash", $msg["hash"]);
		$res = $sthdel->execute();
		if ($res === false) {
			WriteLog("[fbmsg][error][delque] hash=" . $msg["hash"]);
		}
	}
}
