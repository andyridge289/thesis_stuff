<?php

require_once "database.php";
require_once "thing.php";

$TOOL = "node [color=\"#FFFFFF\",fillcolor=\"#FFFFFF\",style=filled, fontcolor=\"#000000\" fontsize=\"22\"];";
$CATEGORY = "node [color=\"047b35\",fillcolor=\"#8df2b6\",style=filled fontsize=\"20\"];";

$DECISION = "node[shape=box,color=\"#004a63\",fillcolor=lightblue2,style=filled fontsize=\"15\"];";
$NOTDECISION = "node [color=\"#888888\", style=\"filled\", shape=rect, fontcolor=\"#888888\", fillcolor=\"#CCCCCC\"];";
$PLACEHOLDER = "node [color=\"#888888\", style=\"filled,dashed\", shape=rect, fontcolor=\"#888888\", fillcolor=\"#FFFFFF\"];";

$CHOSEN = "node [color=\"#222222\", style=\"rounded,filled\", shape=rect, fontcolor=\"#000000\", fillcolor=\"#CC99CC\"];";
$NOT_CHOSEN = "node [color=\"#AAAAAA\", style=\"rounded,filled\", shape=rect, fontcolor=\"#AAAAAA\", fillcolor=\"#EEEEEE\"];";

$categoryId = isset($_GET["c"]) ? $_GET["c"] : 1;
$toolId = isset($_GET["t"]) ? $_GET["t"] : 1;

// TODO MAke it split the tree up
$split = ($categoryId == 2 || $categoryId == 3) ? true : false;


$q = "SELECT * FROM category WHERE id = $categoryId";
$retval = $db->q($q);
if(!$retval) return;
$r = mysqli_fetch_array($retval);
$c = new Thing($r["id"], $r["name"], "category");

$q = "SELECT * FROM tool WHERE id = $toolId";
$retval = $db->q($q);
if(!$retval) return;
$r = mysqli_fetch_array($retval);
$toolName = $r["name"];

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
$c->lookup(false);

// Now we've worked everything out, list the arrays
$chosenOptions = array();
$otherOptions = array();
$madeDecisions = array();
$notmadeDecisions = array();

$c->arrayicise(false);

$first = new Thing($c->id, $c->name, $c->type);
$second = new Thing($c->id, $c->name, $c->type);

$links = array();
$firstLinks = array();
$secondLinks = array();
$firstPlaceholders = array();
$secondPlaceholders = array();

if(!$split)
{
	$c->makeLinks($links, null);
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


if(!$split)
{
	echo "NOT split";

	$output = "digraph output {";

	$output .= "$TOOL\n\"$toolName\"\n";

	$output .= "\n$CATEGORY\n";
	$output .= "\"$c->name\"";

	$output .= "\n\n$DECISION\n";
	for($i = 0; $i < count($madeDecisions); $i++)
	{
		if($i > 0) $output .= ",";

		$output.= "\"" . $madeDecisions[$i]->name . "\"";
	}


	$output .= "\n\n$NOTDECISION\n";
	for($i = 0; $i < count($notmadeDecisions); $i++)
	{
		if($i > 0) $output .= ",";

		$output.= "\"" . $notmadeDecisions[$i]->name . "\"";
	}

	$output .= "\n\n$CHOSEN\n";
	for($i = 0; $i < count($chosenOptions); $i++)
	{
		if($i > 0) $output .= ",";

		$output.= "\"" . $chosenOptions[$i]->name . "\"";
	}

	$output .= "\n\n$NOT_CHOSEN\n";
	for($i = 0; $i < count($otherOptions); $i++)
	{
		if($i > 0) $output .= ",";

		$output.= "\"" . $otherOptions[$i]->name . "\"";
	}

	$output .= "\n\n\n";
	foreach($links as $link)
	{
		$output .= "$link\n";
	}

	$output .= "}"; 
	$filename = "toolgv/" . $toolId . "_" . $categoryId . ".gv";
	$handle = fopen($filename, "w");
	fwrite($handle, $output);
	fclose($handle);
}
else
{
	echo "split";

	$output1 = "digraph output {\n\n";
	$output2 = "digraph output {\n\n";

	$output1 .= "$TOOL\n\"$toolName\"\n";
	$output2 .= "$TOOL\n\"$toolName\"\n";

	$output1 .= "\n$CATEGORY\n";
	$output2 .= "\n$CATEGORY\n";
	$output1 .= "\"$first->name\"";
	$output2 .= "\"$first->name\"";

	$output1 .= "\n\n$DECISION\n";
	$addedToFirst = false;

	$output2 .= "\n\n$DECISION\n";
	$addedToSecond = false;

	// print_r($firstPlaceholders);
	// echo "<br /><br /><br />";
	// print_r($secondPlaceholders);
	// echo "<br /><br /><br />";

	for($i = 0; $i < count($madeDecisions); $i++)
	{
		//echo "Testing $madeDecisions[$i] " . (in_array($madeDecisions[$i], $firstPlaceholders) ? "yes" : "no") . "<br />";

		if(in_array($madeDecisions[$i], $firstPlaceholders))
		{
			// echo "$madeDecisions[$i] is placeholder<br />";
		}
		else if($first->contains($madeDecisions[$i]))
		{
			if($addedToFirst)
				$output1 .= ",";

			$output1 .= "\"$madeDecisions[$i]\"";

			$addedToFirst = true;
		} 

		if(in_array($madeDecisions[$i], $secondPlaceholders))
		{
			// echo "$madeDecisions[$i] is placeholder<br />";
		}
		else if($second->contains($madeDecisions[$i]))
		{
			if($addedToSecond)
				$output2 .= ",";

			$output2 .= "\"$madeDecisions[$i]\"";
			$addedToSecond = true;
		}
		
	}

	$output1 .= "\n\n$NOTDECISION\n";
	$output2 .= "\n\n$NOTDECISION\n";

	$addedToFirst = false;
	$addedToSecond = false;

	for($i = 0; $i < count($notmadeDecisions); $i++)
	{
		if(in_array($notmadeDecisions[$i], $firstPlaceholders))
		{

		}
		else if($first->contains($notmadeDecisions[$i]))
		{
			if($addedToFirst)
				$output1 .= ",";

			$output1 .= "\"$notmadeDecisions[$i]\"";

			$addedToFirst = true;
		}

		if(in_array($notmadeDecisions[$i], $secondPlaceholders))
		{

		}
		else if($second->contains($notmadeDecisions[$i]))
		{
			if($addedToSecond)
				$output2 .= ",";

			$output2 .= "\"$notmadeDecisions[$i]\"";

			$addedToSecond = true;
		}
	}

	$output1 .= "\n\n$PLACEHOLDER\n";

	for($i = 0; $i < count($firstPlaceholders); $i++)
	{
		if($i > 0) $output1 .= ",";

		$output1 .= "\"" . $firstPlaceholders[$i]->name. "\"";
	}

	$output2 .= "\n\n$PLACEHOLDER\n";

	for($i = 0; $i < count($secondPlaceholders); $i++)
	{
		if($i > 0) $output2 .= ",";

		$output2 .= "\"" . $secondPlaceholders[$i]->name . "\"";
	}

	$output1 .= "\n\n$CHOSEN\n";
	$output2 .= "\n\n$CHOSEN\n";

	$addedToFirst = false;
	$addedToSecond = false;

	for($i = 0; $i < count($chosenOptions); $i++)
	{
		if($first->contains($chosenOptions[$i]))
		{
			if($addedToFirst) 
				$output1 .= ",";

			$output1 .= "\"" . $chosenOptions[$i]->name . "\"";

			$addedToFirst = true;
		}

		if($second->contains($chosenOptions[$i]))
		{
			if($addedToSecond) 
				$output2 .= ",";

			$output2 .= "\"" . $chosenOptions[$i]->name . "\"";

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
	}

	$output1 .= "\n\n\n";
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

 	$filename1 = "toolgv/" . $toolId . "_" . $categoryId . "_1.gv";
 	$filename2 = "toolgv/" . $toolId . "_" . $categoryId . "_2.gv";
 
	$handle1 = fopen($filename1, "w");
	$handle2 = fopen($filename2, "w");

	fwrite($handle1, $output1);
	fwrite($handle2, $output2);

	fclose($handle1);
	fclose($handle2);
}

?>