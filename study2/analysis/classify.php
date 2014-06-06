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
}

?>