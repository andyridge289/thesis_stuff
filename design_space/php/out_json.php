<?php

require_once "database.php";
require_once "tree.php";

$count=0;

$categoryId = $_GET["c"] == null ? 1 : $_GET["c"];

$things = array();
$option_things = array();

$q = "SELECT category.name AS category_name, decision.name AS decision_name FROM `decision_has_category` INNER JOIN category ON category.id = decision_has_category.category_id INNER JOIN decision ON decision.id = decision_has_category.decision_id WHERE decision_has_category.category_id = $categoryId";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$treeTop = new Tree("Top", "", $count++);

while($row = mysqli_fetch_array($retval))
{
	$category = $row["category_name"];
	$decision = $row["decision_name"];

	//if(!inArray($categories, $category))
	//	array_push($categories, $category);
		
	//if(!inArray($decisions, $decision))
	//	array_push($decisions, $decision);
		
	$treeTop->addChild($category, "Category", $count++);
	$treeTop->addChildTo($decision, "Decision", $count++, $category);
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
	
	//if(!inArray($decisions, $parent))
	//	array_push($decisions, $parent);

	//if(!inArray($decisions, $child))
	//	array_push($decisions, $child);
	
	// Are these two really the wrong way around? I should fix this...
	$treeTop->addChildTo($child, "Decision", $count++, $parent);
	//array_push($things, "\"$child\"->\"$parent\"");
}

$q = "SELECT decision.name AS decision_name, `option`.name AS option_name FROM decision_has_option INNER JOIN decision ON decision_has_option.decision_id = decision.id INNER JOIN `option` ON decision_has_option.option_id = `option`.id WHERE category_id = $categoryId";
$retval = $database->query($q);
if(!$retval)
{
	return;
}

while($row = mysqli_fetch_array($retval))
{
	$decision = $row["decision_name"];
	$option = $row["option_name"];
	
	$treeTop->addChildTo($option, "Option", $count++, $decision);
	
	//if(!inArray($decisions, $decision))
	//	array_push($decisions, $decision);

	//if(!inArray($options, $option))
	//	array_push($options, $option);
		
	//if(!array_key_exists($decision, $option_things))
	//{
	//	$option_things["$decision"] = array();
	//	array_push($option_things["$decision"], $option);
	//}
	//else
	//{
	//	array_push($option_things["$decision"], $option);
	//}
}

$dsName = "";
if($categoryId == 1)
	$dsName = "functional";
else if($categoryId == 2)
	$dsName = "nonfunctional";
else
	$dsName = "structural";


echo "var " . $dsName . "_json = " . $treeTop->printJSON();

/*$output .= "<br />"; //"\n\n\n";
foreach($things as $thing)
{
	$output .= "\n$thing"; // [arrowhead=none]";
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
	
	//$output .= " [arrowhead=none]";
}

$output .= "}"; 

echo $output;


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

function inArray($array, $thing)
{
	for($i = 0; $i < count($array); $i++)
	{
		if(strpos($array[$i], $thing) !== false)
		{
			return true;
		}
	}
	
	return false;
}*/

?>