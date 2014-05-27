<?php

require_once "database.php";

$q = "SELECT t.bayes, t.bothCount, o.name, t.option1, t.category1, t.decision1, t.option2, t.category2, t.decision2 FROM tools_has_p AS t " . 
	"LEFT JOIN `option` AS o ON t.option1 = o.id ORDER BY t.bothCount DESC";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

while($row = mysqli_fetch_array($retval))
{	
	$q2 = "SELECT * FROM `option` WHERE option.id = $row[option2]";
	$retval2 = $database->query($q2);
	if(!$retval2)
	{
		echo "Fail: $q2<br />";
		continue;
	}
	
	$row2 = mysqli_fetch_array($retval2);
	echo "[$row[bothCount]] $row[name] => $row2[name]: $row[bayes]<br />";
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