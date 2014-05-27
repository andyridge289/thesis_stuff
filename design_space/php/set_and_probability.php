<?php

require_once "database.php";

$q = "TRUNCATE TABLE tools_has_p";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}


$q = "SELECT * FROM tool_has_option";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$options = array();
while($row = mysqli_fetch_array($retval))
{
	$c = $row["category_id"];
	$d = $row["decision_id"];
	$o = $row["option_id"];
	$t = $row["tool_id"];
	
	$thing = findThing($options, $c, $d, $o);
	
	if($thing === null)
	{
		$thing = new Thing($c, $d, $o, $t);
		array_push($options, $thing);
	}
	else
	{
		$thing->addT($t);
	}
}

$q = "SELECT * FROM `tool`";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

$toolCount = mysqli_num_rows($retval);
$threshold = $toolCount / 2;

foreach($options AS $option)
{
	$option->setP($toolCount);
}

$secondOptions = array_merge(array(), $options);

$combos = array();

// Then for each combination find the probability of both of them happening??
// Then do both / individual
for($i = 0; $i < count($options); $i++)
{
	for($j = 0; $j < count($secondOptions); $j++)
	{
		if($options[$i]->c === $secondOptions[$j]->c 
		&& $options[$i]->d === $secondOptions[$j]->d 
		&& $options[$i]->o === $secondOptions[$j]->o)
		{
			continue;
		}
		
		// There should be some threshold if there aren't any things
		if(count($options[$i]->ts) < $threshold)
		{
			continue;
		}
		
		// I don't know if we should be restricting on the second one, probably not?
		//if(count($secondOptions[$j]->ts) < $threshold)
		//{
		//	continue;
		//}
		
		$combo = new Combo($options[$i], $secondOptions[$j], $toolCount);	
		$q = $combo->createQuery();
		$retval = $database->query($q);
		if(!$retval)
		{
			echo "Fail $q<br />";
			continue;
		}	
	}
}

function findThing(&$array, &$c, &$d, &$o)
{
	foreach($array AS $thing)
	{
		if($thing->c === $c && $thing->d === $d && $thing->o === $o)
		{
			return $thing;
		}
	}
	
	return null;
}

class Thing
{
	public $c;
	public $d;
	public $o;
	public $ts;
	public $p;
	
	function Thing($c, $d, $o, $t)
	{
		$this->c = $c;
		$this->d = $d;
		$this->o = $o;
		$this->ts = array();
		array_push($this->ts, $t);
	}
	
	function addT($t)
	{
		array_push($this->ts, $t);
	}
	
	function toString()
	{
		return "$this->c $this->d $this->o t[" . count($this->ts) . "] ===> $this->p<br />";
	}
	
	function setP($toolCount)
	{
		$this->p = count($this->ts) / $toolCount;
	}
	
	function createQuery()
	{
		return "INSERT INTO `tool_has_p` VALUES('', $this->c, $this->d, $this->o," . count($this->ts) . ", $this->p)";
	}
}

class Combo
{
	public $firstThing;
	public $secondThing;
	public $both;
	public $p_and;
	public $bayes;
	
	function Combo($firstThing, $secondThing, $toolCount)
	{
		$this->firstThing = $firstThing;
		$this->secondThing = $secondThing;
		$this->both = array();
		
		$firstTs = $this->firstThing->ts;
		$secondTs = $this->secondThing->ts;
		
		foreach($firstTs as $t)
		{
			if(in_array($t, $secondTs))
			{
				array_push($this->both, $t);
			}
		}
		
		$this->p_and = count($this->both) / $toolCount;
		
		$p = $this->firstThing->p;
		
		if($p == 0)
			$this->bayes = 0;
		else
			$this->bayes = $this->p_and / $p;
	}
	
	function createQuery()
	{
		return "INSERT INTO `tools_has_p` VALUES('', " . 
		$this->firstThing->c . ", " . $this->firstThing->d . ", " . $this->firstThing->o . ", " .
 		$this->secondThing->c . ", " . $this->secondThing->d . ", " . $this->secondThing->o . ", " .
		count($this->both) . ", " . $this->p_and . ", " . $this->bayes . ")";
	}
}

?>