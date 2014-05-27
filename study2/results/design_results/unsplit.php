<?php

require_once "../../lib/database.php";

$q = "SELECT
		sc.id AS split_id, pc.id AS custom_id
		FROM `split_custom` AS sc
		LEFT JOIN `participant_has_custom` AS pc
			ON sc.old_id = pc.id";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

while($r = mysqli_fetch_array($ret))
{
	$q2 = "SELECT thing_id FROM `split_is_thing` WHERE split_id = $r[split_id]";
	$ret2 = $db->q($q2);
	if(!$ret2)
	{
		echo "Fail: $q2<br />";
		continue;
	}

	while($r2 = mysqli_fetch_array($ret2))
	{
		$q3 = "INSERT INTO `custom_is_thing` VALUES('',  $r[custom_id], $r2[thing_id], 0)";
		$ret3 = $db->q($q3);
		if(!$ret)
		{
			echo "fail: $q3<br />";
			continue;
		}

		// $q3 = "INSERT INTO `progress_check` VALUES('', $r[custom_id], 0, 0, 0, 0, 0, 0)";
		// $ret3 = $db->q($q3);
		// if(!$ret)
		// {
		// 	echo "fail: $q3<br />";
		// 	continue;
		// }

		// ACTUALLY DO THE INSERT
	}

	$q2 = "SELECT * FROM `split_has_new` WHERE split_id = $r[split_id]";
	$ret2 = $db->q($q2);
	if(!$ret2)
	{
		echo "Fail: $q2<br />";
		continue;
	}

	while($r2 = mysqli_fetch_array($ret2))
	{
		$q3 = "INSERT INTO `custom_has_new` VALUES('', $r[custom_id], $r2[new_id])";
		$ret3 = $db->q($q3);
		if(!$ret)
		{
			echo "fail: $q3<br />";
			continue;
		}

		// ACTUALLY DO THE INSERT
	}
}

?>