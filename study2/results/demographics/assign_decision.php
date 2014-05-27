<?php

require_once "../../lib/database.php";

// 1 Number of people per condition
$q = "SELECT * FROM `participant_has_condition`";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

$counts = array();
while($r = mysqli_fetch_array($ret))
{
	if(!array_key_exists($r["condition_id"], $counts))
	{
		$counts[$r["condition_id"]] = 1;
	}
	else
	{
		$counts[$r["condition_id"]]++;
	}
}

print_r($counts);

// 2 Average age

// 3 SD of age
			

?>