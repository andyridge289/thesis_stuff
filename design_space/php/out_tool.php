<?php

require_once "database.php";

$TOOL = "node [color=\"#FFFFFF\",fillcolor=\"#FFFFFF\",style=filled, fontcolor=\"#000000\" fontsize=\"22\"];";
$CATEGORY = "node [color=\"047b35\",fillcolor=\"#8df2b6\",style=filled fontsize=\"20\"];";
$DECISION = "node[shape=box,color=\"#004a63\",fillcolor=lightblue2,style=filled fontsize=\"15\"];";
$CHOSEN = "node [color=\"#222222\", style=\"rounded,filled\", shape=rect, fontcolor=\"#000000\", fillcolor=\"#CCCCCC\"];";
$NOT_CHOSEN = "node [color=\"#AAAAAA\", style=\"rounded,filled\", shape=rect, fontcolor=\"#AAAAAA\", fillcolor=\"#EEEEEE\"];";

$categories = array();
$chosen = array();
$notChosen = array();
$decisions = array();

$categoryId = isset($_GET["c"]) ? $_GET["c"] : 1;
$toolId = isset($_GET["t"]) ? $_GET["t"] : 1;

$q = "SELECT * FROM tool WHERE id = $toolId";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$row = mysqli_fetch_array($retval);
$toolName = "\"" . $row["name"] . "\"";

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
	echo "Fail: $q<br />";
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

$q = "SELECT category_id, decision.id AS decision_id, decision.name AS decision_name, `option`.id AS option_id, `option`.name AS option_name FROM decision_has_option INNER JOIN decision ON decision_has_option.decision_id = decision.id INNER JOIN `option` ON decision_has_option.option_id = `option`.id WHERE category_id = $categoryId";
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
	
	$q2 = "SELECT * FROM tool_has_option WHERE tool_id = $toolId AND category_id = $row[category_id] AND decision_id = $row[decision_id] AND option_id = $row[option_id]";
	$retval2 = $database->query($q2);
	//echo $q2;
	if(!$retval2)
	{
		echo "Aaaaaah";
	}
	
	$there = mysqli_num_rows($retval2);
	
	if(!in_array($decision, $decisions))
		array_push($decisions, $decision);

	if($there === 0)
	{
		if(!in_array($option, $notChosen))
			array_push($notChosen, $option);
	}
	else
	{
		if(!in_array($option, $chosen))
			array_push($chosen, $option);
	}
		
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

$output .= "$TOOL \n $toolName \n\n";

$output .= "\n\n$DECISION\n";
$output .= printArray($decisions);

$output .= "\n\n$CHOSEN\n";
$output .= printArray($chosen);

$output .= "\n\n$NOT_CHOSEN\n";
$output .= printArray($notChosen);

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

$output .= "}"; 
echo $output;
$filename = "toolgv/" . $toolId . "_" . $categoryId . ".gv";
$handle = fopen($filename, "w");
fwrite($handle, $output);
fclose($handle);

?>