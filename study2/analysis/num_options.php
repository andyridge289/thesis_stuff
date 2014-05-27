<?php

require_once "../lib/database.php";
require_once "../lib/build_participants.php";

$output = "id,condition,customs,things,total\n";

for($i = 0; $i < count($participants); $i++)
{
	$p = $participants[$i];
	$output .= "$p->id,$p->condition," . count($p->customs) . "," . count($p->things) . "," . (count($p->things) + count($p->customs)) . "\n";
}

echo nl2br($output);

$handle = fopen("num_things.csv", "w");
fwrite($handle, $output);
fclose($handle);

// // Things per participant, based on category
// $output = "";
// for($i = 0; $i < count($p); $i++)
// {
// 	$output .= $p[$i]->condition . "," . count($p[$i]->things) . "\n";
// }

// $handle = fopen("num_things.csv", "w");
// fwrite($handle, $output);
// fclose($handle);



// // New things per participant, based on category
// $output = "";
// for($i = 0; $i < count($p); $i++)
// {
// 	$output .= $p[$i]->condition . "," . count($p[$i]->new) . "\n";
// }

// $handle = fopen("num_new_things.csv", "w");
// fwrite($handle, $output);
// fclose($handle);

?>