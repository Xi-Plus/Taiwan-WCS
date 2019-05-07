<?php
function TimediffFormat($time) {
	if ($time < 60) {
		return $time . "秒";
	}

	if ($time < 60 * 50) {
		return round($time / 60) . "分";
	}

	if ($time < 60 * 60 * 23.5) {
		return round($time / (60 * 60)) . "小時";
	}

	return round($time / (60 * 60 * 24)) . "天";
}
