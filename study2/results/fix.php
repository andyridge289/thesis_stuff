<?php

require_once "../lib/database.php";

// $q = "SELECT * FROM `to_map`";
// $ret = $db->q($q);
// if(!$ret)
// {
// 	echo "Fail $q<br />";
// 	return;
// }

// `click_count` int(11) NOT NULL,
  // `time_taken` int(11) NOT NULL,

// while($r = mysqli_fetch_array($ret))
// {
// 	$q2 = "SELECT * FROM `$r[old_table]` WHERE id = $r[old_id]";
// 	$ret2 = $db->q($q2);
// 	if(!$ret2)
// 	{
// 		echo "Fail $q2<br />";
// 		continue;
// 	}

// 	while($r2 = mysqli_fetch_array($ret2))
// 	{
// 		echo "$r[old_table](" . $r["old_id"]  . "):  $r2[name]<br />";
// 	}
// }

// $q = "SELECT * FROM `participant_metrics`";
// $ret = $db->q($q);
// if(!$ret)
// {
// 	echo "Fail $q<br />";
// 	return;
// }

// while($r = mysqli_fetch_array($ret))
// {
// 	$q2 = "UPDATE `participant_demographics` 
// 			SET click_count = $r[click_count], 
// 				time_taken = $r[time_taken]
// 			WHERE participant_id = $r[participant_id]";

// 	$ret2 = $db->q($q2);
// 	if(!$ret2)
// 	{
// 		echo "Fail $q2<br />";
// 	}
// }

// $q = "SELECT * FROM `participant_postq`";
// $ret = $db->q($q);
// if(!$ret)
// {
// 	echo "Fail $q<br />";
// 	return;
// }

// while($r = mysqli_fetch_array($ret))
// {
// 	$q2 = "UPDATE `participant_demographics`
// 			SET result_comments = '" . addslashes($r["result_comments"]) . "', 
// 				how_comments = '" . addslashes($r["how_comments"]) . "', 
// 				tool_comments = '" . addslashes($r["tool_comments"]) . "'
// 			WHERE participant_id = $r[participant_id]";

// 	$ret2 = $db->q($q2);
// 	if(!$ret2)
// 	{
// 		echo "Fail $q2<br />";
// 	}
// }

$q = "SELECT * FROM `participant_sc`";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail $q<br />";
	return;
}

while($r = mysqli_fetch_array($ret))
{
	$platformsArr = explode(",", $r["design_platforms"]);
	$purposesArr = explode(",", $r["design_purpose"]); 


	$q2 = "UPDATE `participant_demographics`
		SET design_platforms = '$r[design_platforms]',
			design_purpose = '$r[design_purpose]'
		WHERE participant_id = $r[participant_id]";

		$ret2 = $db->q($q2);
		if(!$ret2)
		{
			echo "Fail $q2<br />";
		}

}

?>