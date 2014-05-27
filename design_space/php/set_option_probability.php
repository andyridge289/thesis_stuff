<?php

require_once "database.php";

$q = "TRUNCATE TABLE tool_has_p";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$q = "SELECT * FROM tool_has_option";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$combos = array();
while($row = mysqli_fetch_array($retval))
{
	$c = $row["category_id"];
	$d = $row["decision_id"];
	$o = $row["option_id"];
	$t = $row["tool_id"];
	
	$thing = findThing($combos, $c, $d, $o);
	
	if($thing === null)
	{
		$thing = new Thing($c, $d, $o, $t);
		array_push($combos, $thing);
	}
	else
	{
		$thing->addT($t);
	}
}

$q = "SELECT * FROM `tool`";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$toolCount = mysqli_num_rows($retval);

foreach($combos AS $option)
{
	$option->setP($toolCount);
	$q = $option->createQuery();
	$retval = $database->query($q);
	if(!$retval)
	{
		echo "Fail: $q<br />";
		continue;
	}
}

function findThing(&$array, &$c, &$d, &$o)
{
	foreach($array AS $thing)
	{
		if($thing->c === $c && $thing->d === $d && $thing->o === $o)
		{
			return $thing;
		}
	}
	
	return null;
}

class Thing
{
	public $c;
	public $d;
	public $o;
	public $ts;
	public $p;
	
	function Thing($c, $d, $o, $t)
	{
		$this->c = $c;
		$this->d = $d;
		$this->o = $o;
		$this->ts = array();
		array_push($this->ts, $t);
	}
	
	function addT($t)
	{
		array_push($this->ts, $t);
	}
	
	function toString()
	{
		return "$this->c $this->d $this->o t[" . count($this->ts) . "] ===> $this->p<br />";
	}
	
	function setP($toolCount)
	{
		$this->p = count($this->ts) / $toolCount;
	}
	
	function createQuery()
	{
		return "INSERT INTO `tool_has_p` VALUES('', $this->c, $this->d, $this->o," . count($this->ts) . ", $this->p)";
	}
}

// Get all of the options
/*$q = "SELECT * FROM `option`";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$options = array();
while($row = mysqli_fetch_array($retval))
{
	array_push($options, new Thing($row["id"], $row["name"]));
}


$q = "SELECT COUNT(*) AS total FROM tool";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$row = mysqli_fetch_array($retval);
$toolCount = $row["total"];

// We need the thing to appear in more than half of the tools
$threshold = $toolCount / 2;

// For each option see what the probability of that one happening is and save it somewhere
for($i = 0; $i < count($options); $i++)
{
	$options[$i]->probability = pOfThing($options[$i]);
}

foreach($options AS $combo)
{
	echo $combo->toString();
}

function pOfThing(&$thing)
{
	global $database;
	global $toolCount;
	
	$q  = "SELECT tool_has_option.id, tool_has_option.decision_id, category_id FROM `tool_has_option` INNER JOIN decision ON decision.id = tool_has_option.decision_id WHERE option_id = $thing->id";
	$retval = $database->query($q);
	if(!$retval)
	{
		echo "Fail: $q<br />";
		return -1;
	}
	
	$count = mysqli_num_rows($retval);
	$thing->count = $count;
	
	return $count / $toolCount;
}

class Thing
{
	public $id;
	public $name;
	public $count;
	public $probability;

	function Thing($id, $name)
	{
		$this->id = $id;
		$this->name = $name;
	}
	
	function toString()
	{
		return "$this->id: $this->name -- $this->probability<br />";
	}
}



/*function pOfThingAndThing(&$firstThing, &$secondThing, &$comboList)
{
	global $database;
	global $toolCount;
	global $threshold;
	
	$q = "SELECT tool_id FROM `tool_has_option` WHERE option_id = $firstThing->id";
	$retval = $database->query($q);
	if(!$retval)
	{
	 	echo "First fail: $q<br />";
	 	return -1;
	}
	
	$count = 0;
	
	if(mysqli_num_rows($retval) < $threshold)
	{
		// If there aren't enough initial instances then carry on
		return;
	}
	
	while($row = mysqli_fetch_array($retval))
	{
		$toolId = $row["tool_id"];
		$q2 = "SELECT * FROM `tool_has_option` WHERE option_id = $secondThing->id AND tool_id = $toolId";
		$retval2 = $database->query($q2);
		if(!$retval2)
		{
			echo "Second fail: $q<br />";
			continue;
		}
		
		$count += mysqli_num_rows($retval2) != 0 ? 1 : 0;
	}
	
	$p = $count / $toolCount;
	
	if($p === 0)
		return;
	
	$bayes = 0;
	
	if($firstThing->probability !== 0)
		$bayes = $p / $firstThing->probability;
		
	//echo "$firstThing->name ==> $secondThing->name: $bayes<br />";
	
	$combo = new Combo($firstThing, $secondThing, $p, $bayes);
	$combo->bothCount = $count;
	//echo $combo->toString();
	
	array_push($comboList, $combo);
}




class Combo
{
	public $firstThing;
	public $firstThingCount;
	
	public $secondThing;
	public $secondThingCount;
	
	public $bothCount;
	
	public $p;
	public $bayes;
	
	function Combo($firstThing, $secondThing, $p, $bayes)
	{
		$this->firstThing = $firstThing;
		$this->secondThing = $secondThing;
		$this->p = $p;
		$this->bayes = $bayes;
	}
	
	function toString()
	{
		$output = "[" . $this->bothCount . "] " . $this->firstThing->name . " (" . $this->firstThing->count . ")";
		$output .= " ==> " . $this->secondThing->name . " (" . $this->secondThing->count . ")";
		$output .=  " ======> " . $this->bayes ;
	
		return "$output<br />";
	}
	
}

function cmp($a, $b)
{
	if($a->bothCount === $b->bothCount)
		return 0;
	else
		return $a->bothCount < $b->bothCount ? 1 : -1;
}*/



/*$CATEGORY = "node [color=\"047b35\",fillcolor=\"#8df2b6\",style=filled, shape=house];";
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

	if(!inArray($categories, $category))
		array_push($categories, $category);
		
	if(!inArray($decisions, $decision))
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
	
	if(!inArray($decisions, $parent))
		array_push($decisions, $parent);

	if(!inArray($decisions, $child))
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
	
	if(!inArray($decisions, $decision))
		array_push($decisions, $decision);

	if(!inArray($options, $option))
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
}*/

?>