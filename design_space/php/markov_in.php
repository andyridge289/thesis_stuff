<?php

$lines = File("markov.csv");

$cols = explode(",", $lines[0]);
$options = array_diff($cols, array(""));

//print_r($options);

// Create a matrix where the keys are the names of the options
$m = create2d($options);


for($i = 1; $i < count($lines); $i++)
{
	$line = explode(",", $lines[$i]);
	$causeKey = $line[0];
	
	for($j = 0; $j < count($line); $j++)
	{
		$effectKey = $tags[$j];
		$m[$causeKey][$effectKey] = $line[$j];
	}
}

function create2d($options)
{
	$twoDee = array();
	for($i = 1; $i < count($options); $i++)
	{
		$twoDee[$options[$i]] = array();
		
		for($j = 0; $j < count($options); $j++)
		{
			$row = &$twoDee[$options[$i]];
			$name = $options[$j];
			$row[$name] = 0;
		}
	}
	
	return $twoDee;
}

print_r($m);

?>