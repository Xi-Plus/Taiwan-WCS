<?php
require __DIR__ . '/config/config.php';
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}

require __DIR__ . '/function/curl.php';
require __DIR__ . '/function/log.php';
require __DIR__ . '/function/getlist.php';

$time = date("Y-m-d H:i:s");

echo "fetching from {$C["fetch"]}\n";
$html = file_get_contents($C["fetch"]);
if ($html === false) {
	WriteLog("fetch fail");
	exit;
}

$obj = simplexml_load_string($html);
$newrecord = [];
foreach ($D["citylist"] as $city) {
	$newrecord[$city] = [];
}
foreach ($obj->entry as $row) {
	$msg = $row->summary->__toString();
	if (preg_match('/^\[停班停課通知\](.+?(?:縣|市))(.+)$/', $msg, $m)) {
		echo "$m[1]$m[2]\n";
		if (!in_array($m[1] . $m[2], $newrecord[$m[1]])) {
			$newrecord[$m[1]][] = $m[1] . $m[2];
		}
	}
}

echo "-----------------\n";

$test = false;

$oldrecord = [];
foreach ($D["citylist"] as $city) {
	$oldrecord[$city] = [];
}
$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}msg`");
$sth->execute();
$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
	$oldrecord[$row['city']][] = $row['msg'];
}

$sthcity = $G["db"]->prepare("INSERT INTO `{$C['DBTBprefix']}msg` (`city`, `msg`) VALUES (:city, :msg)");
foreach ($D["citylist"] as $city) {
	foreach ($newrecord[$city] as $msg) {
		if (!in_array($msg, $oldrecord[$city])) {
			$sthcity->bindValue(":city", $city);
			$sthcity->bindValue(":msg", $msg);
			$res = $sthcity->execute();

			WriteLog("[fetch][info][insert] city=" . $city . " status=" . $msg);
			if ($res === false) {
				WriteLog("[fetch][error][insert] city=" . $city . " status=" . $status);
			}
		}
	}
}

$C['enableFBpost'] && exec("php " . __DIR__ . "/fbpost.php > /dev/null 2>&1 &");
$C['enableFBmessage'] && exec("php " . __DIR__ . "/fbmessage.php > /dev/null 2>&1 &");
$C['enableTGmessage'] && exec("php " . __DIR__ . "/tgmessage.php > /dev/null 2>&1 &");

WriteLog("[fetch][info] done");
