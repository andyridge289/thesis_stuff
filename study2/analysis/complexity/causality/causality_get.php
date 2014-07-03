<?php

require_once "../../../lib/database.php";

$q = "SELECT * FROM participant_has_condition";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail $q";
	return;
}

$c = array();
while($r = mysqli_fetch_array($ret)) {

	if($r["participant_id"] == 34)
		continue;

	array_push($c, new Triple($r["participant_id"], $r["condition_id"]));
}

$all = "participant,condition,causals\n";
$tree = "participant,condition,causals\n";
$list = "participant,condition,causals\n";
$amalgam = "participant,condition,causals,representation\n";

foreach($c AS $p) {

	$q = "SELECT DISTINCT id FROM `design_causality` WHERE participant_id = $p->id";
	$ret = $db->q($q);
	$p->causals = mysqli_num_rows($ret);

	$line = "$p->id,$p->condition,$p->causals\n";

	$all .= $line;

	if($p->condition == 1) {
		$tree .= $line;
		$list .= $line;
	} else if($p->condition % 2 === 0) {
		$tree .= $line;
	} else {
		$list .= $line;
	}

	$level = $p->condition > 3 ? 3 : 
				$p->condition > 1 ? 2 : 1;
	$rep = $p->condition % 2 == 0 ? "Tree" : "List";

	$line2 = "$p->id,$level,$p->causals,";

	if($p->condition > 1) {
		$amalgam .= $line2 . "$rep\n";
	} else {
		$amalgam .= $line2 . "Tree\n";
		$amalgam .= $line2 . "List\n";
	}
}

$handle = fopen("causality_3.csv", "w");
fwrite($handle, $amalgam);
fclose($handle);

$handle = fopen("causality_5.csv", "w");
fwrite($handle, $all);
fclose($handle);

$handle = fopen("causality_124.csv", "w");
fwrite($handle, $tree);
fclose($handle);

$handle = fopen("causality_135.csv", "w");
fwrite($handle, $list);
fclose($handle);

class Triple
{
	public $id;
	public $condition;
	public $causals;

	function Triple($id, $c) {
		$this->id = $id;
		$this->condition = $c;
	}
}

?>