<?php

require_once "database.php";

// Add the name to get the ID of the tool back
if(!isset($_POST["tool_name"]))
{
	echo "Fail. Tool name not set.";
	return;
}

$toolId = getToolId($_POST["tool_name"]);

if($toolId == -1)
{
	$toolId = addTool($_POST["tool_name"]);
}

// Loop through the POST array and find all the keys and then they are the ones that we need to mark
$keys = array_keys($_POST);

for($i = 0; $i < count($keys); $i++)
{
	if(strcmp($keys[$i], "tool_name") === 0)
		continue;
	
	$key = explode("_", $keys[$i]);
	
	$q = "INSERT INTO tool_has_option VALUES('', $toolId, $key[0], $key[1], $key[2])";
	$retval = $database->query($q);
	if(!$retval)
	{
		echo "Fail fail fail: $q<br />";
	}
}

function getToolId($name)
{
	global $database;
	$q = "SELECT id FROM tool WHERE name = '$name'";
	$retval = $database->query($q);
	if(!$retval)
	{
		return -1;	
	}
		
	if(mysqli_num_rows($retval) === 0)
	{
		return -1;
	}
		
	$row = mysqli_fetch_array($retval);
	return $row["id"];
}	

function addTool($name)
{
	global $database;
	$q = "INSERT INTO tool VALUES('', '$name')";
	$retval = $database->query($q);
	if(!$retval)
		return -1;
		
	return getToolId($name);
}

?>