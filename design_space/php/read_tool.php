<?php

ini_set('auto_detect_line_endings', TRUE);

require_once "database.php";

$handle = fopen("tools.csv", "r");
$categories = fgetcsv($handle);

$CATEGORIES = 0;
$DECISIONS = 1;
$OPTIONS = 2;

$last = "";
for($i = 0; $i < count($categories); $i++)
{
	if(strcmp("", $categories[$i]) === 0)
	{
		$categories[$i] = $last;
	}
	else
	{
		$last = $categories[$i];
	}
}

$decisions = fgetcsv($handle);

$last = "";
for($i = 0; $i < count($decisions); $i++)
{
	if(strcmp("", $decisions[$i]) === 0)
	{
		$decisions[$i] = $last;
	}
	else
	{
		$last = $decisions[$i];
	}
}

$options = fgetcsv($handle);

while($toolRow = fgetcsv($handle))
{
	$toolName = $toolRow[0];
	$toolId = getId("tool", $toolName);
	
	for($i = 1; $i < count($toolRow); $i++)
	{
		if(strcmp("", $toolRow[$i]) !== 0) 
		{
			$categoryId = getId("category", $categories[$i]);
			$decisionId = getId("decision", $decisions[$i]);
			$optionId = getId("option", $options[$i]);
		
			$q = "INSERT INTO tool_has_option VALUES('', $toolId, $categoryId, $decisionId, $optionId)";
			echo "$q<br />";
		}
	}

	break;
}

function getId($table, $name)
{
	global $database;
	$q = "SELECT id FROM `$table` WHERE name = \"$name\"";
	$retval = $database->query($q);
	
	if(!$retval){ echo "Fail"; return; }

	if(mysqli_num_rows($retval) === 0)
	{
		echo "No rows for $table, $name<br />";
	}
	
	$row = mysqli_fetch_array($retval);
	return $row["id"];
}

/*require_once "database.php";

$q = "SELECT decision.id AS decision_id, decision.name AS decision_name, `option`.id AS option_id, `option`.name AS option_name, category.id AS category_id, category.name AS category_name FROM decision_has_option INNER JOIN decision ON decision_has_option.decision_id = decision.id INNER JOIN `option` ON decision_has_option.option_id = `option`.id INNER JOIN category ON category.id = decision_has_option.category_id ORDER BY decision_has_option.category_id, decision_has_option.decision_id";
$retval = $database->query($q);
if(!$retval){ echo "All Fail"; return; }

$currentCategory = -1;
$currentDecision = -1;

$topRow = ",";
$secondRow = ",";
$thirdRow = "Tool Name,";

$cols = array();

while($row = mysqli_fetch_array($retval))
{

	$categoryId = $row["category_id"];
	$categoryName = $row["category_name"];
	$decisionId = $row["decision_id"];
	$decisionName = $row["decision_name"];
	$optionId = $row["option_id"];
	$optionName = $row["decision_name"];
	
	if($currentCategory !== $categoryId)
	{
		$topRow .= "$categoryName,";
		$currentCategory = $categoryId;
	}
	else
	{
		$topRow .= ",";
	}
	
	if($currentDecision !== $decisionId)
	{
		$secondRow .= "$decisionName,";
		$currentDecision = $decisionId;
	}
	else
	{
		$secondRow .= ",";
	}
	
	$thirdRow .= "optionName,";
	
 	array_push($cols, new Sextuple($categoryId, $categoryName, $decisionId, $decisionName, $optionId, $optionName));
}

$q = "SELECT * FROM tool INNER JOIN tool_has_option ON tool.id = tool_has_option.tool_id";
$retval = $database->query($q);
if(!$retval) { echo "Tool fail"; return; }
$tools = array();

$handle = fopen("tools.csv", "w");

fwrite($handle, $topRow . "\n");
fwrite($handle, $secondRow . "\n");
fwrite($handle, $thirdRow);

foreach($tools as $tool)
{
	fwrite($handle, "$tool\n");
}

fclose($handle);

class Sextuple
{	
	public $categoryId;
	public $decisionId;
	public $optionId;
	public $categoryName;
	public $decisionId;
	public $optionName;
	
	function Sextuple($categoryId, $categoryName, $decisionId, $decisionName, $optionId, $optionName)
	{
		$this->categoryId = $categoryId;
		$this->categoryName = $categoryName;
		$this->decisionId = $decisionId;
		$this->decisionName = $decisionName;
		$this->optionId = $optionId;
		$this->optionName = $optionName;
	}
}*/

?>