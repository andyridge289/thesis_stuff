<?php

require_once "../../lib/database.php";

if(!isset($_GET["a"]) || !isset($_GET["b"]) || !isset($_GET["a_b"])) {
	echo "fail, not set";
	return;
}

$q = "INSERT INTO `design_specificity` VALUES('', $_GET[a], $_GET[b], $_GET[a_b], NOW())";
$ret = $db->q($q);
if(!$ret) {
	echo "fail: $q";
} else {
	echo "win";
}

?>