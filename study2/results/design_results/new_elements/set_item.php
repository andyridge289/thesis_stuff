<?php

require_once "../../../lib/database.php";

if(!isset($_POST["what"]))
{
	echo "Fail!<br />";
	return;
}

if($_POST["what"] === "done custom")
{
	$q = "UPDATE `progress_check` SET done_new = 1 WHERE custom_id = $_POST[custom]";
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
	$q = "UPDATE `progress_check_split` SET done_new = 1 WHERE split_id = $_POST[custom]";
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
else if($_POST["what"] === "add new custom")
{
	$q = "INSERT INTO `thing_new` VALUES('', '$_POST[new_thing]')";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
		return;
	}

	$q = "SELECT * FROM `thing_new` WHERE name LIKE '$_POST[new_thing]'";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
		return;
	}

	$r = mysqli_fetch_array($ret);

	$q = "INSERT INTO `custom_has_new` VALUES('', $_POST[custom_id], $r[id])";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "add new split")
{
	$q = "INSERT INTO `thing_new` VALUES('', '$_POST[new_thing]')";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
		return;
	}

	$q = "SELECT * FROM `thing_new` WHERE name LIKE '$_POST[new_thing]'";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
		return;
	}

	$r = mysqli_fetch_array($ret);

	$q = "INSERT INTO `split_has_new` VALUES('', $_POST[custom_id], $r[id])";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "link new custom")
{
	$q = "INSERT INTO `custom_has_new` VALUES('', $_POST[custom_id], $_POST[thing_id])";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "link new split")
{
	$q = "INSERT INTO `split_has_new` VALUES('', $_POST[custom_id], $_POST[thing_id])";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "unverify")
{
	$q = "UPDATE `progress_check` SET done_verify = 0 WHERE custom_id = $_POST[custom_id]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
	}
	else
	{
		echo "win";
	}
}
else if($_POST["what"] === "unverify split")
{
	$q = "UPDATE `progress_check_split` SET done_verify = 0 WHERE split_id = $_POST[custom_id]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q";
	}
	else
	{
		echo "win";
	}
}
else
{
	echo $_POST["what"];
}

?>