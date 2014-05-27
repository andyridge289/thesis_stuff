<?php

require_once "database.php";

$threshold = isset($_GET["t"]) ? $_GET["t"] : 0.25;
$threshold = getThreshold($threshold);

$output = "";

$options = array();
$q = "SELECT * FROM `option`";

$r = $db->query($q);
if(!$r)
{
	return;
}

while($row = mysqli_fetch_array($r))
{
	$q2 = "SELECT DISTINCT(tool_id) FROM `tool_has_option` WHERE option_id = $row[id]";
	$r2 = $db->q($q2);
	if(!$r2)
		continue;
		
	$n = 0;
	while($row2 = mysqli_fetch_array($r2))
	{
		// All the ones that are available have IDs less than 18...
		if($row2["tool_id"] < 18)
			$n++;
	}

	if($n > $threshold)
		array_push($options, new Option($row["id"], $row["name"]));
}

$twoDee = create2d($options);

for($i = 0; $i < count($options); $i++)
{
	$optionId = $options[$i]->id;
	
	$q2 = "SELECT DISTINCT tool_id FROM tool_has_option WHERE option_id = " . $options[$i]->id;
	$r2 = $db->query($q2);
	if(!$r2)
	{
		continue;
	}
	
	while($row2 = mysqli_fetch_array($r2))
	{
		$q3 = "SELECT option_id, `option`.name AS name FROM tool_has_option INNER JOIN `option` on tool_has_option.option_id = `option`.id WHERE tool_id = $row2[tool_id]";
		$r3 = $db->query($q3);
		if(!$r3)
		{
			continue;
		}
		
		while($row3 = mysqli_fetch_array($r3))
		{
			// This should be the list of options for the tool
			$first = $options[$i]->name;
			$second = $row3["name"];
			
			if(array_key_exists($second, $twoDee[$first]))
				$twoDee[$first][$second]++;
		}
	}
}

$keys = array_keys($twoDee);
$output = "";

for($i = 0; $i < count($keys); $i++)
{
	$output .= ",$keys[$i]";
}
$output .= "\n";

for($i = 0; $i < count($keys); $i++)
{
	$key = $keys[$i];
	$output .= "$key,";
	$row = $twoDee[$key];
	
	for($j = 0; $j < count($keys); $j++)
	{
		$key2 = $keys[$j];
		$output .= $row[$key2];
		
		if($j < count($keys) - 1)
			$output .= ",";
	}
	
	echo $output;
	
	if($i < count($keys) - 1)
		$output .= "\n";
}

$handle = fopen("cooccurrence_t_$threshold.csv", "w");
fwrite($handle, $output);
fclose($handle);

function create2d($options)
{
	$twoDee = array();
	for($i = 0; $i < count($options); $i++)
	{
		$twoDee[$options[$i]->name] = array();
		
		for($j = 0; $j < count($options); $j++)
		{
			$row = &$twoDee[$options[$i]->name];
			$name = $options[$j]->name;
			$row[$name] = 0;
		}
	}
	
	return $twoDee;
}

function getThreshold($t)
{
	global $db;
	
	$q = "SELECT * FROM `tool` WHERE currently_available = 1";
	$r = $db->q($q);
	if(!$r)
		return 0;
		
	return mysqli_num_rows($r) * $t;
}

class Option
{
	public $id;
	public $name;
	
	function Option($id, $name)
	{
		$this->id = $id;
		$this->name = $name;
	}
}

?>