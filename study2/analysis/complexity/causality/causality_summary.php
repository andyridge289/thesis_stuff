<?php

require_once "../../../lib/build_participants.php";

for($i = 0; $i < count($participants); $i++) {
	$p = $participants[$i];
	echo "$p->id: " . count($p->causal) . "<br />"; 
}