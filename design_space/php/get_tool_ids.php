<?php

require_once "database.php";

header('Content-type: text/javascript');

$current = isset($_GET["c"]) ? " WHERE currently_available = 1" : "";

$q = "SELECT * FROM tool$current";
$retval = $database->query($q);
if(!$retval)
{
	echo "Fail: $q<br />";
	return;
}

echo "var ids = [";
$string = "";
while($row = mysqli_fetch_array($retval))
{
	$string .= $row["id"] . ",";
}

echo rtrim($string, ",");

echo "];";

?>