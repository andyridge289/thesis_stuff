<?php

require_once "../lib/database.php";

$NUM_CONDITIONS = 6;

// QUANTITATIVE

// H1 the number of decisions made differs significantly across conditions
	// We are expecting 2-5 to make more than 1

// H2 The proportion of decisions made within/without prior DS model differs significantly across conditions
	// We are expecting 1 to make more without and 2-5 to make more within

// H3 The spread of decisions across the various sub-models of the design space differs significantly across conditions
	// We are expecting 1 to be less evenly spread and 2-5 to be more 


$data = decisions_per_participant();

echo "<h4># Decisions per participant</h4>";
for($i = 1; $i <= $NUM_CONDITIONS; $i++)
{
	// echo "$i: ";
	$condition = $data[$i];
	// print_r($condition);
	// echo "<br />";
	for($j = 0; $j < count($condition); $j++)
	{
		$obj = $condition[$j];
		echo "$obj->p," . ($obj->o + $obj->c) . "<br />";
	}
}

echo "<h4>Mean # Decisions per condition</h4>";
for($i = 1; $i <= $NUM_CONDITIONS; $i++)
{

	$condition = $data[$i];
	$runningTotal = 0;

	for($j = 0; $j < count($condition); $j++)
	{
		$runningTotal += $condition[$j]->c;
		$runningTotal += $condition[$j]->o;
	}

	if(count($condition) == 0)
		echo "$i,0<br/>";
	else
		echo "$i," . ($runningTotal / count($condition)) . "<br />";
}

// Proportion of decisions within/without pre-identified DS model
$data = decisions_in_model();

echo "<h4># in DS per participant</h4>";
echo "P,in,out,%<br/>";
for($i = 1; $i <= $NUM_CONDITIONS; $i++)
{
	$condition = $data[$i];

	for($j = 0; $j < count($condition); $j++)
	{
		$obj = $condition[$j];
		echo "$obj->p," . $obj->o . "," . $obj->c . "," . round( $obj->o / ($obj->c + $obj->o), 4 ) . "<br />";
	}
}

echo "<h4>Mean # Decisions per DS per condition</h4>";
for($i = 1; $i <= $NUM_CONDITIONS; $i++)
{
	$condition = $data[$i];
	$runningTotalIn = 0;
	$runningTotalOut = 0;

	for($j = 0; $j < count($condition); $j++)
	{
		$runningTotalOut += $condition[$j]->c;
		$runningTotalIn += $condition[$j]->o;
	}

	if(count($condition) == 0)
		echo "$i,0,0<br/>";
	else
		echo "$i," . $runningTotalIn . "," . $runningTotalOut . "," . round($runningTotalIn / ($runningTotalIn + $runningTotalOut) , 4) . "<br />";
}

echo "<h4># decisions per DS model per category</h4>";
$data = decisions_across_sub_models();

for($i = 1; $i <= $NUM_CONDITIONS; $i++)
{
	$condition = $data[$i];

	for($j = 0; $j < count($condition); $j++)
	{
		$obj = $condition[$j];
		$counts = $obj->c;
		echo $obj->p . "," . $counts[1] . "," . $counts[2] . "," .
			 $counts[3] . "," . $counts[4] . "<br />";
	}
}

function decisions_per_participant()
{
	global $db, $NUM_CONDITIONS;

	$q = "SELECT * FROM participant_has_condition";

	$ret = $db->q($q);
	if(!$ret) return;

	$data = array();
	for($i = 1; $i <= $NUM_CONDITIONS; $i++)
		$data[$i] = array();

	while($r = mysqli_fetch_array($ret))
	{
		$customQ = "SELECT * FROM participant_has_custom WHERE participant_id = $r[participant_id] AND ignore_custom = 0";
		$ret2 = $db->q($customQ);
		if(!$ret2) { echo "fail $customQ"; continue; }

		$c = mysqli_num_rows($ret2);

		$optionQ = "SELECT * FROM participant_has_option WHERE participant_id = $r[participant_id]";
		$ret2 = $db->q($optionQ);
		if(!$ret2) { echo "fail $optionQ"; continue; }

		$o = mysqli_num_rows($ret2);

		$condition = $r["condition_id"] * 1;

		array_push($data[$condition], new Pair($r["participant_id"], $c, $o)); 
	}

	return $data;
}

function decisions_in_model()
{
	global $db, $NUM_CONDITIONS;

	$q = "SELECT * FROM participant_has_condition";

	$ret = $db->q($q);
	if(!$ret) return;

	$data = array();
	for($i = 1; $i <= $NUM_CONDITIONS; $i++)
		$data[$i] = array();


	while($r = mysqli_fetch_array($ret))
	{
		// These ones are definitely in the model
		$optionQ = "SELECT * FROM participant_has_option WHERE participant_id = $r[participant_id]";
		$ret2 = $db->q($optionQ);
		if(!$ret2) { echo "fail $optionQ"; continue; }
		$o = mysqli_num_rows($ret2);

		$condition = $r["condition_id"] * 1;

		$customQ = "SELECT * FROM participant_has_custom AS pc
					WHERE pc.participant_id = $r[participant_id]
					AND pc.ignore_custom = 0";
		
		$ret2 = $db->q($customQ);
		if(!$ret2) { echo "fail $customQ"; continue; }

		
		$newO = 0;
		$totalC = mysqli_num_rows($ret2);

		while($r2 = mysqli_fetch_array($ret2))
		{
			$q3 = "SELECT * FROM custom_is_option WHERE custom_id = $r2[id]";
			$ret3 = $db->q($q3);
			if(!$ret3) { echo "Fail: $q3"; continue; }
			$newO += mysqli_num_rows($ret3);

			$q3 = "SELECT * FROM custom_is_decision WHERE custom_id = $r2[id]";
			$ret3 = $db->q($q3);
			if(!$ret3) { echo "Fail: £q3"; continue; }
			$newO += mysqli_num_rows($ret3);
		}

		$c = $totalC - $newO;
		$o += $newO;

		array_push($data[$condition], new Pair($r["participant_id"], $c, $o)); 
	}

	return $data;
}

function decisions_across_sub_models()
{
	global $db, $NUM_CONDITIONS;

	$q = "SELECT * FROM participant_has_condition";
	$ret = $db->q($q);
	if(!$ret) { echo "Fail: $q"; return; }

	$data = array();
	for($i = 1; $i <= $NUM_CONDITIONS; $i++)
		$data[$i] = array();


	while($r = mysqli_fetch_array($ret))
	{
		$condition = $r["condition_id"] * 1;

		$counts = array();
		$counts[1] = 0;
		$counts[2] = 0;
		$counts[3] = 0;
		$counts[4] = 0;

		// TODO There are three missing, where did they go?

		$total = 0;

		$q2 = "SELECT * FROM participant_has_option AS po
			LEFT JOIN decision_has_option AS do ON po.option_id = do.option_id
			WHERE po.participant_id = $r[participant_id]";
		$ret2 = $db->q($q2);
		if(!$ret2){ echo "Fail: $q2"; continue; }

		while($r2 = mysqli_fetch_array($ret2))
		{
			$cat = $r2["category_id"] * 1;
			$counts[$cat]++;
			$total++;
		}

		$q2 = "SELECT * FROM participant_has_custom AS pc
			LEFT JOIN custom_is_option AS co ON pc.id = co.custom_id
			LEFT JOIN decision_has_option AS do ON co.option_id = do.option_id
			WHERE pc.participant_id = $r[participant_id]
			AND pc.ignore_custom = 0";

		$ret2 = $db->q($q2);
		if(!$ret2){ echo "Fail: $q2"; continue; }

		while($r2 = mysqli_fetch_array($ret2))
		{
			$cat = $r2["category_id"] * 1;

			if($cat == 0)
				continue;

			$counts[$cat]++;
			$total++;
		}

		$q2 = "SELECT * FROM participant_has_custom AS pc
			LEFT JOIN custom_is_decision AS cd ON pc.id = cd.custom_id
			LEFT JOIN decision_has_option AS do ON cd.decision_id = do.decision_id
			WHERE pc.participant_id = $r[participant_id]
			AND pc.ignore_custom = 0";

		$ret2 = $db->q($q2);
		if(!$ret2){ echo "Fail: $q2"; continue; }



		$q2 = "SELECT * FROM participant_has_custom AS pc
			LEFT JOIN custom_has_ds AS cd ON pc.id = cd.custom_id
			WHERE pc.participant_id = $r[participant_id]
			AND pc.ignore_custom = 0";

		$ret2 = $db->q($q2);
		if(!$ret2){ echo "Fail: $q2"; continue; }

		while($r2 = mysqli_fetch_array($ret2))
		{
			$cat = $r2["ds_id"] * 1;

			// echo "$cat<br />";

			if($cat == 0)
				continue;

			$counts[$cat]++;
			$total++;
		}

		// echo "<br />$total<br />";

		array_push($data[$condition], new Pair($r["participant_id"], $counts, null)); 
	}

	return $data;
}

class Pair
{
	public $p;
	public $c;
	public $o;

	function Pair($participant_id, $customs, $options)
	{
		$this->p = $participant_id;
		$this->c = $customs;
		$this->o = $options;
	}
}

?>