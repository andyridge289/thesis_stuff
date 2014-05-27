<?php

require_once "../lib/database.php";
require_once "../lib/build_participants.php";

$ps = $participants;

for($i = 0; $i < count($ps); $i++)
{
	$p = $ps[$i];

	$customs = count($p->customs) + count($p->split);
	$total = $customs + count($p->things);

	echo $p->id  . ", " . $p->condition . ", " . $customs . ", " . count($p->things) . ", " . $total . "<br />";
};

?>