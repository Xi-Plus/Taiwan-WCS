<?php
$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}city` ORDER BY `no` ASC");
$res = $sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D["city"] = [];
$D["citylist"] = [];
foreach ($row as $value) {
	$D["city"][$value["city"]] = $value;
	$D["city"][$value["city"]]['status'] = '';
	$D["citylist"][] = $value["city"];
}

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}cityshortname`");
$res = $sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D["cityshortname"] = [];
foreach ($row as $value) {
	$D["cityshortname"][$value["shortname"]] = $value["fullname"];
}

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}msg`");
$res = $sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($row as $msg) {
	$D['city'][$msg['city']]['status'] .= date('Y/m/d H:i', strtotime($msg['time'])) . " " . $msg['msg'] . "\n";
}

foreach ($D["citylist"] as $city) {
	if ($D['city'][$city]['status'] === '') {
		$D['city'][$city]['status'] = $city . ':無任何停班停課消息';
	}
}

function getuserlist($tmid) {
	global $C, $G;
	$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}follow` WHERE `tmid` = :tmid");
	$sth->bindValue(":tmid", $tmid);
	$res = $sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	$data = [];
	foreach ($row as $temp) {
		$data[] = $temp["city"];
	}
	return $data;
}
