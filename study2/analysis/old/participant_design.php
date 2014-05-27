<html> 
	<head>
		<link href='../css/bootstrap.css' rel='stylesheet' type='text/css'
	</head>
	<body>
		<div style='margin-left:10px;'>
	<?php

require_once "../lib/database.php";
require_once "../lib/build_participants.php";

$ps = $participants;
usort($ps, "cmp");

for($i = 0; $i < count($ps); $i++)
{
	$p = $ps[$i];

	echo "<h4>$p->id ($p->condition)</h4><p><i>Customs:</i></p>";
	for($j = 0; $j < count($p->customs); $j++)
	{
		echo "<p><b>" . $p->customs[$j]->name . "</b><br />" . $p->customs[$j]->description . "</p>";
	}

	// echo "<p><i>Split customs:</i></p>";
	// for($j = 0; $j < count($p->split); $j++)
	// {
	// 	echo "<p><b>" . $p->split[$j]->name . "</b><br />" . $p->split[$j]->description . "</p>";
	// }

	for($j = 0; $j< count($p->customsAreThings); $j++)
	{
		echo "<span class='label label' style='margin-right:5px;margin-top:2px;'>" . $p->customsAreThings[$j]->name . "</span>";
	}
	echo "<br /><br />";

	for($j = 0; $j< count($p->things); $j++)
	{
		echo "<span class='label label-info' style='margin-right:5px;margin-top:2px;'>" . $p->things[$j]->name . "</span>";
	}
	echo "<br /><br />";



	for($j = 0; $j < count($p->new); $j++)
	{
		echo "<span class='label' style='background-color:red;margin-right:5px;margin-top:2px;'>" . $p->new[$j]->name . "</span>";
	}

	echo "<br /><br />";
}

function cmp($a, $b)
{
	return $a->id > $b->id;
}

?>
	</div>
</body>
</html>