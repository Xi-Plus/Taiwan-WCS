<?php

$C['FBpageid'] = 'page_id';
$C['FBpagetoken'] = 'page_token';
$C['FBWHtoken'] = 'Webhooks_token';
$C['FBAPI'] = 'https://graph.facebook.com/v2.8/';

$C["DBhost"] = 'localhost';
$C['DBname'] = 'dbname';
$C['DBuser'] = 'user';
$C['DBpass'] = 'pass';
$C['DBTBprefix'] = 'taiwan_wcs_';

$C['fetch'] = 'http://typhdstr.dgpa.gov.tw/?uid=48';

$C['add_limit'] = 5;

$C['LogKeep'] = 86400*7;

$C["allowsapi"] = array("cli");

$G["db"] = new PDO ('mysql:host='.$C["DBhost"].';dbname='.$C["DBname"].';charset=utf8', $C["DBuser"], $C["DBpass"]);

date_default_timezone_set("Asia/Taipei");
