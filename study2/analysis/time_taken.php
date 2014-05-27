<?php

require_once "../lib/database.php";

time_taken();

function time_taken()
{
	global $db;

	$q = "SELECT * FROM `participant_has_condition` AS `pc`
			LEFT JOIN `participant_metrics` AS `pm` 
				ON pc.participant_id = pm.participant_id
			ORDER BY pc.condition_id";
	$ret = $db->q($q);
	if(!$ret){ echo "Fail: $q<br />"; return; }
	$out = "";

	while($r = mysqli_fetch_array($ret))
	{
		$out .= "$r[condition_id],$r[time_taken]\n";
	}

	$handle = fopen("time_taken.csv", "w");
	fwrite($handle, $out);
	fclose($handle);
}

// // 1 Per participant
// function count_per_participant()
// {
// 	global $db;

// 	$q = "SELECT id FROM participant";
// 	$ret = $db->q($q);
// 	if(!$ret){ echo "Fail: $q<br />"; return; }

// 	echo "<table><tr><th>Participant ID</th><th># Decisions</th></tr>";

// 	while($r = mysqli_fetch_array($ret))
// 	{
// 		$p = $r["id"];

// 		$q2 = "SELECT * FROM participant_has_custom WHERE participant_id = $p";
// 		$ret2 = $db->q($q2);
// 		if(!$ret2){ echo "Fail: $q2<br />"; return; }
// 		$c = mysqli_num_rows($ret2);

// 		$q2 = "SELECT * FROM participant_has_option WHERE participant_id = $p";
// 		$ret2 = $db->q($q2);
// 		if(!$ret2){ echo "Fail: $q2<br />"; return; }
// 		$o = mysqli_num_rows($ret2);

// 		echo "<tr><td>$p</td><td>" . ($c + $o) . "</td></tr>";
// 	}

// 	echo "</table>";
// }



// 2 Per condition (total)
function count_per_condition()
{
	global $db;

	$cond = array();
	$cond[1] = new Thing();
	$cond[2] = new Thing();
	$cond[3] = new Thing();
	$cond[4] = new Thing();
	$cond[5] = new Thing();

	$q = "SELECT * FROM participant_has_condition GROUP BY condition_id";
	$ret = $db->q($q);
	if(!$ret){ echo "Fail: $q<br />"; return; }

	while($r = mysqli_fetch_array($ret))
	{
		$condition = intval($r["condition_id"]);
		$p = $r["participant_id"];

		$q2 = "SELECT * FROM participant_has_custom WHERE participant_id = $p";
		$ret2 = $db->q($q2);
		if(!$ret2){ echo "Fail: $q2<br />"; return; }
		$c = mysqli_num_rows($ret2);

		$q2 = "SELECT * FROM participant_has_option WHERE participant_id = $p";
		$ret2 = $db->q($q2);
		if(!$ret2){ echo "Fail: $q2<br />"; return; }
		$o = mysqli_num_rows($ret2);

		$cond[$condition]->add($c + $o);
	}

	echo "<table>
		<tr><td>Condition</td><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th></tr>
		<tr><th>Total</th><td>" . $cond[1]->c . "</td><td>" . $cond[2]->c . 
		"</td><td>" . $cond[3]->c . "</td><td>" . $cond[4]->c . "</td><td>" . 
		$cond[5]->c . "</td></tr><tr><th>Average</th><td>" . $cond[1]->a .
		"</td><td>" . $cond[2]->a . "</td><td>" . $cond[3]->a . "</td><td>" . 
		$cond[4]->a . "</td><td>" . $cond[5]->a . "</td></tr></table>";
}

class Thing
{
	public $c;
	public $n;
	public $a;

	function Thing()
	{
		$this->c = 0;
		$this->n = 0;
		$this->a = 0;
	}

	function add($stuff)
	{
		$this->c += $stuff;
		$this->n++;
		$this->a = $this->c / $this->n;
	}
}

?>