<?php 

require_once "database.php";

$q = "SELECT * FROM `option`";
$ret = $db->q($q);
if(!$ret)
	return;

while($r = mysqli_fetch_array($ret))
{
	$q2 = "SELECT DISTINCT tool_id FROM tool_has_option WHERE option_id = $r[id]";
	$ret2 = $db->q($q2);
	if(!$ret2)
		continue;
		
	while($r2 = mysqli_fetch_array($ret2))
	{
		$q3 = "SELECT * FROM tool_has_option WHERE option_id = $r[id] AND tool_id = $r2[tool_id]";
		$ret3 = $db->q($q3);
		if(!$ret3)
			continue;
			
		if(mysqli_num_rows($ret3) > 1)
		{
			$r3 = mysqli_fetch_array($ret3);
			
			echo "$r3[id] More than 1: $r[name] $r2[tool_id]<br />"; 
			
			$q4 = "DELETE FROM tool_has_option WHERE id = $r3[id]";
			$ret4 = $db->q($q4);
			if(!$ret4)
			{
				continue;
			}
		}
	}
}


?>