<?php

require_once "database.php";

$categories = getArray("category");
$decisions = getArray("decision");
$options = getArray("option");

$filename = $_GET["f"];
$file = File($filename);
echo "Connect $filename<br />";

// The category is always the 0th element
$category = trim(str_replace("\"", "", $file[0]));
$connections = array();

$parentDecisions = array();
for($i = 1; $i < count($file); $i++)
{
	$line = trim(str_replace("\"", "", $file[$i]));
	
	if(strcmp($line, "") === 0)
	{
		array_pop($parentDecisions);
	}
	else if(isDecision($line))
	{
		// If it's a decision then we need to either connect it to the category or 
		// connect it to its parent decision
		if(count($parentDecisions) == 0)
		{
			// Then we need to connect to the category
			echo "$category->$line<br />";
			$q = "INSERT INTO `decision_has_category` VALUES('', " . getId($line, $decisions) . "," . getId($category, $categories) . ")";
			$retval = $database->query($q);
			if(!$retval) echo "Fail: $q<br />";
		}
		else 
		{
			// Then we need to connect our decision to whatever is on the end of the array
			$parent = $parentDecisions[count($parentDecisions) - 1];
			//echo "$parent->$line<br />";
			$q = "INSERT INTO `decision_has_decision` VALUES('', " . getId($line, $decisions) . "," . getId($parent, $decisions) . "," . getId($category, $categories) . ")";
			$retval = $database->query($q);
			if(!$retval) echo "Fail: $q<br />";
		}
		
		array_push($parentDecisions, $line);
	}
	else if(isOption($line))
	{
		// Then we need to connect it to the last decision
		$parent = $parentDecisions[count($parentDecisions) - 1];
		echo "Parent: $parent<br />";
		$q = "INSERT INTO `decision_has_option` VALUES('', " . getId($parent, $decisions) . "," . getId($line, $options) . "," . getId($category, $categories) . ")";
		$retval = $database->query($q);
		if(!$retval) echo "Fail: $q<br />";
	}
	else
	{
		echo "CRAP: $line <br />";
	}
}

function isOption($name)
{
	global $options;
	return isThing($name, $options);
}

function isDecision($name)
{
	global $decisions;
	return isThing($name, $decisions);
}

function isThing($name, $array)
{
	for($i = 0; $i < count($array); $i++)
	{
		if(strcmp($array[$i]->name, $name) === 0)
			return true;
	}
	
	return false;
}

function getId($name, $array)
{
	//echo "Getting ID: $name  ";
	for($i = 0; $i < count($array); $i++)
	{
		if(strcmp($array[$i]->name, trim($name)) === 0)
		{
			//echo $array[$i]->id . "<br />";
			return $array[$i]->id;
				
		}
	}
	
	echo "Not found: $name<br />";
	//print_r($array);
}

function getArray($name)
{
	global $database;

	$q = "SELECT * FROM `$name`";
	$retval = $database->query($q);
	if(!$retval) return;
	$array = array();
	
	while($row = mysqli_fetch_array($retval))
	{
		array_push($array, new Thing($row["id"], $row["name"]));
	}
	
	return $array;
}

class Thing
{
	public $id;
	public $name;
	
	function Thing($id, $name)
	{
		$this->id = $id;
		$this->name = $name;  
	}
}

?>