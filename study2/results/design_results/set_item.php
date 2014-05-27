<?php

require_once "../../lib/database.php";

if(!isset($_POST["what"]))
{
	echo "Fail!<br />";
	return;
}

if($_POST["what"] === "option")
{
	$q = "INSERT INTO `custom_is_option` VALUES('', $_POST[custom], $_POST[option], 0)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "split new")
{
	$q = "INSERT INTO `split_is_thing` VALUES('', $_POST[custom], $_POST[thing], 0)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "new")
{
	$q = "INSERT INTO `custom_is_thing` VALUES('', $_POST[custom], $_POST[thing], 0)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "decision")
{
	$q = "INSERT INTO `custom_is_decision` VALUES('', $_POST[custom], $_POST[decision], 0)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "ds")
{
	$q = "INSERT INTO `custom_in_ds` VALUES('', $_POST[custom], $_POST[ds], 0)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "split option")
{
	$q = "INSERT INTO `split_is_option` VALUES('', $_POST[custom], $_POST[option], 0)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "split decision")
{
	$q = "INSERT INTO `split_is_decision` VALUES('', $_POST[custom], $_POST[decision], 0)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "split ds")
{
	$q = "INSERT INTO `split_in_ds` VALUES('', $_POST[custom], $_POST[ds], 0)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "not option")
{
	$q = "INSERT INTO `custom_is_option` VALUES('', $_POST[custom], -1, 0);";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "not decision")
{
	$q = "INSERT INTO `custom_is_decision` VALUES('', $_POST[custom], -1, 0);";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "not ds")
{
	$q = "INSERT INTO `custom_in_ds` VALUES('', $_POST[custom], -1, 0);";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "split not option")
{
	$q = "INSERT INTO `split_is_option` VALUES('', $_POST[custom], -1, 0);";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "split not decision")
{
	$q = "INSERT INTO `split_is_decision` VALUES('', $_POST[custom], -1, 0);";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "split not ds")
{
	$q = "INSERT INTO `split_in_ds` VALUES('', $_POST[custom], -1, 0);";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "split done new")
{
	// If there's a row update it so done_option = 1
		$q = "UPDATE `progress_check_split` SET done_16 = 1 WHERE split_id = $_POST[custom]";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
}
else if($_POST["what"] === "done new")
{
	// See if there's a row in progress check for the custom
	$q = "SELECT * FROM `progress_check` WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		return;
	}

	if(mysqli_num_rows($ret) > 0)
	{
		// If there's a row update it so done_option = 1
		$q = "UPDATE `progress_check` SET done_16 = 1 WHERE custom_id = $_POST[custom]";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
	else
	{
		// If there's no, insert one
		$q = "INSERT INTO `progress_check` VALUES('', $_POST[custom], 0, 0, 0, 1)";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
}
else if($_POST["what"] === "done option")
{
	// See if there's a row in progress check for the custom
	$q = "SELECT * FROM `progress_check` WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		return;
	}

	if(mysqli_num_rows($ret) > 0)
	{
		// If there's a row update it so done_option = 1
		$q = "UPDATE `progress_check` SET done_option = 1 WHERE custom_id = $_POST[custom]";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
	else
	{
		// If there's no, insert one
		$q = "INSERT INTO `progress_check` VALUES('', $_POST[custom], 1, 0, 0)";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
}
else if($_POST["what"] === "done decision")
{
	// See if there's a row in progress check for the custom
	$q = "SELECT * FROM `progress_check` WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		return;
	}

	if(mysqli_num_rows($ret) > 0)
	{
		// If there's a row update it so done_decision = 1
		$q = "UPDATE `progress_check` SET done_decision = 1 WHERE custom_id = $_POST[custom]";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
	else
	{
		// If there's no, insert one
		$q = "INSERT INTO `progress_check` VALUES('', $_POST[custom], 1, 1, 0)";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
}
else if($_POST["what"] === "done ds")
{
	// See if there's a row in progress check for the custom
	$q = "SELECT * FROM `progress_check` WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		return;
	}

	if(mysqli_num_rows($ret) > 0)
	{
		// If there's a row update it so done_decision = 1
		$q = "UPDATE `progress_check` SET done_ds = 1 WHERE custom_id = $_POST[custom]";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
	else
	{
		// If there's no, insert one
		$q = "INSERT INTO `progress_check` VALUES('', $_POST[custom], 1, 1, 1)";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
}
else if($_POST["what"] === "split done option")
{
	// See if there's a row in progress check for the custom
	$q = "SELECT * FROM `progress_check_split` WHERE split_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		return;
	}

	if(mysqli_num_rows($ret) > 0)
	{
		// If there's a row update it so done_option = 1
		$q = "UPDATE `progress_check_split` SET done_option = 1 WHERE split_id = $_POST[custom]";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
	else
	{
		$q = "INSERT INTO `progress_check_split` VALUES('', $_POST[custom], 1, 0, 0)";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}

	// If there's no, insert one
}
else if($_POST["what"] === "split done decision")
{
	// See if there's a row in progress check for the custom
	$q = "SELECT * FROM `progress_check_split` WHERE split_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		return;
	}

	if(mysqli_num_rows($ret) > 0)
	{
		// If there's a row update it so done_option = 1
		$q = "UPDATE `progress_check_split` SET done_decision = 1 WHERE split_id = $_POST[custom]";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
	else
	{
		$q = "INSERT INTO `progress_check_split` VALUES('', $_POST[custom], 1, 1, 0)";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}

	// If there's no, insert one
}
else if($_POST["what"] === "split done ds")
{
	// See if there's a row in progress check for the custom
	$q = "SELECT * FROM `progress_check_split` WHERE split_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		return;
	}

	if(mysqli_num_rows($ret) > 0)
	{
		// If there's a row update it so done_option = 1
		$q = "UPDATE `progress_check_split` SET done_ds = 1 WHERE split_id = $_POST[custom]";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}
	else
	{
		// If there's no, insert one
		$q = "INSERT INTO `progress_check_split` VALUES('', $_POST[custom], 1, 1, 1)";
		$ret = $db->q($q);
		if(!$ret)
		{
			echo "Fail: $q<br />";
		}
		else
		{
			echo "win";
		}
	}

	
}



























/*else if($_POST["what"] === "new option")
{
	$q = "INSERT INTO `custom_new_option` VALUES('', $_POST[custom])";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "new decision")
{
	$q = "INSERT INTO `custom_new_decision` VALUES('', $_POST[custom])";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "new decision solves old")
{
	$q = "INSERT INTO `new_decision_solves_old` VALUES ('', $_POST[custom], $_POST[decision])";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "new option solves old decision")
{
	$q = "INSERT INTO `new_option_solves_old_decision` VALUES ('', $_POST[custom], $_POST[decision])";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail $q:<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "update ds")
{
	$q = "UPDATE `custom_in_ds` SET ds = $_POST[ds] WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "verify ds")
{
	$q = "UPDATE `custom_in_ds` SET verified = 1 WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "update decision")
{
	$q = "UPDATE `custom_is_decision` SET decision_id = $_POST[decision] WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "verify decision")
{
	$q = "UPDATE `custom_is_decision` SET verified = 1 WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "update option")
{
	$q = "UPDATE `custom_is_option` SET decision_id = $_POST[option] WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "verify option")
{
	$q = "UPDATE `custom_is_option` SET verified = 1 WHERE custom_id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}*/
else if($_POST["what"] === "nosplit")
{
	// This means that it's fine and doesn't need to be split
	$q = "UPDATE `participant_has_custom` SET split = 1 WHERE id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "dosplit")
{
	// This means that it needs to be split
	$q = "UPDATE `participant_has_custom` SET split = 2 WHERE id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "ignored")
{
	// This means that it needs to be split
	$q = "UPDATE `participant_has_custom` SET ignore_custom = 1 WHERE id = $_POST[custom]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "add split")
{
	// This means we need to actually add to the split table
	$q = "INSERT INTO `split_custom` VALUES('',$_POST[id], \"$_POST[name]\", \"$_POST[description]\", \"$_POST[rationale]\")";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "done split")
{
	$q = "UPDATE `participant_has_custom` SET split = 3 WHERE id = $_POST[id]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}
	else
	{
		echo "win";
	}
}


?>