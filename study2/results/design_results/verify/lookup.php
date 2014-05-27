<?php 

require_once "../../../lib/database.php";

if(!isset($_POST["customId"]))
{
	// echo "Fail: no customId";
	// return;
	$_POST["customId"] = 33;
}

$verified = array();
$unverified = array();

echo "var data = { \"custom_id\": $_POST[customId],
	\"verified\":";

// Need to get the things that are linked to the custom that ARE verified
$q = "SELECT ct.id AS ct_id, ct.thing_id, t.name AS thing_name, ct.verified AS verified, 
			t.type AS type, t.stage_removed AS stage_removed
		FROM `custom_is_thing` AS ct
		LEFT JOIN `thing` AS t on ct.thing_id = t.id
		WHERE ct.custom_id = $_POST[customId]";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

while($r = mysqli_fetch_array($ret))
{
	if($r["verified"] == 1)
	{
		array_push($verified, new Thing($r["thing_id"], $r["thing_name"], $r["type"], true, $r["ct_id"], $r["stage_removed"]));
	}
	else
	{
		array_push($unverified, new Thing($r["thing_id"], $r["thing_name"], $r["type"], false, $r["ct_id"], $r["stage_removed"]));
	}
}

echo json_encode($verified);
echo ",\"unverified\":";
echo json_encode($unverified);
echo ",\"matches\":";

$q = "SELECT name, description, rationale FROM `participant_has_custom` WHERE id = $_POST[customId]";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}
$r = mysqli_fetch_array($ret);
$name = explode(" ", $r["name"]);
$description = explode(" ", $r["description"]);
$rationale = explode(" ", $r["rationale"]);

$wordList = array();
$ignore = array("the", "and", "it", "my", "we", "she", "it", "he", "a", "be", "when", "then", "that", 
	"is", "such", "as", "from", "to", "or", "wish", 
	"perform", "on", "they", "have", "already", "up", "back", "may", "with", "got", "could", "some", "by");

foreach($name AS $word)
{
	$word = preg_replace('/[^a-z]+/i', '', $word); 

	if(in_array(strtolower($word), $ignore) || in_array(strtolower($word), $wordList))
		continue;

	array_push($wordList, $word);
}

foreach($description AS $word)
{
	$word = preg_replace('/[^a-z]+/i', '', $word); 

	if(in_array(strtolower($word), $ignore) || in_array(strtolower($word), $wordList))
		continue;

	// echo "$word<br />";
	array_push($wordList, $word);
}

foreach($rationale AS $word)
{
	$word = preg_replace('/[^a-z]+/i', '', $word); 

	if(in_array(strtolower($word), $ignore) || in_array(strtolower($word), $wordList))
		continue;

	// echo "$word<br />";
	array_push($wordList, $word);
}

$matches = array();
foreach($wordList AS $word)
{
	if($word == "")
		continue;

	$q = "SELECT * FROM `thing` WHERE name REGEXP '$word' AND stage_removed = -1";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		continue;
	}

	while($r = mysqli_fetch_array($ret))
	{
		if(!check($matches, $r["id"]))
			array_push($matches, new Thing($r["id"], $r["name"], $r["type"], false,  "", -1));
	}
}

foreach($matches AS &$match)
{
	$q = "SELECT * FROM custom_is_thing WHERE thing_id = $match->id AND custom_id = $_POST[customId]";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
		continue;
	}

	if(mysqli_num_rows($ret) > 0)
		$match->verified = true;
}

echo json_encode($matches);
echo "}";

// Break the name of the custom into words and find all the things that REGEXP those words

function check($thingArray, $id)
{
	foreach($thingArray AS $thing)
	{
		if($thing->id == $id)
			return true;
	}

	return false;
}

class Thing
{
	public $id;
	public $name;
	public $type;
	public $verified;
	public $relation;
	public $stageRemoved;

	function Thing($id, $name, $type, $verified, $relation, $stageRemoved)
	{
		$this->id = $id;
		$this->name = $name;
		$this->type = $type;
		$this->verified = $verified;
		$this->relation = $relation;
		$this->stageRemoved = $stageRemoved;
	}
}

?>