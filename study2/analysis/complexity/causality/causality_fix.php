<?php

// require_once "../../../lib/build_participants.php";
require_once "../../../lib/database.php";

// for($i = 39; $i > 0; $i--) {
// 	if($i >= 33) {
		// $q = "UPDATE `design_causality` SET participant_id = 1 WHERE participant_id = 0";
		// $ret = $db->q($q);
		// if(!$ret) {
		// 	echo "Fail $i<br />";
		// }
// 	} else {
// 		$q = "UPDATE `design_causality` SET participant_id = " . ($i + 1) . " WHERE participant_id = $i";
// 		$ret = $db->q($q);
// 		if(!$ret) {
// 			echo "Fail $i<br />";
// 		}
// 	}
// }

// $p = isset($_GET["p"]) ? $_GET["p"] : 1;

// $q = "SELECT * FROM `design_causality` WHERE participant_id = $p";
// $ret = $db->q($q);
// if(!$ret) {
// 	echo "Fail $q";
// 	return;
// }

// echo "<ul style='width:100%'>";

// while($r = mysqli_fetch_array($ret)) {
// 	$cause = getThing($r["cause_id"], $r["cause_type"]);
// 	$effect = getThing($r["effect_id"], $r["effect_type"]);
// 	echo "<li><span width='50%'>$cause</span><span width='50%'>$effect</span></li>";
// }

// echo "</ul>";

// function getThing($id, $type) {
// 	global $db;
// 	if($type == "thing") {
// 		$q = "SELECT * FROM `thing` WHERE id = $id";
// 		$ret = $db->q($q);
// 		if(!$ret) {
// 			echo "Fail $q";
// 			return null;
// 		}
// 		$r = mysqli_fetch_array($ret);
// 		return $r["name"];
// 	} else if($type == "split") {
// 		$q = "SELECT * FROM `split_custom` WHERE id = $id";
// 		$ret = $db->q($q);
// 		if(!$ret) {
// 			echo "Fail $q";
// 			return null;
// 		}
// 		$r = mysqli_fetch_array($ret);
// 		return $r["name"];
// 	} else if($type == "custom") {
// 		$q = "SELECT * FROM `participant_has_custom` WHERE id = $id";
// 		$ret = $db->q($q);
// 		if(!$ret) {
// 			echo "Fail $q";
// 			return null;
// 		}
// 		$r = mysqli_fetch_array($ret);
// 		return $r["name"];
// 	} else {
// 		return "todo";
// 	}
// }
