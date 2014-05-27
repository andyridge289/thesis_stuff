<?php 

require_once "../../php/database.php";

$q = "UPDATE `thing` SET type = '$_POST[type]' WHERE id = $_POST[id]";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

echo "win";

?>