<?php

require_once "database.php";

$q = "SELECT DISTINCT t1.tool_id, t2.tool_id, o1.name AS cause,
                o2.name AS effect,
                count(*) AS cooccurrence
FROM tool_has_option AS t1
LEFT JOIN tool_has_option AS t2 ON t2.tool_id = t1.tool_id
AND t1.option_id != t2.option_id
LEFT JOIN design_space.option AS o1 ON o1.id = t1.option_id
LEFT JOIN design_space.option AS o2 ON o2.id = t2.option_id
GROUP BY t1.option_id,
         t2.option_id;";
        
$ret = $db->query($q);
if(!$ret)
	return;
	
$output = "var m = {\n";
$currentCause = "";
$m = array();

while($r = mysqli_fetch_array($ret))
{
	if(strcmp($r["cause"], $currentCause) !== 0)
	{
		// The causes are different, reset the cause
		// then create a new array
		$currentCause = $r["cause"];
		$m[$currentCause] = array();
	}
	
	$effect = $r["effect"];
	$m[$currentCause][$effect] = $r["cooccurrence"];	
}

$keys = array_keys($m);

for($i = 0; $i < count($keys); $i++)
{
	$key = $keys[$i];
	
	if(strcmp($key, "") === 0)
		continue;
	
	$sKey = sanitiseKey($key);
	
	if($i > 0)
		$output .= ",\n";
	
	$output .= "$sKey: [";
	$row = $m[$key];
	
	for($j = 0; $j < count($keys); $j++)
	{
		$key2 = $keys[$j];
		$sKey2 = sanitiseKey($key2);
		
		$val = isset($row[$key2]) ? $row[$key2] : 0;
		
		if($j > 0)
			$output .= ",";

		$output .= "\n\t{ name:\"$key2\", key:\"$sKey2\", value:" . $val . "}";
	}
	
	//echo $output;
	
	if($i < count($keys) - 1)
		$output .= "]";
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

function sanitiseKey($key)
{
	$s = str_replace(" ", "_", $key);
	$s = preg_replace('/[^a-z\d ]/i', '', $s);
	return $s;
}


?>