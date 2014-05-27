<?php

require_once "database.php";

$q = "SELECT
		d.participantId AS pid, d.code, i.id AS init, n.id AS notInit
		FROM `Data` AS d
		LEFT JOIN `Code` AS c on d.code = c.id
		LEFT JOIN `CodeHasCategory` AS cc ON c.id = cc.codeId
		LEFT JOIN `Category` AS cat ON cc.categoryId = cat.id
		LEFT JOIN `InitialCategory` AS i ON cat.id = i.id
		LEFT JOIN `NotInitialCategory` AS n ON cat.id = n.id";

$ret = $db->q($q);
if(!$ret)
{
	echo "Fail $q";
	return;
}

// I want the participant, the code, and whether it's from an initial category or not

$p = array();
while($r = mysqli_fetch_array($ret))
{
	$pid = $r["pid"];
	if(!array_key_exists($pid, $p))
	{
		$p[$pid] = new Thing($pid);
	}

	if(isset($r["init"]))
	{
		$p[$pid]->numI++;
	}

	if(isset($r["notInit"]))
	{
		$p[$pid]->numS++;
	}
}

$k = array_keys($p);

$init = 0;
$sub = 0;
$tot = 0;

$percents = array();

for($i = 0; $i < count($k); $i++)
{
	$pp = $p[$k[$i]];

	$init += $pp->numI;
	$sub += $pp->numS;
	$tot += $pp->numI + $pp->numS;

	echo $pp->numI / ($pp->numI + $pp->numS);
	echo "<br />"; 
}

class Thing
{
	public $id;
	public $numI;
	public $numS;

	function Thing($pid)
	{
		$this->id = $pid;
		$this->numI = 0;
		$this->numS = 0;
	}
}

?>