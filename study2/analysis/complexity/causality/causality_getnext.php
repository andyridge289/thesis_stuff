<?php

require_once "../../../lib/database.php";

// Find the largest left that we've got and the largest right that we've got
$q = "SELECT * FROM `design_causality_plist` ORDER BY participant_id DESC";
$ret = $db->q($q);
if(!$ret) {
	echo "Fail $q";
	return;
}

$nextId = 1;
if(mysqli_num_rows($ret) > 0) {
	$r = mysqli_fetch_array($ret);
	$nextId += $r["participant_id"];
}

echo "var nextPid = $nextId;";

?>