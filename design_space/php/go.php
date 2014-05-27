<?php

// Clear
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "clear.php");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($curl);
curl_close($curl);
echo "Clear:$result<br />";

// Connect and read with each filename
$filenames = array("ds_functional.txt", "ds_structural_tool.txt");
for($i = 0; $i < count($filenames); $i++)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "connect.php?f=$filenames[$i]");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($curl);
	curl_close($curl);
	echo "<br />$filenames[$i]: $result<br />";
}

// Then output for each value
$indexes = array(1, 2, 3);
foreach($indexes as $index)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "out.php?c=$index");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($curl);
	echo "<br />out $index: $result<br />";
	curl_close($curl);
}

?>