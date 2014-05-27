<?php

require_once "database.php";

$output = "var m = {\n";

$options = array();
$q = "SELECT * FROM `option`";

$r = $db->query($q);
if(!$r)
{
	return;
}

while($row = mysqli_fetch_array($r))
{
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
			
			$twoDee[$first][$second]++;
		}
	}
}

$keys = array_keys($twoDee);

for($i = 0; $i < count($keys); $i++)
{
	$total = 0;
	$key = $keys[$i];
	$row = &$twoDee[$key];

	for($j = 0; $j < count($keys); $j++)
	{
		$key2 = $keys[$j];
		$total += $row[$key2];
	}
	
	if($total === 0)
	{
		//echo "Zero total for $key<br />";
		continue;
	}
	
	for($j = 0; $j < count($keys); $j++)
	{
		$key2 = $keys[$j];
		//$row[$key2] /= $total;
	}
}

for($i = 0; $i < count($keys); $i++)
{
	$key = $keys[$i];
	$sKey = sanitiseKey($key);
	$output .= "$sKey: [";
	$row = $twoDee[$key];
	
	for($j = 0; $j < count($keys); $j++)
	{
		$key2 = $keys[$j];
		$sKey2 = sanitiseKey($key2);
		
		if($j > 0)
			$output .= ",";

		$output .= "\n\t{ name:\"$key2\", key:\"sKey2\", value:" . $row[$key2] . "}";
	}
	
	//echo $output;
	
	if($i < count($keys) - 1)
		$output .= "],\n";
	else
		$output .= "]";
}

$output .= "};";

$output .= "\n\nvar k = [";
for($i = 0; $i < count($keys); $i++)
{
	if($i > 0)
		$output .= ",";
		
	$key = $keys[$i];

	$output .= "\n{ name: \"$key\", value: \"" . sanitiseKey($key) . "\"}";
}
$output .= "];";

$handle = fopen("markov.json", "w");
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

function sanitiseKey($key)
{
	$s = str_replace(" ", "_", $key);
	$s = preg_replace('/[^a-z\d ]/i', '', $s);
	return $s;
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