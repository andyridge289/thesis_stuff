<?php

require_once "../lib/database.php";
require_once "../lib/build_participants.php";

// First get the list of classifiers
$classes = array();
$q = "SELECT * FROM `sub_classification` ORDER BY ds";
$ret = $db->q($q);
if(!$ret) {
	echo "Fai: $q";
	return;
}

while($r = mysqli_fetch_array($ret)) {
	array_push($classes, new Trio($r["id"], $r["name"], $r["ds"]));
}

$p_counts = array();

// Link to the classified things for each participant
for($i = 0; $i < count($participants); $i++) {

	$p = $participants[$i];
	$p_counts[$p->id] = array();

	foreach($p->customs AS $custom) {
		
		$q = "SELECT classifier FROM `classified_custom` WHERE custom_id = $custom->id";
		$ret = $db->q($q);
		if(!$ret) {
			echo "Fail $q<br />";
			continue;
		}
		$r = mysqli_fetch_array($ret);

		if(!array_key_exists($r["classifier"], $p_counts[$p->id])) {
			$p_counts[$p->id][$r["classifier"]] = 1;
		} else {
			$p_counts[$p->id][$r["classifier"]]++;
		}
	}

	foreach($p->split AS $split) {
		$q = "SELECT classifier FROM `classified_split` WHERE split_id = $split->id";
		$ret = $db->q($q);
		if(!$ret) {
			echo "Fail $q<br />";
			continue;
		}
		$r = mysqli_fetch_array($ret);

		if(!array_key_exists($r["classifier"], $p_counts[$p->id])) {
			$p_counts[$p->id][$r["classifier"]] = 1;
		} else {
			$p_counts[$p->id][$r["classifier"]]++;
		}	
	}

	foreach($p->things AS $thing) {
		$q = "SELECT classifier FROM `classified_thing` WHERE thing_id = $thing->id";
		$ret = $db->q($q);
		if(!$ret) {
			echo "Fail $q<br />";
			continue;
		}
		$r = mysqli_fetch_array($ret);

		if(!array_key_exists($r["classifier"], $p_counts[$p->id])) {
			$p_counts[$p->id][$r["classifier"]] = 1;
		} else {
			$p_counts[$p->id][$r["classifier"]]++;
		}
	}
}

$output = "participant,";
for($j = 0; $j < count($classes); $j++) {
	if($j > 0)
		$output .= ",";

	$output .= $classes[$j]->name;
}
$output .= "\n";

for($i = 0; $i < count($participants); $i++) {

	$p = $participants[$i];
	$pcount = $p_counts[$p->id];
	$output .= $p->id . ",";

	for($j = 0; $j < count($classes); $j++) {

		if($j > 0) $output .= ",";

		if(array_key_exists($classes[$j]->id, $pcount)) {
			$output .= $pcount[$classes[$j]->id];
		} else {
			$output .= "0";
		}

	}
	$output .= "\n";
}

$handle = fopen("completion_output.csv", "w");
fwrite($handle, $output);
fclose($handle);

class Trio {

	public $name;
	public $id;
	public $ds;

	function Trio($id, $name, $ds) {
		$this->id = $id;
		$this->name = $name;
		$this->ds = $ds;
	}
}

// echo "<pre>";
// print_r($participants[0]);
// echo "</pre>";

// return;

// FIRST DO CUSTOMS - conditions 1, 2, 4
// $conditions = array();
// for($i = 0; $i < count($participants); $i++) {

// 	if(!array_key_exists($participants[$i]->condition, $conditions)) {
// 		$conditions[$participants[$i]->condition] = array();
// 	}

// 	array_push($conditions[$participants[$i]->condition], $participants[$i]);
// }

// $names = array("custom", "things", "all", "new");

// foreach($names AS $name) {

// 	$indexes = array(1, 2, 4);
// 	$colVals = array(1, 2, 3);
// 	$out = get($conditions, $indexes, $colVals, $name);
// 	$handle = fopen($name . "_124.csv", "w");
// 	fwrite($handle, $out);
// 	fclose($handle);

// 	$indexes = array(1, 3, 5);
// 	$out = get($conditions, $indexes, $colVals, $name);
// 	$handle = fopen($name . "_135.csv", "w");
// 	fwrite($handle, $out);
// 	fclose($handle);

// 	$indexes = array(1, 2, 3, 4, 5);
// 	$out = get($conditions, $indexes, $indexes, $name);
// 	$handle = fopen($name . "_12345.csv", "w");
// 	fwrite($handle, $out);
// 	fclose($handle);

// }

// function get($conditions, $indexes, $colVals, $type) {

// 	$out = "";
// 	for($i = 0; $i < count($indexes); $i++) {
// 		$index = $indexes[$i];
// 		$colVal = $colVals[$i];

// 		if($type == "custom") {
// 			$one = getNumCustoms($conditions[$index]);
// 		} else if ($type == "things") {
// 			$one = getChosenThings($conditions[$index]);
// 		} else if($type == "all") {
// 			$one = getAllThings($conditions[$index]);
// 		} else { // It's new
// 			$one = getNew($conditions[$index]);
// 		}

// 		$out .= makeString($colVal, $one);	
// 	}
	
// 	return $out;
// }

// function getNumCustoms($array) {
// 	$customs = array();
// 	foreach($array as $p) {
// 		array_push($customs, count($p->customs));
// 	}
// 	return $customs;
// }

// function getChosenThings($array) {
// 	$options = array();
// 	foreach($array AS $p) {
// 		array_push($options, count($p->things));
// 	}
// 	return $options;
// }

// function getAllThings($array) {
// 	$all = array();
// 	foreach($array AS $p) {
// 		array_push($all, count($p->things) + count($p->customsAreThings));
// 	}
// 	return $all;
// }

// function getNew($array) {
// 	$new = array();
// 	foreach($array AS $p) {
// 		array_push($new, count($p->new));
// 	}
// 	return $new;
// }

// function makeString($c, $array) {
// 	$output = "";
// 	foreach($array as $n) {
// 		$output .= "$c,$n\n";
// 	}
// 	return $output;
// }

// Conditions 1, 3, 5

// Conditions All



// $output = "id,condition,customs,things,total\n";

// for($i = 0; $i < count($participants); $i++)
// {
// 	$p = $participants[$i];
// 	$output .= "$p->id,$p->condition," . count($p->customs) . "," . count($p->things) . "," . (count($p->things) + count($p->customs)) . "\n";
// }

// echo nl2br($output);

// $handle = fopen("num_things.csv", "w");
// fwrite($handle, $output);
// fclose($handle);

?>