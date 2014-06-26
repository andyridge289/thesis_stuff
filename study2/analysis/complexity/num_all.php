<?php

require_once "../../lib/database.php";
require_once "../../lib/build_participants.php";

$out = "participant,condition,total\n";

// Participant number, condition, all design choices made.
for($i = 0; $i < count($participants); $i++) {
	$p = $participants[$i];
	$out .= "$p->id,$p->condition," . 
	        (count($p->customs) + count($p->split) + count($p->things)) . "\n";
}

$handle = fopen("complexity_5.csv", "w");
fwrite($handle, $out);
fclose($handle);

echo nl2br($out);


?>