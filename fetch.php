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
$data = [];
foreach ($obj->entry as $row) {
	$msg = $row->summary->__toString();
	if (preg_match('/^\[停班停課通知\](.+?(?:縣|市))(.+)$/', $msg, $m)) {
		echo "$m[1]$m[2]\n";
		if (!isset($data[$m[1]])) {
			$data[$m[1]] = [];
		}
		if (!in_array($m[1] . $m[2], $data[$m[1]])) {
			$data[$m[1]][] = $m[1] . $m[2];
		}
	}
}

echo "-----------------\n";

$test = false;

$sthcity = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}city` SET `status` = :status, `time` = :time, `fbpost` = 0, `fbmessage` = 0, `test` = :test WHERE `city` = :city");
foreach ($D["citylist"] as $city) {
	if (isset($data[$city])) {
		$status = implode("\n", $data[$city]);
	} else {
		$status = $city . ":無停班停課消息";
	}
	echo $status . "\n";

	if ($status != $D["city"][$city]["status"]) {
		$sthcity->bindValue(":status", $status);
		$sthcity->bindValue(":time", $time);
		$sthcity->bindValue(":test", $test);
		$sthcity->bindValue(":city", $city);
		$res = $sthcity->execute();

		WriteLog("[fetch][info][updsta] city=" . $city . " status=form " . $D["city"][$city]["status"] . " to " . $status);
		if ($res === false) {
			WriteLog("[fetch][error][updsta] city=" . $city . " status=" . $status);
		}
	}
}

$C['enableFBpost'] && exec("php " . __DIR__ . "/fbpost.php > /dev/null 2>&1 &");
$C['enableFBmessage'] && exec("php " . __DIR__ . "/fbmessage.php > /dev/null 2>&1 &");
$C['enableTGmessage'] && exec("php " . __DIR__ . "/tgmessage.php > /dev/null 2>&1 &");

WriteLog("[fetch][info] done");
