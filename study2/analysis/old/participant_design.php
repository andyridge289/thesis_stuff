<html> 
	<head>
		<link href='../css/bootstrap.css' rel='stylesheet' type='text/css'
	</head>
	<body>
		<div style='margin-left:10px;'>
	<?php

require_once "../../lib/database.php";
require_once "../../lib/build_participants.php";

$ps = $participants;
usort($ps, "cmp");

for($i = 0; $i < count($ps); $i++)
{
	$p = $ps[$i];

	echo "<h4>$p->id ($p->condition)</h4><p><i>Customs:</i></p>";
	if(count($p->customs) == 0) {
		echo "None";
	} else  {
		for($j = 0; $j < count($p->customs); $j++)
		{
			$c = $p->customs[$j];

			echo "<p><b>[" . $c->id . "]:  " . $c->name . "</b>";
			echo "<br />" . $c->description . "</p>";
		}
	}

	// echo "<p><i>Split customs:</i></p>";
	// if(count($p->split) == 0) {
	// 	echo "None";
	// } else {
	// 	for($j = 0; $j < count($p->split); $j++)
	// 	{
	// 		echo "<p><b>" . $p->split[$j]->name . "</b><br />" . $p->split[$j]->description . "</p>";
	// 	}
	// }

	// echo "<p><i>Custom -> Things</i></p>";
	// if(count($p->customsAreThings) == 0) {
	// 	echo "None";
	// } else {
	// 	for($j = 0; $j< count($p->customsAreThings); $j++)
	// 	{
	// 		echo "<p><b>" . $p->customsAreThings[$j]->name . "</b><br />" . $p->customsAreThings[$j]->description . "</p>";
	// 		// "<span class='label label' style='margin-right:5px;margin-top:2px;'>" . $p->customsAreThings[$j]->name . "</span>";
	// 	}
	// }	

	echo "<p><i>Things</i></p>";
	if(count($p->things) == 0) {
		echo "None";
	} else {
		for($j = 0; $j< count($p->things); $j++)
		{
			$c = $p->things[$j];
			echo "<p><b>[" . $c->id . "]:  " . $c->name . "</b><br />";
			echo $c->description . "</p>";
			// echo "<span class='label label-info' style='margin-right:5px;margin-top:2px;'>" . $p->things[$j]->name . "</span>";
		}
	}	

	// echo "<p><i>New</i></p>";
	// if(count($p->new) == 0) {
	// 	echo "None";
	// } else {
	// 	for($j = 0; $j < count($p->new); $j++)
	// 	{
	// 		echo "<p><b>" . $p->new[$j]->name . "</b><br />" . $p->new[$j]->description . "</p>";
	// 		// echo "<span class='label' style='background-color:red;margin-right:5px;margin-top:2px;'>" . $p->new[$j]->name . "</span>";
	// 	}
	// }
}

function cmp($a, $b)
{
	return $a->id > $b->id;
}

?>
	</div>
</body>
</html>