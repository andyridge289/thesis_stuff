
<html>
	<head>
		<link rel="stylesheet" href="../../css/bootstrap.css" />
	</head>
	<body>
<?php 

$p = isset($_GET["p"]) ? $_GET["p"] : 1;

require_once "../../lib/database.php";
require_once "../../lib/build_new_tree.php";

echo "<div>
	<a href='participant_design.php?p=" . ($p + 1) . "'>Next</a>
</div>";

// for($p = 1; $p < 42; $p++)
// {

	$q = "SELECT * FROM `participant_postq` WHERE participant_id = $p";
	$ret = $db->q($q);
	if(!$ret){ echo "Fail: $q<br />"; return; }
	$r = mysqli_fetch_array($ret);

	echo "<h3>Participant $p</h3><p>$r[result_comments]</p>";
	echo "<h4>Design choices</h4>";


	$q = "SELECT t.name AS thing, t.type AS type
			FROM `participant_has_custom` AS pc
			LEFT JOIN `custom_is_thing` AS ct ON pc.id = ct.custom_id
			LEFT JOIN `thing` AS t ON ct.thing_id = t.id
			WHERE pc.participant_id = $p";
	$ret = $db->q($q);
	if(!$ret){ echo "Fail: $q<br />"; return; }

	$things = array();
	while($r = mysqli_fetch_array($ret))
	{
		if(!in_array($r, $things))
			array_push($things, $r);
	}

	$q = "SELECT t.name AS thing, t.type AS type
			FROM `participant_has_option` AS po
			LEFT JOIN `ds_map` AS dm ON po.option_id = dm.old_id
			LEFT JOIN `thing` AS t ON dm.thing_id = t.id
			WHERE po.participant_id = $p";
	$ret = $db->q($q);
	while($r = mysqli_fetch_array($ret))
	{
		if($r["type"] == "option" && !in_array($r, $things))
			array_push($things, $r);

	}

	foreach($things AS $r)
	{
		$col = $r["type"] === "decision" ? "#000080" : $r["type"] == "option" ? "#FFF" : "#54A854";

		$node = findInTree($newRoot, $r["thing"]);

		echo "<div> <span class='label label-info' style='margin-left:5px;color:$col;' >$r[thing]</span>" . reverseString($node) . "</div>";
	}

	$q = "SELECT * 
			FROM `participant_has_custom` AS pc
			WHERE pc.participant_id = $p AND pc.ignore_custom = 0";

	$ret = $db->q($q);
	if(!$ret){ echo "Fail: $q<br />"; return; }

	while($r = mysqli_fetch_array($ret))
	{
		echo "<div><b>$r[name]</b><br />Description: $r[description]<br /><br />Rationale: $r[rationale]</div><br />";
	}

	$q = "SELECT * FROM `participant_has_option` AS po
			INNER JOIN `option` AS o ON po.option_id = o.id
			WHERE participant_id = $p";
	$ret = $db->q($q);
	if(!$ret){ echo "Fail: $q<br />"; return; }

	while($r = mysqli_fetch_array($ret))
	{
		echo "<div><b>$r[name]</b><br />Description: $r[description]<br /><br />Rationale: $r[rationale]</div><br />";
	}



	// break;
// }

function inArray()
{

}

?>
	</body>
</html>