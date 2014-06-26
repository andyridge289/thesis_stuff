<?php

require_once "../../lib/database.php";

$q = "SELECT * FROM participant_has_condition";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail $q";
	return;
}

$c = array();
while($r = mysqli_fetch_array($ret)) {
	$c[$r["participant_id"]] = $r["condition_id"];
}

// Find the largest left that we've got and the largest right that we've got
$q = "SELECT * FROM `design_specificity`";
$ret = $db->q($q);
if(!$ret) {
	echo "Fail $q";
	return;
}

$data = array();

while($r = mysqli_fetch_array($ret)) {

	$a = $r["a"];
	$b = $r["b"];

	if($r["a_b"] == 1) {
		array_push($data, new Edge($a, $b, 1));
	} else if($r["a_b"] == -1) {
		array_push($data, new Edge($b, $a, 1));
	}
}

$out = "";

$dead = array();

foreach($data AS $d) {
	
	$start = $d->a;
	$end = $d->b;

	// Find everything that starts with the end of this one
	$next = array();
	foreach($data AS $e) {
		if($e->a == $end) {
			array_push($next, $e);
		}
	}

	// Find everything else that starts with the start of this one
	$these = array();
	foreach ($data as $e) {
		if($e->a == $start && $e->b != $end) {
			array_push($these, $e);
		}
	}

	foreach($these AS $t) {
		foreach($next AS $n) {
			if($n->b == $t->b) {
				// Then we need to remove the one in these

				for($i = 0; $i < count($data); $i++) {
					if($data[$i]->a == $t->a && $data[$i]->b == $t->b) {
						// Remove the ith one
						// echo "Remove " . $data[$i]->a . " -> " . $data[$i]->b . "<br />";
						$arr = array_splice($data, $i, 1);
						array_push($dead, $arr[0]);
						break;
					}
				}

			}
		}
	}
}

foreach($dead AS $d) {
	$q = "DELETE FROM `design_specificity` WHERE a = $d->a AND b = $d->b";
	$ret = $db->q($q);
	if(!$ret) {
		echo "Fail $q<br />";
	}
}

foreach ($data as $d) {
	// $out .= "$d->a (" . $c[$d->a] . "),$d->b (" . $c[$d->b] . "),1\n";
	$out .= "$d->a,$d->b,1\n";
}

echo nl2br($out);

$handle = fopen("specific.csv", "w");
fwrite($handle, $out);
fclose($handle);

class Edge {
	public $a;
	public $b;
	public $w;

	function Edge($a, $b, $w) {
		$this->a = $a;
		$this->b = $b;
		$this->w = $w;
	}
}

// echo "</table>";

?>