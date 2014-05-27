<?php

require_once "database.php";

$OPTION = "option";
$DECISION = "decision";
$CATEGORY = "category";

$q = "SELECT decision_has_option.decision_id, decision.name AS decision_name, decision_has_option.option_id, `option`.name AS option_name FROM decision_has_option INNER JOIN `option` ON decision_has_option.option_id = `option`.id INNER JOIN decision ON decision_has_option.decision_id = decision.id";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail";
	return;
}

$stuff = array();
$options = array();


while($row = mysqli_fetch_array($retval))
{
	$option = new Thing($row["option_id"], $row["option_name"], $OPTION, $row["decision_name"]);
	array_push($stuff, &$option);
	array_push($options, &$option);
	
	$thing = getThingByName($stuff, $row["decision_name"]);
	
	if($thing == null)
	{
		$thing = new Thing($row["decision_id"], $row["decision_name"], $DECISION, null);
		array_push($stuff, $thing);
	}
}

// Now we should have added all the decisions that have options
// Do the decisions that have decisions
$q = "SELECT child_id, parent_id, decision.name AS parent_name FROM decision_has_decision INNER JOIN decision ON decision_has_decision.parent_id = decision.id";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail 2";
	return;
}

while($row = mysqli_fetch_array($retval))
{
	$q2 = "SELECT * FROM decision WHERE id = $row[child_id]";
	$retval2 = $database->query($q2);
	if(!$retval2)
	{
		echo "Fail 3";
		continue;
	}
	
	$row2 = mysqli_fetch_array($retval2);
	
	$childId = $row["child_id"];
	$childName = $row2["name"];
	$parentId = $row["parent_id"];
	$parentName = $row["parent_name"];
	
	$child = getThingByName($stuff, $childName);
	if($child === null)
	{
		$child = new Thing($childId, $childName, $DECISION, $parentName);
		array_push($stuff, $child);
	}
	else
	{
		$child->parent = $parentName;
	}
	
	$parent = getThingByName($stuff, $parentName);
	if($parent === null)
	{
		$parent = new Thing($parentId, $parentName, $DECISION, null);
		array_push($stuff, $parent);
	}
}

// Now we should have all the links between the decisions
// Do the categories

$q = "SELECT decision_id, category_id, category.name AS category_name, decision.name AS decision_name FROM decision_has_category INNER JOIN category ON decision_has_category.category_id = category.id INNER JOIN decision ON decision_has_category.decision_id = decision.id";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail 4";
	return;
}

while($row = mysqli_fetch_array($retval))
{
	$decisionId = $row["decision_id"];
	$decisionName = $row["decision_name"];
	$categoryId = $row["category_id"];
	$categoryName = $row["category_name"];
	
	$decision = getThingByName($stuff, $decisionName);
	if($decision === null)
	{
		$decision = new Thing($decisionId, $decisionName, $DECISION, $categoryName);
		array_push($stuff, $decision);
	}
	else
	{
		$decision->parent = $categoryName;
	}
	
	$category = getThingByName($stuff, $categoryName);
	if($category === null)
	{
		$category = new Thing($categoryId, $categoryName, $CATEGORY, null);
		array_push($stuff, $category);
	}
}

print_r($stuff);

/*$linesAbove = array();
$linesAbove[0] = array();
for($i = 0; $i < count($options); $i++)
{
	array_push($linesAbove[0], $options[$i]->parent);	
}

print_r($linesAbove);*/




function getThingByName($array, $word)
{
	foreach($array as $thing)
	{
		if(strcmp($thing->name, $word) === 0)
		{
			return $thing;
		}
	}
	
	return null;
}

function cmp($a, $b)
{
	if($a->option === $b->option)
		return 0;
		
	return ($a->option < $b->option) ? -1 : 1;
}

function getName($table, $id)
{
	global $database;
	$q = "SELECT name FROM `$table` WHERE id = \"$id\"";
	$retval = $database->query($q);
	
	if(!$retval){ echo "Fail"; return; }

	if(mysqli_num_rows($retval) === 0)
	{
		echo "No rows for $table, $id<br />";
	}
	
	$row = mysqli_fetch_array($retval);
	return $row["name"];
}

class Thing
{
	public $id;
	public $name;
	public $type;
	public $parent;
	public $children;
	public $depth;
	
	function Thing($id, $name, $type, $parent)
	{
		$this->id = $id;
		$this->name = $name;
		$this->type = $type;
		$this->parent = $parent;
		$this->children = array();
	}
	
	function addChild(&$thing)
	{
		array_push(&$this->children, &$thing);
	}
}

?>