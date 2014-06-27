<?php

require_once "../../../lib/database.php";

// Indicate that the participant is finished
if(isset($_GET["done"])) {

	$q = "INSERT INTO `design_causality_plist` VALUES('', $_GET[done], NOW())";
	$ret = $db->q($q);
	if(!$ret) {
		echo "fail $q<br />";
		return;
	} else {
		echo "win";
	}
}
else if(isset($_GET["add"])) {
	
	$bijective = isset($_GET["b"]) ? $_GET["b"] : 0;
	
	$q = "INSERT INTO `design_causality` VALUES('', $_GET[p], $_GET[cause_id], '$_GET[cause_type]', $_GET[effect_id], '$_GET[effect_type]', $bijective, NOW())";
	$ret = $db->q($q);
	if(!$ret) {
		echo "Fail $q";
		return;
	} else {
		echo "win";
	}
}
else {
	echo "nope";
}

?>