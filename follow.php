<?php
require(__DIR__.'/config/config.php');
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}

require(__DIR__.'/function/curl.php');
require(__DIR__.'/function/log.php');
require(__DIR__.'/function/sendmessage.php');
require(__DIR__.'/function/getlist.php');

$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}input` ORDER BY `time` ASC");
$res = $sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($row as $data) {
	$sth = $G["db"]->prepare("DELETE FROM `{$C['DBTBprefix']}input` WHERE `hash` = :hash");
	$sth->bindValue(":hash", $data["hash"]);
	$res = $sth->execute();
}
function GetTmid() {
	global $C, $G;
	$res = cURL($C['FBAPI']."me/conversations?fields=participants,updated_time&access_token=".$C['FBpagetoken']);
	$updated_time = file_get_contents("data/updated_time.txt");
	$newesttime = $updated_time;
	while (true) {
		if ($res === false) {
			WriteLog("[follow][error][getuid]");
			break;
		}
		$res = json_decode($res, true);
		if (count($res["data"]) == 0) {
			break;
		}
		foreach ($res["data"] as $data) {
			if ($data["updated_time"] <= $updated_time) {
				break 2;
			}
			if ($data["updated_time"] > $newesttime) {
				$newesttime = $data["updated_time"];
			}
			foreach ($data["participants"]["data"] as $participants) {
				if ($participants["id"] != $C['FBpageid']) {
					$sth = $G["db"]->prepare("INSERT INTO `{$C['DBTBprefix']}user` (`uid`, `tmid`, `name`) VALUES (:uid, :tmid, :name)");
					$sth->bindValue(":uid", $participants["id"]);
					$sth->bindValue(":tmid", $data["id"]);
					$sth->bindValue(":name", $participants["name"]);
					$res = $sth->execute();
					break;
				}
			}
		}
		$res = cURL($res["paging"]["next"]);
	}
	file_put_contents("data/updated_time.txt", $newesttime);
}
foreach ($row as $data) {
	$input = json_decode($data["input"], true);
	foreach ($input['entry'] as $entry) {
		foreach ($entry['messaging'] as $messaging) {
			$sid = $messaging['sender']['id'];
			$sth = $G["db"]->prepare("SELECT * FROM `{$C['DBTBprefix']}user` WHERE `sid` = :sid");
			$sth->bindValue(":sid", $sid);
			$sth->execute();
			$row = $sth->fetch(PDO::FETCH_ASSOC);
			if ($row === false) {
				GetTmid();
				$mmid = "m_".$messaging['message']['mid'];
				$res = cURL($C['FBAPI'].$mmid."?fields=from&access_token=".$C['FBpagetoken']);
				$res = json_decode($res, true);
				$uid = $res["from"]["id"];
				$sthsid = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}user` SET `sid` = :sid WHERE `uid` = :uid");
				$sthsid->bindValue(":sid", $sid);
				$sthsid->bindValue(":uid", $uid);
				$sthsid->execute();

				$sth->execute();
				$row = $sth->fetch(PDO::FETCH_ASSOC);
				if ($row === false) {
					WriteLog("[follow][error][uid404] sid=".$sid." uid=".$uid);
					continue;
				} else {
					WriteLog("[follow][info][newuser] sid=".$sid." uid=".$uid);
				}
			}
			$tmid = $row["tmid"];
			if (isset($messaging['read'])) {
				$sth = $G["db"]->prepare("UPDATE `{$C['DBTBprefix']}user` SET `lastread` = :lastread WHERE `tmid` = :tmid");
				$sth->bindValue(":lastread", "2038-01-19 03:04:17");
				$sth->bindValue(":tmid", $tmid);
				$res = $sth->execute();
				WriteLog("[read] ".$sid);
				continue;
			}
			if (!isset($messaging['message']['text'])) {
				SendMessage($tmid, "僅接受文字訊息");
				continue;
			}
			$msg = $messaging['message']['text'];
			if ($msg[0] !== "/") {
				SendMessage($tmid, "無法辨識的訊息\n".
					"本粉專由機器人自動運作\n".
					"啟用訊息通知輸入 /add\n".
					"顯示所有命令輸入 /help");
				continue;
			}
			$msg = str_replace("\n", " ", $msg);
			$msg = preg_replace("/\s+/", " ", $msg);
			$cmd = explode(" ", $msg);
			switch ($cmd[0]) {
				case '/add':
					if (!isset($cmd[1])) {
						$msg = "輸入 /add [縣市] 接收此縣市的通知\n\n".
							"可用的縣市有：".implode("、", $D["citylist"])."\n\n".
							"範例： /add ".$D["citylist"][0];
						SendMessage($tmid, $msg);
						break;
					}
					$city = $cmd[1];
					if (isset($cmd[2])) {
						SendMessage($tmid, "參數個數錯誤\n".
							"必須提供1個參數為縣市");
						break;
					}
					if (isset($D["city"][$city])) {
						$user = getuserlist($tmid);
						if (!in_array($city, $user)) {
							$sth = $G["db"]->prepare("INSERT INTO `{$C['DBTBprefix']}follow` (`tmid`, `city`) VALUES (:tmid, :city)");
							$sth->bindValue(":tmid", $tmid);
							$sth->bindValue(":city", $city);
							$res = $sth->execute();
							SendMessage($tmid, "已開始接收 ".$city." 的通知\n".
								"當人事行政總處網頁有你設定縣市的內容更新時，將會主動發送訊息告知");
						} else {
							SendMessage($tmid, $city." 已經接收過了\n".
								"要取消請使用 /del");
						}
					} else {
						$msg = "找不到此縣市，縣市名必須用字完全一樣\n".
							"輸入 /add [縣市] 接收此縣市的通知\n\n".
							"可用的縣市有：".implode("、", $D["citylist"])."\n\n".
							"範例： /add ".$D["citylist"][0];
						SendMessage($tmid, $msg);
					}
					break;

				case '/del':
					$user = getuserlist($tmid);
					if (!isset($cmd[1])) {
						$msg = "輸入 /del [縣市] 取消接收此縣市通知\n\n";
						if (count($user) == 0) {
							$msg .= "沒有接收任何縣市通知\n".
								"輸入 /add [縣市] 開始接收縣市通知";
						} else {
							$msg .= "接收的縣市有：".implode("、", $user)."\n\n".
								"範例： /del ".$user[0];
						}
						SendMessage($tmid, $msg);
						break;
					}
					$city = $cmd[1];
					if (!isset($D["city"][$city])) {
						SendMessage($tmid, "找不到 ".$city." ，縣市名必須用字完全一樣");
					} else if (in_array($city, $user)) {
						$sth = $G["db"]->prepare("DELETE FROM `{$C['DBTBprefix']}follow` WHERE `tmid` = :tmid AND `city` = :city");
						$sth->bindValue(":tmid", $tmid);
						$sth->bindValue(":city", $city);
						$res = $sth->execute();
						SendMessage($tmid, "已停止接收 ".$city." 的通知");
					} else {
						SendMessage($tmid, "並沒有接收 ".$city." 的通知");
					}
					break;

				case '/list':
					$user = getuserlist($tmid);
					if (count($user) == 0) {
						SendMessage($tmid, "沒有接收任何縣市\n".
							"輸入 /add [縣市] 開始接收縣市通知");
					} else {
						$msg = "已接收以下縣市通知\n";
						foreach ($user as $city) {
							$msg .= $city."\n";
						}
						$msg .= "\n".
							"/del 停止縣市通知";
						SendMessage($tmid, $msg);
					}
					break;
				
				case '/show':
					$user = getuserlist($tmid);
					if (count($user) == 0) {
						SendMessage($tmid, "沒有接收任何縣市\n".
							"輸入 /add [縣市] 開始接收縣市通知");
					} else {
						$msg = "";
						foreach ($user as $city) {
							$msg .= $city."：「".$D["city"][$city]["status"]."」\n";
						}
						$msg .= "\n".
							"/del 停止縣市通知";
						SendMessage($tmid, $msg);
					}
					break;
				
				case '/help':
					if (isset($cmd[2])) {
						$msg = "參數過多\n".
							"必須給出一個參數為指令的名稱";
					} else if (isset($cmd[1])) {
						switch ($cmd[1]) {
							case 'add':
								$msg = "/add 顯示所有區域\n".
									"/add [縣市] 接收縣市通知";
								break;
							
							case 'del':
								$msg = "/del [縣市] 停止此縣市通知";
								break;
							
							case 'list':
								$msg = "/list 列出已接收通知的縣市";
								break;
							
							case 'show':
								$msg = "/show 列出已接收通知縣市的資料";
								break;
							
							case 'help':
								$msg = "/help 顯示所有命令";
								break;
							
							default:
								$msg = "查無此指令";
								break;
						}
					} else {
						$msg = "可用命令\n".
						"/add 接收縣市通知\n".
						"/del 停止縣市通知\n".
						"/list 列出已接收通知的縣市\n".
						"/show 列出已接收通知縣市的訊息\n".
						"/help 顯示所有命令\n\n".
						"/help [命令] 顯示命令的詳細用法";
					}
					SendMessage($tmid, $msg);
					break;
				
				default:
					SendMessage($tmid, "無法辨識命令\n".
						"輸入 /help 取得可用命令");
					break;
			}
		}
	}
}
