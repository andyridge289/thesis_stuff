<?php

require_once "../lib/database.php";
require_once "../lib/build_participants.php";
require_once "../lib/build_new_tree.php";

$ps = $participants;
usort($ps, "cmp");

function cmp($a, $b)
{
	return $a->id > $b->id;
}

$decisionMatrix = array();
foreach($ps as $p)
{
	$pn = new PN($p);

	// Firstly, get the customs

	foreach($p->customsAreThings AS $thing)
	{
		$node = findInTree($newRoot, $thing->name);
		
		if($node->type === "category")
		{
			// We can't really do anything, I guess just ignore these because they will be classified as new
		}
		else if($node->type === "decision")
		{
			// We're good, add this one.
			if(!thingInArray($node, $pn->n))
				array_push($pn->n, $node);

			// Also do the parent
			while($node->type == "decision")
			{
				$node = $node->parent;

				if(!thingInArray($node, $pn->n))
					array_push($pn->n, $node);
			}
		}
		else
		{
			while($node->type != "decision" && $node->parent != null)
			{
				$node = $node->parent;
			}

			if(!thingInArray($node, $pn->n))
				array_push($pn->n, $node);
		}
	}

	// Secondly, get the split customs
	foreach ($p->splitAreThings as $thing) 
	{
		$node = findInTree($newRoot, $thing->name);

		if($node->type === "category")
		{
			// We can't really do anything, I guess just ignore these because they will be classified as new
		}
		else if($node->type === "decision")
		{
			// We're good
			if(!thingInArray($node, $pn->n))
				array_push($pn->n, $node);
		}
		else
		{
			while($node->type != "decision" && $node->parent != null)
			{
				$node = $node->parent;
			}

			if(!thingInArray($node, $pn->n))
				array_push($pn->n, $node);
		}
	}

	// Secondly, get the split customs
	foreach ($p->things as $thing) 
	{
		$node = findInTree($newRoot, $thing->name);

		if($node == null)
		{
			// FIXME Work these out...
			// echo "Couldn't find $thing->name<br />";
			continue;
		}

		if($node->type === "category")
		{
			// We can't really do anything, I guess just ignore these because they will be classified as new
		}
		else if($node->type === "decision")
		{
			// We're good
			if(!thingInArray($node, $pn->n))
				array_push($pn->n, $node);
		}
		else
		{


			while($node->type != "decision" && $node->parent != null)
			{
				$node = $node->parent;
			}
			
			if(!thingInArray($node, $pn->n))
				array_push($pn->n, $node);
		}
	}

	array_push($decisionMatrix, $pn);
}

$output = "";

for($i = 0; $i < count($decisionMatrix); $i++)
{

	$pni = $decisionMatrix[$i];
	$output .= $pni->p->id . "," . $pni->p->condition;
	$iNames = array();
	for($j = 0; $j < count($pni->n); $j++)
	{
		array_push($iNames, $pni->n[$j]->name);
	}

	for($j = 0; $j < count($decisionMatrix); $j++)
	{
		$output .= ",";

		if($j == $i)
			continue;

		$pnj = $decisionMatrix[$j];
		$jNames = array();
		for($k = 0; $k < count($pnj->n); $k++)
		{
			array_push($jNames, $pnj->n[$k]->name);
		}

		
		$output .= jaccard($iNames, $jNames);

	}

	$output .= "\n";
}


$handle = fopen("decision_jaccard.csv", "w");
fwrite($handle, $output);
fclose($handle);








////// HELPER STUFF



class PN
{
	public $n;
	public $p;

	function PN($p)
	{
		$this->p = $p;
		$this->n = array();
	}
}

function jaccard($a, $b)
{
	$top = array_intersect($a, $b);

	$bottom = array_merge($a, $b);
	for($i = 0; $i < count($bottom); $i++)
	{
		for($j = $i + 1; $j < count($bottom); $j++)
		{
			if($i == $j)
			{
				array_splice($bottom, $j, 1);
				break;
			}
		}
	}

	return count($top) / count($bottom);
}


function thingInArray2($thing, $array)
{
	for($i = 0; $i < count($array); $i++)
	{
		if($array[$i]->n->id == $thing->id)
		{
			return true;
		}
	}

	return false;
}

?>