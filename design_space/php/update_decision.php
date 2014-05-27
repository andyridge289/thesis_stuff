<?php

require_once "database.php";

$lines = File("Decision.txt");

foreach($lines AS $line)
{
	$line = explode(",", $line);

	$id = $line[0];
	$min = $line[3];
	$max = $line[4];
	
	$q = "UPDATE decision SET min_cardinality = $min, max_cardinality = $max WHERE id = $id";
	$ret = $db->query($q);
	if(!$ret)
	{
		echo "Fail $id<br />";
	}
}

?>