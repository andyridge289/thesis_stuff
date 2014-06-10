<?php

require_once "../lib/database.php";

print_r($_POST);

if(isset($_POST["custom"])) {

	$q = "INSERT INTO `classified_custom` VALUES('', $_POST[custom], $_POST[classifier])";
	$ret = $db->q($q);
	if(!$ret) {
		echo "Fail $q";
	} else {
		echo "win";
	}
} else if(isset($_POST["split"])) {
	$q = "INSERT INTO `classified_split` VALUES('', $_POST[split], $_POST[classifier])";
	$ret = $db->q($q);
	if(!$ret) {
		echo "Fail $q";
	} else {
		echo "win";
	}
} else if(isset($_POST["thing"])) {
	$q = "INSERT INTO `classified_thing` VALUES('', $_POST[thing], $_POST[classifier])";
	$ret = $db->q($q);
	if(!$ret) {
		echo "Fail $q";
	} else {
		echo "win";
	}
}

?>