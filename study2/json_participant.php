<?php

require_once "lib/database.php";

$p = isset($_GET["p"]) ? $_GET["p"] : 1;

// Should only need to get the stuff from custom

$q = "SELECT * FROM participant_has_custom WHERE participant_id = $p";


$ret = $db->q($q);
if(!$ret) { echo "Fail: $q<br />"; return; }

$things = array();
while($r = mysqli_fetch_array($ret))
{
	$thing = new Thing($r["name"], $r["description"], $r["rationale"]);
	array_push($things, $thing);
}

echo json_encode($things);


class Thing
{
	public $id;
	public $name;
	public $rationale;
	public $description;
	public $ds;
	public $dsCode;
	public $addedFromId;
	public $addedFromName;
	public $toolView;
	public $optionView;
	public $optionViewProportion;
	public $decision;

	function Thing($name, $description, $rationale)
	{
		$this->id = -1;
		$this->name = $name;
		$this->description = $description;
		$this->rationale = $rationale;
		$this->ds = "Custom Option";
		$this->dsCode = "custom";
		$this->addedFromId = -1;
		$this->addedFromName = "";
		$this->toolView = "";
		$this->optionView = -1;
		$this->optionViewProportion = 0;
		$this->decision = "";
	}

}

?>