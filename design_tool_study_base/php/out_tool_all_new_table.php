<?php

require_once "database.php";
require_once "thing.php";

$categoryId = isset($_GET["c"]) ? $_GET["c"] : 1;

$q = "SELECT * FROM category WHERE id = $categoryId";
$retval = $db->q($q);
if(!$retval) return;
$r = mysqli_fetch_array($retval);

$q = "SELECT * 
FROM decision_has_option
		LEFT JOIN `option` ON decision_has_option.option_id = `option`.id
		WHERE decision_has_option.category_id = $categoryId
		ORDER BY decision_has_option.decision_id";

$retval = $db->q($q);
if(!$retval) return;

$everything = array();

while($r = mysqli_fetch_array($retval))
{
	$option_id = $r["name"];
	$everything[$option_id] = array();
}

$q = "SELECT * FROM tool";
$retval = $db->q($q);
if(!$retval) return;
$tools = array();
while($r = mysqli_fetch_array($retval))
{
	array_push($tools, $r["name"]);
}

foreach($everything AS &$thing)
{
	foreach($tools AS $tool)
	{
		$thing[$tool] = "";
	}
}

// Above this is just initial setup

$q = "SELECT 
	`option`.name AS option_name, tool.name AS tool_name
	FROM `tool_has_option`
	LEFT JOIN tool ON tool.id = tool_has_option.tool_id
	LEFT JOIN `option` ON `option`.id = tool_has_option.option_id
	LEFT JOIN decision_has_option ON decision_has_option.option_id = tool_has_option.option_id
	WHERE decision_has_option.category_id = $categoryId";

$retval = $db->q($q);
if(!$retval) return;

while($r = mysqli_fetch_array($retval))
{
	// echo "$r[option_name] $r[tool_name]";
	$option = $r["option_name"];
	$tool = $r["tool_name"];

	// print_r($everything[$option][$tool]);
	// break;

	$everything[$option][$tool] = 1;
	// break;
}

// return;
















// Below this is just outputting

echo "<table>";

$keys = array_keys($everything);

echo "<tr><td>Tool</td>";
for($i = 0; $i < count($keys); $i++)
{
	echo "<td>$keys[$i]</td>";
}
echo "</tr>";

for($i = 0; $i < count($tools); $i++)
{
	$tool = $tools[$i];
	echo "<tr><td>$tool</td>";


	for($j = 0; $j < count($keys); $j++)
	{
		echo "<td>" . $everything[$keys[$j]][$tool] . "</td>";
	}

	echo "</tr>";
}

echo "</table>";










/*$TOOL = "node [color=\"#FFFFFF\",fillcolor=\"#FFFFFF\",style=filled, fontcolor=\"#000000\" fontsize=\"22\"];";
$CATEGORY = "node [color=\"047b35\",fillcolor=\"#8df2b6\",style=filled fontsize=\"20\"];";

$DECISION = "node[shape=box,color=\"#004a63\",fillcolor=lightblue2,style=filled fontsize=\"15\"];";
$NOTDECISION = "node [color=\"#888888\", style=\"filled\", shape=rect, fontcolor=\"#888888\", fillcolor=\"#CCCCCC\"];";
$PLACEHOLDER = "node [color=\"#888888\", style=\"filled,dashed\", shape=rect, fontcolor=\"#888888\", fillcolor=\"#FFFFFF\"];";

$CHOSEN = "node [color=\"#222222\", style=\"rounded,filled\", shape=rect, fontcolor=\"#000000\", fillcolor=\"#CC99CC\"];";
$NOT_CHOSEN = "node [color=\"#AAAAAA\", style=\"rounded,filled\", shape=rect, fontcolor=\"#AAAAAA\", fillcolor=\"#EEEEEE\"];";





$q = "SELECT * FROM tool";
$retval = $db->q($q);
if(!$retval) return;
$toolCount = mysqli_num_rows($retval);

$q = "SELECT  
		*
		FROM `decision_has_category` 
		LEFT JOIN decision ON decision.id = decision_has_category.decision_id 
		WHERE decision_has_category.category_id = $categoryId";
$retval = $db->q($q);
if(!$retval) return;

while($r = mysqli_fetch_array($retval))
{
	$d = new Thing($r["id"], $r["name"], "decision");
	$c->addChild($d);
}

// At this point we've got all of the decisions that come off the category

$q = "SELECT 
		* 
		FROM decision_has_decision
		LEFT JOIN decision ON decision.id = decision_has_decision.child_id
		WHERE decision_has_decision.category_id = $categoryId";
$retval = $db->query($q);
if(!$retval) return;

while($r = mysqli_fetch_array($retval))
{
	$d = new Thing($r["child_id"], $r["name"], "decision");
	$value = $c->addToTree($r["parent_id"], "decision", $d);
}

$q = "SELECT
		*
		FROM decision_has_option
		LEFT JOIN `option` ON option.id = decision_has_option.option_id
		WHERE decision_has_option.category_id = $categoryId";
$retval = $db->query($q);
if(!$retval) return;

while($r = mysqli_fetch_array($retval))
{
	if($r["name"] == "")
		continue;


	$o = new Thing($r["option_id"], $r["name"], "option");
	$value = $c->addToTree($r["decision_id"], "decision", $o);
}

// At this point we've built the tree for the DS, now to populate the tool things
$c->lookup(true);

// Now we've worked everything out, list the arrays
$chosenOptions = array();
$otherOptions = array();
$madeDecisions = array();
$notmadeDecisions = array();

$first = new Thing($c->id, $c->name, $c->type);
$second = new Thing($c->id, $c->name, $c->type);

$c->arrayicise(true);

$links = array();
$firstLinks = array();
$secondLinks = array();
$firstPlaceholders = array();
$secondPlaceholders = array();

if(!$split)
{
	$c->makeLinks($links, $firstPlaceholders);
}
else
{
	usort($c->children, "cmp");

	for($i = 0; $i < count($c->children); $i++)
	{
		$child = $c->children[$i];

		if($first->subtreeSize() <= $second->subtreeSize())
		{
			array_push($first->children, $child);

			// And put a placeholder in the other one
			$placeholder = new Thing($child->id, $child->name, $child->type);
			$placeholder->placeholder = true;

			array_push($second->children, $placeholder);
			array_push($secondPlaceholders, $placeholder);
		}
		else
		{
			array_push($second->children, $c->children[$i]);

			// And put a placeholder in the other one
			$placeholder = new Thing($child->id, $child->name, $child->type);
			$placeholder->placeholder = true;

			array_push($first->children, $placeholder);
			array_push($firstPlaceholders, $placeholder);
		}
	}


	$first->makeLinks($firstLinks, $firstPlaceholders);
	echo "<br /><br /><br />";
	$second->makeLinks($secondLinks, $secondPlaceholders);
}

$borderValueFull = 0.1;
$fontValueFull = 0;
$fillValueFull = 0.5;
$borderValueNone = 0.67;
$fontValueNone = 0.4;
$fillValueNone = 1;

if($split)
{
	echo "split";

	$output1 = "digraph output {\n\n";
	$output2 = "digraph output {\n\n";

	$output1 .= "\n$CATEGORY\n";
	$output2 .= "\n$CATEGORY\n";
	$output1 .= "\"$first->name\"";
	$output2 .= "\"$first->name\"";

	$output1 .= "\n\n$DECISION\n";
	$addedToFirst = false;

	$output2 .= "\n\n$DECISION\n";
	$addedToSecond = false;

	for($i = 0; $i < count($madeDecisions); $i++)
	{
		echo "Testing " . $madeDecisions[$i]->name . " " . (in_array($madeDecisions[$i], $firstPlaceholders) ? "yes" : "no") . "<br />";

		if(inThingArray($madeDecisions[$i], $firstPlaceholders))
		{

		}
		else if($first->containsThing($madeDecisions[$i]))
		{
			if($addedToFirst)
				$output1 .= ",";

			$output1 .= "\"" . $madeDecisions[$i]->name . "\"";

			$addedToFirst = true;
		} 

		if(inThingArray($madeDecisions[$i], $secondPlaceholders))
		{
			// echo "$madeDecisions[$i] is placeholder<br />";
		}
		else if($second->containsThing($madeDecisions[$i]))
		{
			if($addedToSecond)
				$output2 .= ",";

			$output2 .= "\"" . $madeDecisions[$i]->name . "\"";
			$addedToSecond = true;
		}
		
	}

	echo "Errrrr";

	$output1 .= "\n\n$PLACEHOLDER\n";

	for($i = 0; $i < count($firstPlaceholders); $i++)
	{
		if($i > 0) $output1 .= ",";

		$output1 .= "\"" . $firstPlaceholders[$i]->name . "\"";
	}

	$output2 .= "\n\n$PLACEHOLDER\n";

	for($i = 0; $i < count($secondPlaceholders); $i++)
	{
		if($i > 0) $output2 .= ",";

		$output2 .= "\"" . $secondPlaceholders[$i]->name . "\"";
	}

	for($i = 0; $i < count($chosenOptions); $i++)
	{
		$percentage = $chosenOptions[$i]->chosen;

		$borderColour = hslToRgb(0, 0, getV($borderValueFull, $borderValueNone, $percentage));
		$fontColour = hslToRgb(0, 0, getV($fontValueFull, $fontValueNone, $percentage));
		$fillColour = hslToRgb(0.83, 0.25, (1 - $percentage) / 2 + 0.5);
			

		if($first->containsThing($chosenOptions[$i]))
		{
			$output1 .= "node [color=\"#" . $borderColour . "\", style=\"rounded,filled\", shape=rect, fontcolor=\"#" . $fontColour . "\", fillcolor=\"#" . $fillColour . "\"]";
			$output1 .= "\"" . $chosenOptions[$i]->name . "\"\n";
		}
		
		if($second->containsThing($chosenOptions[$i]))
		{
			$output2 .= "node [color=\"#" . $borderColour . "\", style=\"rounded,filled\", shape=rect, fontcolor=\"#" . $fontColour . "\", fillcolor=\"#" . $fillColour . "\"]";
			$output2 .= "\"" . $chosenOptions[$i]->name . "\"\n";
		}
	}

	/*$output1 .= "\n\n$CHOSEN\n";
	$output2 .= "\n\n$CHOSEN\n";

	$addedToFirst = false;
	$addedToSecond = false;

	for($i = 0; $i < count($chosenOptions); $i++)
	{
		if($first->contains($chosenOptions[$i]))
		{
			if($addedToFirst) 
				$output1 .= ",";

			$output1 .= "\"$chosenOptions[$i]\"";

			$addedToFirst = true;
		}

		if($second->contains($chosenOptions[$i]))
		{
			if($addedToSecond) 
				$output2 .= ",";

			$output2 .= "\"$chosenOptions[$i]\"";

			$addedToSecond = true;
		}
	}

	$output1 .= "\n\n$NOT_CHOSEN\n";
	$output2 .= "\n\n$NOT_CHOSEN\n";

	$addedToFirst = false;
	$addedToSecond = false;

	for($i = 0; $i < count($otherOptions); $i++)
	{
		if($first->contains($otherOptions[$i]))
		{
			if($addedToFirst) 
				$output1 .= ",";

			$output1 .= "\"$otherOptions[$i]\"";	

			$addedToFirst = true;
		}

	
		if($second->contains($otherOptions[$i]))
		{
			if($addedToSecond) 
				$output2 .= ",";

			$output2 .= "\"$otherOptions[$i]\"";	

			$addedToSecond = true;
		}		
	}*/

	/*$output1 .= "\n\n\n";
	$output2 .= "\n\n\n";

	foreach($firstLinks as $link)
	{
		$output1 .= "$link\n";
	}

	foreach($secondLinks as $link)
	{
		$output2 .= "$link\n";
	}

	$output1 .= "}"; 
	$output2 .= "}";

 	$filename1 = "toolgv/all" . $categoryId . "_1.gv";
 	$filename2 = "toolgv/all" . $categoryId . "_2.gv";
 
	$handle1 = fopen($filename1, "w");
	$handle2 = fopen($filename2, "w");

	fwrite($handle1, $output1);
	fwrite($handle2, $output2);

	fclose($handle1);
	fclose($handle2);
}
else
{
	echo "not split<br />";

	$output = "digraph output {";

	$output .= "\n$CATEGORY\n";
	$output .= "\"$c->name\"";

	$output .= "\n\n$DECISION\n";
	for($i = 0; $i < count($madeDecisions); $i++)
	{
		if($i > 0) $output .= ",";

		$output.= "\"" . $madeDecisions[$i]->name . "\"";
	}

	$output .= "\n\n$NOT_CHOSEN\n";

	// 300, 25, 80

	for($i = 0; $i < count($chosenOptions); $i++)
	{
		$percentage = $chosenOptions[$i]->chosen;

		$borderColour = hslToRgb(0, 0, getV($borderValueFull, $borderValueNone, $percentage));
		$fontColour = hslToRgb(0, 0, getV($fontValueFull, $fontValueNone, $percentage));
		$fillColour = hslToRgb(0.83, 0.25, (1 - $percentage) / 2 + 0.5);
			
		$output .= "node [color=\"#" . $borderColour . "\", style=\"rounded,filled\", shape=rect, fontcolor=\"#" . $fontColour . "\", fillcolor=\"#" . $fillColour . "\"]";
		$output.= "\"" . $chosenOptions[$i]->name . "\"\n";
	}

	$output .= "\n\n\n";
	foreach($links as $link)
	{
		$output .= "$link\n";
	}

	$output .= "}"; 
	$filename = "toolgv/all" . $categoryId . ".gv";
	$handle = fopen($filename, "w");
	fwrite($handle, $output);
	fclose($handle);
}*/

?>