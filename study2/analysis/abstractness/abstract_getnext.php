<?php

require_once "../../lib/database.php";

// Find the largest left that we've got and the largest right that we've got
$q = "SELECT * FROM `design_abstractness` ORDER BY a DESC, b DESC";
$ret = $db->q($q);
if(!$ret) {
	echo "Fail $q";
	return;
}

$a = 0;
$b = 0;
if(mysqli_num_rows($ret) > 0) {
	$r = mysqli_fetch_array($ret);
	$a = $r["a"];
	$b = $r["b"] + 1;
}

echo "var startA = " . $a . "; var startB = " . $b;

?>