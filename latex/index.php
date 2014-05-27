<?php

ini_set("auto_detect_line_endings", true);

$lines = File("4.3_model.tex");
$handle = fopen("4.3_model_new.tex", "w");

for($i = 0; $i < count($lines); $i++)
{
	$line = $lines[$i];
	
	$c = substr_count($line, "\\\\");
	
	if($c == 2)
	{
		$firstPos = strpos($line, "\\\\");
		
		$first = trim(substr($line, 0, $firstPos));
		$line = substr($line, $firstPos + 2, strlen($line) - $firstPos - 2);
		
		$secondPos = strpos($line, "\\\\");
		$second = trim(substr($line, 0, $secondPos));
		$third = trim(substr($line, $secondPos + 2, strlen($line) - $secondPos - 2));
		
		$secondPos = strpos($line, "-");
		$secondFirst = $second;
		$secondSecond = "";
		
		if($secondPos !== FALSE)
		{
			$secondFirst = trim(substr($second, 0, $secondPos - 1));
			$secondSecond = trim(substr($second, $secondPos + 1, strlen($second) - $secondPos - 1));	
		}
		
		$line = "$first \\hfill $third \\\\ $secondFirst \\hfill $secondSecond\n"; 
	}
	
	fwrite($handle, $line);
}

fclose($handle);

?>