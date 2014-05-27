<?php

$lines = File("requirements.txt");

$lines2 = array();
foreach ($lines as $line) 
{
	if(strpos($line, "Category") === 0)
	{
		array_push($lines2, strtolower(trim(substr($line, strpos($line, ":") + 1))));
	}
}

$categories = array();
// Do the first category

foreach ($lines2 as &$line) 
{
	$thing = trim(substr($line, 0, strpos($line, "-")));
	

	if(array_key_exists($thing, $categories) === false)
	{
		$categories[$thing] = 1;
	}
	else
	{
		$categories[$thing]++;
	}

	$line = trim(substr($line, strpos($line, "-") + 1));
}

print_r($categories);
echo "<br />";

$moreLines = array();
$subCategories = array();
foreach($lines2 as &$line)
{
	if(strpos($line, "-") == null)
	{
		if(array_key_exists($line, $subCategories) === false)
		{
			$subCategories[$line] = 1;
		}
		else
		{
			$subCategories[$line]++;
		}
	}
	else
	{
		$thing = trim(substr($line, 0, strpos($line, "-")));

		if(array_key_exists($thing, $subCategories) === false)
		{
			$subCategories[$thing] = 1;
		}
		else
		{
			$subCategories[$thing]++;
		}

		$line = trim(substr($line, strpos($line, "-") + 1));
		array_push($moreLines, $line);
	}
}

print_r($subCategories);
echo "<br />";

$subSubCategories = array();
foreach($moreLines AS &$line)
{
	if(array_key_exists($line, $subSubCategories) === false)
	{
		$subSubCategories[$line] = 1;
	}
	else
	{
		$subSubCategories[$line]++;
	}
}
print_r($subSubCategories);

?>