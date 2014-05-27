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
	$decisionMatrix[$p->id] = array();

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
			if(!thingInArray2($node, $decisionMatrix[$p->id]))
				array_push($decisionMatrix[$p->id], new NP($node, $p));

			// Also do the parent
			while($node->type == "decision")
			{
				$node = $node->parent;

				if(!thingInArray2($node, $decisionMatrix[$p->id]))
					array_push($decisionMatrix[$p->id], new NP($node, $p));
			}
		}
		else
		{
			while($node->type != "decision" && $node->parent != null)
			{
				$node = $node->parent;
			}

			if(!thingInArray2($node, $decisionMatrix[$p->id]))
				array_push($decisionMatrix[$p->id], new NP($node, $p));
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
			if(!thingInArray2($node, $decisionMatrix[$p->id]))
				array_push($decisionMatrix[$p->id], new NP($node, $p));
		}
		else
		{
			while($node->type != "decision" && $node->parent != null)
			{
				$node = $node->parent;
			}

			if(!thingInArray2($node, $decisionMatrix[$p->id]))
				array_push($decisionMatrix[$p->id], new NP($node, $p));
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
			if(!thingInArray2($node, $decisionMatrix[$p->id]))
				array_push($decisionMatrix[$p->id], new NP($node, $p));
		}
		else
		{


			while($node->type != "decision" && $node->parent != null)
			{
				$node = $node->parent;
			}

			
			if(!thingInArray2($node, $decisionMatrix[$p->id]))
				array_push($decisionMatrix[$p->id], new NP($node, $p));
		}
	}
}

$decisionCounts = array();
foreach($decisionMatrix AS $decisions)
{
	foreach($decisions AS $d)
	{
		if(array_key_exists($d->n->name, $decisionCounts))
		{
			$decisionCounts[$d->n->name]++;
		}
		else
		{
			$decisionCounts[$d->n->name] = 1;
		}
	}
}

print_r($decisionCounts);

$output = "";
$k = array_keys($decisionCounts);
for($i = 0; $i < count($k); $i++)
{
	$output .= $k[$i] . "," . $decisionCounts[$k[$i]] . "\n";
}

$handle = fopen("decision_counts.csv", "w");
fwrite($handle, $output);
fclose($handle);

// And now do the decision counts per condition


$decisionCounts = array();
foreach($decisionMatrix AS $decisions)
{
	foreach($decisions AS $d)
	{
		if(array_key_exists($d->p->condition, $decisionCounts))
		{
			if(array_key_exists($d->n->name, $decisionCounts[$d->p->condition]))
			{
				$decisionCounts[$d->p->condition][$d->n->name]++;
			}
			else
			{
				$decisionCounts[$d->p->condition][$d->n->name] = 1;
			}
		}
		else
		{
			$decisionCounts[$d->p->condition] = array();

			// The thing inside can't exist if the outer one doesn't
			$decisionCounts[$d->p->condition][$d->n->name] = 1;
		}
	}
}

$output = "";
$k = array_keys($decisionCounts);
for($i = 0; $i < count($k); $i++)
{
	$k2 = array_keys($decisionCounts[$k[$i]]);
	for($j = 0; $j < count($k2); $j++)
	{
		$output .= "$k[$i],$k2[$j]," . $decisionCounts[$k[$i]][$k2[$j]] . "\n";
	}
}

$handle = fopen("decision_counts_condition.csv", "w");
fwrite($handle, $output);
fclose($handle);







////// HELPER STUFF



class NP
{
	public $n;
	public $p;

	function NP($n, $p)
	{
		$this->n = $n;
		$this->p = $p;
	}
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