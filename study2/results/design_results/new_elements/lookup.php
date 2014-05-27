<?php

require_once "../../../lib/database.php";

$q = "SELECT * FROM `thing_new`";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

$results = array();
while($r = mysqli_fetch_array($ret))
{
	array_push($results, new INP($r["id"], $r["name"]));
}

echo "results = " . json_encode($results);

class INP
{
	public $id;
	public $name;

	function INP($id, $name)
	{
		$this->id = $id;
		$this->name = $name;
	}
}

?>