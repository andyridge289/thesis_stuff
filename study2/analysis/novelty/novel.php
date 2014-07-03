<?php

require_once "../../lib/build_participants.php";

$all5 = "participant,condition,new\n";
$all3 = "participant,condition,new,representation\n";
$all124 = "participant,condition,new\n";
$all135 = "participant,condition,new\n";

for($i = 0; $i < count($participants); $i++)
{
	$p = $participants[$i];

	$line = "$p->id,$p->condition," . count($p->new);

	$all5 .= "$line\n";

	if($p->condition == 1) {
		$all124 .= "$line\n";
		$all135 .= "$line\n";
		$all3 .= "$line,tree\n";
		$all3 .= "$line,list\n";
	} else if($p->condition == 2) {
		$all124 .= "$line\n";
		$all3 .= "$line,tree\n";
	} else if($p->condition == 3) {
		$all135 .= "$line\n";
		$all3 .= "$p->id,2," . count($p->new) . ",list\n";
	} else if($p->condition == 4) {
		$all124 .= "$line\n";
		$all3 .= "$p->id,3," . count($p->new) . ",tree\n";
	} else {
		$all135 .= "$line\n";
		$all3 .= "$p->id,3," . count($p->new) . ",list\n";
	}
}

echo nl2br($all5);

$handle = fopen("new_5.csv", "w");
fwrite($handle, $all5);
fclose($handle);

$handle = fopen("new_3.csv", "w");
fwrite($handle, $all3);
fclose($handle);

$handle = fopen("new_124.csv", "w");
fwrite($handle, $all124);
fclose($handle);

$handle = fopen("new_135.csv", "w");
fwrite($handle, $all135);
fclose($handle);
?>