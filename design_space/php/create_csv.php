<?php

require_once "database.php";

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
	$optionName = $row["option_name"];
	
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
	
	$thirdRow .= "$optionName,";
	
 	//array_push($cols, new Sextuple($categoryId, $categoryName, $decisionId, $decisionName, $optionId, $optionName));
}

$q = "SELECT * FROM tool";// INNER JOIN tool_has_option ON tool.id = tool_has_option.tool_id";
$retval = $database->query($q);
if(!$retval) { echo "Tool fail"; return; }
$tools = array();

while($row = mysqli_fetch_array($retval))
{
	$toolName = $row["name"];
	echo "$toolName<br />";
	array_push($tools, $toolName);
	/*$toolId = $row["tool_id"];
	$categoryId = $row["category_id"];
	$decisionId = $row["decision_id"];
	$optionId = $row["option_id"];
	echo "$toolId $toolName $categoryId $decisionId $optionId<br />";*/
	
}

$handle = fopen("tools.csv", "w");

//fwrite($handle, $topRow . "\n");
//fwrite($handle, $secondRow . "\n");
fwrite($handle, $thirdRow . "\n");

foreach($tools as $tool)
{
	fwrite($handle, "$tool\n");
}

fclose($handle);

/*class Sextuple
{	
	public $categoryId;
	public $decisionId;
	public $optionId;
	public $categoryName;
	public $decisionName;
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