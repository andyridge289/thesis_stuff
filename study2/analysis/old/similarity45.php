<?php

require_once "../lib/database.php";
require_once "../lib/build_participants.php";
require_once "../lib/build_new_tree.php";

$ps = $participants;
$f = array();
foreach($ps AS $p)
{
	if($p->condition > 3)
	{
		$p->thingNames = array();
		foreach($p->things AS $thing)
		{
			array_push($p->thingNames, $thing->name);
		}
		array_push($f, $p);
	}
}

$output = "";

usort($f, "cmp");
function cmp($a, $b)
{
	return $a->id > $b->id;
}

for($i = 0; $i < count($f); $i++)
{
	$p = $f[$i];
	$output .= "$p->id";

	for($j = 0; $j < count($f); $j++)
	{
		$output .= ",";

		if($i == $j)
			continue;

		$output .= jaccard($f[$i]->thingNames, $f[$j]->thingNames);
	}

	$output .= "\n";
}


// $nnvs = array();
// for($i = 0; $i < count($foursAndFives); $i++)
// {
// 	for($j = $i + 1; $j < count($foursAndFives); $j++)
// 	{
// 		array_push($nnvs, new NNV($foursAndFives[$i]->id, $foursAndFives[$j]->id, ));
// 	}
// }



// $m = array();

// // TODO Chuck it in a matrix

// for($i = 0; $i < count($nnvs); $i++)
// {
// 	$nv = $nnvs[$i];
// 	if(!array_key_exists($nv->x, $m))
// 	{
// 		$m[$nv->x] = array();
// 	}
// 	else
// 	{
// 		$m[$nv->x][$nv->y] = $nv->v;
// 	}
// }

// $k = array_keys($m);


// function cmp2($a, $b)
// {
// 	return $a > $b;
// }
// usort($k, "cmp2");

// for($i = 0; $i < count($k); $i++)
// {
// 	$output .= "," . $k[$i];
// }
// $output .= "\n";

// for($i = 0; $i < count($k); $i++)
// {
// 	$output .= $k[$i];
// 	$row = $m[$k[$i]];
// 	for($j = 0; $j < count($k); $j++)
// 	{
// 		$output .= ",";

// 		if(!array_key_exists($k[$j], $row))
// 			continue;

// 		// print_r($row);
// 		// break;
// 		$output .= $row[$k[$j]];
// 	}

// 	$output .= "\n";
// }

$handle = fopen("similarity.csv", "w");
fwrite($handle, $output);
fclose($handle);


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

// class NNV
// {
// 	public $x;
// 	public $y;
// 	public $v;

// 	function NNV($x, $y, $v)
// 	{
// 		$this->x = $x;
// 		$this->y = $y;
// 		$this->v = $v;
// 	}
// }

?>