<?php
$res = file_get_contents("msgparsegen_in.txt");
$res = str_replace("\"", "", $res);
$res = str_replace("\t", ",", $res);
$res = str_replace("\n", "/", $res);
$res = str_replace(",*/", "\n", $res);
$res = str_replace(",*", "", $res);
$res = str_replace("/", "|", $res);
$res = str_replace("?", '\?', $res);
echo $res;
$res = explode("\n", $res);
$shift = "\t";
$out = "";
foreach ($res as $cnt => $temp) {
	$temp2 = explode(",", $temp);
	$out .= $shift . '// ' . ($cnt + 1) . "\n";
	$out .= $shift . 'if (preg_match("/';
	for ($i = 0; $i <= 2; $i++) {
		if ($temp2[$i] === "") {
			break;
		}
		if ($i) {
			$out .= '.*';
		}
		$out .= '(' . $temp2[$i] . ')';
	}
	$out .= '/';
	if (preg_match("/[A-Za-z]/", $temp)) {
		$out .= 'i';
	}
	$out .= '", $msg)) {' . "\n";
	$out .= $shift . "\t" . 'return ';
	if (preg_match("/[A-Za-z_]/", $temp2[3])) {
		$out .= '$' . $temp2[3];
	} else {
		$out .= '"' . $temp2[3] . '"';
	}
	$out .= ";\n";
	$out .= $shift . '}' . "\n";
}
file_put_contents("msgparsegen_out.txt", $out);
