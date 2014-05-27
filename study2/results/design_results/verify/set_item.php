<?php

require_once "../../../lib/database.php";

if(!isset($_POST["what"]))
{
	echo "Fail!<br />";
	return;
}

if($_POST["what"] === "verify")
{
	$q = "UPDATE `custom_is_thing` SET verified = 1 WHERE id = $_POST[id] AND custom_id = $_POST[custom] AND thing_id = $_POST[thing]";
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
else if($_POST["what"] === "verify split")
{
	$q = "UPDATE `split_is_thing` SET verified = 1 WHERE id = $_POST[id] AND split_id = $_POST[split] AND thing_id = $_POST[thing]";
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
else if($_POST["what"] === "unverify")
{
	$q = "UPDATE `custom_is_thing` SET verified = 0 WHERE custom_id = $_POST[custom] AND thing_id = $_POST[thing]";
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
else if($_POST["what"] === "unverify split")
{
	$q = "UPDATE `split_is_thing` SET verified = 0 WHERE split_id = $_POST[split] AND thing_id = $_POST[thing]";
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
	$q = "DELETE FROM `custom_is_thing` WHERE id = $_POST[id] AND custom_id = $_POST[custom] AND thing_id = $_POST[thing]";
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
else if($_POST["what"] === "not option split")
{
	$q = "DELETE FROM `split_is_thing` WHERE id = $_POST[id] AND split_id = $_POST[split] AND thing_id = $_POST[thing]";
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
else if($_POST["what"] === "new match")
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
else if($_POST["what"] === "new match split")
{
	$q = "INSERT INTO `split_is_thing` VALUES('', $_POST[split], $_POST[thing], 0)";
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
else if($_POST["what"] === "done")
{
	$q = "UPDATE `progress_check` SET done_verify = 1 WHERE custom_id = $_POST[custom]";
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
	$q = "UPDATE `progress_check_split` SET done_verify = 1 WHERE split_id = $_POST[split]";
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
else if($_POST["what"] === "undone")
{
	$q = "UPDATE `progress_check` SET done_verify = 1, done_option = 0, done_decision = 0, done_ds = 0, done_16 = 0 WHERE custom_id = $_POST[custom]";
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
else if($_POST["what"] === "undone split")
{
	$q = "UPDATE `progress_check_split` SET done_verify = 1, done_option = 0, done_decision = 0, done_ds = 0, done_16 = 0 WHERE split_id = $_POST[split]";
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