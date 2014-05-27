<?php

require_once "database.php";

$CATEGORY = "node [color=\"047b35\",fillcolor=\"#8df2b6\",style=filled, shape=house];";
$DECISION = "node[shape=box,color=\"#004a63\",fillcolor=lightblue2,style=filled];";
$OPTION = "node [color=\"#444444\", style=\"rounded,filled\", shape=rect, fontcolor=\"000000\", fillcolor=\"#DDDDDD\"];";

$categories = array();
$options = array();
$decisions = array();

$categoryId = isset($_GET["c"]) ? $_GET["c"] : 1;

$things = array();
$option_things = array();
$output = "digraph output {";

$q = "SELECT category.name AS category_name, decision.name AS decision_name FROM `decision_has_category` INNER JOIN category ON category.id = decision_has_category.category_id INNER JOIN decision ON decision.id = decision_has_category.decision_id WHERE decision_has_category.category_id = $categoryId";
//echo $q;
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
	//echo "$category -> $decision<br />";

	if(!in_array($category, $categories))
		array_push($categories, $category);
		
	if(!in_array($decision, $decisions))
		array_push($decisions, $decision);

	array_push($things, "\"$category\"->\"$decision\"");
}

$q = "SELECT decision_has_decision.child_id, decision.name AS parent_name FROM decision_has_decision INNER JOIN decision ON decision_has_decision.parent_id = decision.id WHERE decision_has_decision.category_id = $categoryId";
$retval = $database->query($q);
if(!$retval)
{
	//echo "Fail: $q<br />";
	return;
}

while($row = mysqli_fetch_array($retval))
{
	$child = $row["parent_name"];
	
	$q2 = "SELECT name FROM decision WHERE id = $row[child_id]";
	$retval2 = $database->query($q2);
	if(!$retval) continue;
	
	$row2 = mysqli_fetch_array($retval2);
	$parent = $row2["name"];
	
	if(!in_array($parent, $decisions))
		array_push($decisions, $parent);

	if(!in_array($child, $decisions))
		array_push($decisions, $child);
		
	array_push($things, "\"$child\"->\"$parent\"");
}

$q = "SELECT decision.name AS decision_name, `option`.name AS option_name FROM decision_has_option INNER JOIN decision ON decision_has_option.decision_id = decision.id INNER JOIN `option` ON decision_has_option.option_id = `option`.id WHERE category_id = $categoryId";
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
	//echo "$decision -> $option<br />";
	
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
//$output .= printArray($options);

$output .= "\n\n\n";
foreach($things as $thing)
{
	$output .= "\n$thing [arrowhead=none]";
}

$q = "SELECT COUNT(*) AS total FROM tool";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail $q<br />";
	return;
}
$row = mysqli_fetch_array($retval);
$totalTools = $row["total"];

$borderValueFull = 0.1;
$fontValueFull = 0;
$fillValueFull = 0.5;
$borderValueNone = 0.67;
$fontValueNone = 0.4;
$fillValueNone = 1;


$keys = array_keys($option_things);
foreach($keys as $decision)
{
	// Get the ID of the key
	$opts = $option_things[$decision];

	$decisionId = getID($decision, "decision");

	$lastDecision = "";
	$lastOption = "";

	for($i = 0; $i < count($opts); $i++)
	{	
		$optionId = getID($opts[$i], "option");
		
		$q = "SELECT COUNT(*) AS total FROM tool_has_option WHERE decision_id = $decisionId AND option_id = $optionId AND category_id = $categoryId";

		$retval = $database->query($q);
		if(!$retval)
		{
			echo "Fail: $q<br />";
			continue;
		}
		
		$row = mysqli_fetch_array($retval);
		$percentage = $row["total"] / $totalTools;
		
		$borderColour = HSV_TO_RGB(0, 0, getV($borderValueFull, $borderValueNone, $percentage));
		$fontColour = HSV_TO_RGB(0, 0, getV($fontValueFull, $fontValueNone, $percentage));
		$fillColour = HSV_TO_RGB(0, 0, getV($fillValueFull, $fillValueNone, $percentage));
		
		$output .= "node [color=\"#" . $borderColour . "\", style=\"rounded,filled\", shape=rect, fontcolor=\"#" . $fontColour . "\", fillcolor=\"#" . $fillColour . "\"]";
		
		if(strcmp($decision, $lastDecision) === 0)
			$output .= "\n\"$lastOption\"->";
		else
			$output .= "\n\"$decision\"->";
		
		$output .= "\"" . $opts[$i] . "   " . $percentage . "\" [arrowhead=none]";
		
		$lastOption = $opts[$i] . "   " . $percentage;
		$lastDecision = $decision;
	}
	
	$output .= " [arrowhead=none]";
}

$output .= "}"; 
$handle = fopen("alltoolgv/$categoryId.gv", "w");
fwrite($handle, $output);
fclose($handle);

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

function getID($name, $table)
{
	global $database;
	$q = "SELECT id FROM `$table` WHERE name = '$name'";
	
	$retval = $database->query($q);
	if(!$retval)
	{
		echo "Fail: $q<br />";
		return -1;
	}
	
	$row = mysqli_fetch_array($retval);
	return $row["id"];
}

function getV($top, $bottom, $percentage)
{
	$distance = $top - $bottom;
	$distance *= $percentage;
	return $distance + $bottom;
}

function HSV_TO_RGB ($H, $S, $V) // HSV Values:Number 0-1
{ // RGB Results:Number 0-255
	$RGB = array();

	if($S == 0)
	{
		$R = $G = $B = $V * 255;
	}
	else
	{
		$var_H = $H * 6;
		$var_i = floor( $var_H );
		$var_1 = $V * ( 1 - $S );
		$var_2 = $V * ( 1 - $S * ( $var_H - $var_i ) );
		$var_3 = $V * ( 1 - $S * (1 - ( $var_H - $var_i ) ) );

		if ($var_i == 0) { $var_R = $V ; $var_G = $var_3 ; $var_B = $var_1 ; }
		else if ($var_i == 1) { $var_R = $var_2 ; $var_G = $V ; $var_B = $var_1 ; }
		else if ($var_i == 2) { $var_R = $var_1 ; $var_G = $V ; $var_B = $var_3 ; }
		else if ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2 ; $var_B = $V ; }
		else if ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1 ; $var_B = $V ; }
		else { $var_R = $V ; $var_G = $var_1 ; $var_B = $var_2 ; }

		$R = $var_R * 255;
		$G = $var_G * 255;
		$B = $var_B * 255;
	}

	$RGB['R'] = dechex($R);
	$RGB['G'] = dechex($G);
	$RGB['B'] = dechex($B);

	return $RGB['R'] . $RGB['G'] . $RGB['B'];
}

?>