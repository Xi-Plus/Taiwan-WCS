<?php
$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}city` ORDER BY `no` ASC");
$res = $sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D["city"] = array();
$D["citylist"] = array();
foreach ($row as $value) {
	$D["city"][$value["city"]] = $value;
	$D["citylist"][] = $value["city"];
}

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}cityshortname`");
$res = $sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D["cityshortname"] = array();
foreach ($row as $value) {
	$D["cityshortname"][$value["shortname"]] = $value["fullname"];
}

function getuserlist($tmid) {
	global $C, $G;
	$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}follow` WHERE `tmid` = :tmid");
	$sth->bindValue(":tmid", $tmid);
	$res = $sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	$data = array();
	foreach ($row as $temp) {
		$data[] = $temp["city"];
	}
	return $data;
}
