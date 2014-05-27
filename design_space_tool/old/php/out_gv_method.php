<?php

require_once "database.php";

$CATEGORY = "node [color=\"047b35\",fillcolor=\"#8df2b6\",style=filled, shape=house];";
$DECISION = "node[shape=box,color=\"#004a63\",fillcolor=lightblue2,style=filled];";
$OPTION = "node [color=\"#444444\", style=\"rounded,filled\", shape=rect, fontcolor=\"black\", fillcolor=\"#DDDDDD\"];";

$categories = array();
$options = array();
$decisions = array();

$categoryId = isset($_GET["c"]) ? $_GET["c"] : 1;
$step = isset($_GET["s"]) ? $_GET["s"] : 100;

$things = array();
$option_things = array();
$output = "digraph output {";

$q = "SELECT c.name AS category_name, d.name AS decision_name 
		FROM `decision_has_category` AS dc
		INNER JOIN category AS c ON c.id = dc.category_id 
		INNER JOIN decision AS d ON d.id = dc.decision_id 
		WHERE dc.category_id = $categoryId";
		
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

while($row = mysqli_fetch_array($retval))
{
	$category = $row["category_name"];
	$decision = $row["decision_name"];

	if(!in_array($category, $categories))
		array_push($categories, $category);
		
	if(!in_array($decision, $decisions))
		array_push($decisions, $decision);

	array_push($things, "\"$category\"->\"$decision\"");
}

$q = "SELECT dd.child_id, d.name AS parent_name 
		FROM decision_has_decision AS dd
		INNER JOIN decision AS d ON dd.parent_id = d.id 
		WHERE dd.category_id = $categoryId";
		
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

while($row = mysqli_fetch_array($retval))
{
	$parent = $row["parent_name"];
	
	$q2 = "SELECT name FROM decision WHERE id = $row[child_id]";
	$retval2 = $database->query($q2);
	if(!$retval) continue;
	
	$row2 = mysqli_fetch_array($retval2);
	$child = $row2["name"];
	
	if(!in_array($child, $decisions))
	{
		echo "Added decision $child<br />";
		array_push($decisions, $child);
	}

	if(!in_array($parent, $decisions))
		array_push($decisions, $parent);
		
	array_push($things, "\"$parent\"->\"$child\"");
}

$q = "SELECT d.name AS decision_name, o.name AS option_name 
		FROM decision_has_option AS do
		INNER JOIN decision AS d ON do.decision_id = d.id 
		INNER JOIN `option` AS o ON do.option_id = o.id 
		WHERE category_id = $categoryId";

$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

while($row = mysqli_fetch_array($retval))
{
	$decision = $row["decision_name"];
	$option = $row["option_name"];
	
	if(!in_array($decision, $decisions))
		array_push($decisions, $decision);

	if(!in_array($option, $options))
		array_push($options, $option);
		
	if(!array_key_exists($decision, $option_things))
	{
		$option_things["$decision"] = array();
		array_push($option_things["$decision"], $option);
	}
	else
	{
		array_push($option_things["$decision"], $option);
	}
}

$output .= "$CATEGORY\n";
$output .= printArray($categories);

$output .= "\n\n$DECISION\n";
$output .= printArray($decisions);

$output .= "\n\n$OPTION\n";
$output .= printArray($options);

$output .= "\n\n\n";
foreach($things as $thing)
{
	$output .= "\n$thing [arrowhead=none]";
}

$keys = array_keys($option_things);
foreach($keys as $key)
{
	$output .= "\n\"$key\"->";
	$opts = $option_things[$key];
	for($i = 0; $i < count($opts); $i++)
	{
		$output .= "\"" . $opts[$i] . "\"";
		if($i < count($opts) - 1) $output .= "->";
	}
	
	$output .= " [arrowhead=none]";
}

function printArray($array)
{
	$arr = "";
	for($i = 0; $i < count($array); $i++)
	{
		$arr .= "\"" . $array[$i] . "\"";
		if($i < count($array) - 1) $arr .= ",";
	}
	
	return $arr;
}

class Thing
{
	public $name;
	public $step;
	
	function Thing($name, $step)
	{
		$this->name = $name;
		$this-
	}
}

$output .= "}"; 
$handle = fopen("gv/method_$categoryId.gv", "w");
fwrite($handle, $output);
fclose($handle);

?>