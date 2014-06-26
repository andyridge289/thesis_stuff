<html> 
	<head>
		<link href='../../css/bootstrap.css' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="../../lib/jquery-2.0.0.js"></script>
	<?php

require_once "../../lib/database.php";
require_once "../../lib/build_participants.php";

$ps = $participants;

function cmp($a, $b)
{
	return $a->id > $b->id;
}
usort($ps, "cmp");

echo "<script type='text/javascript'>var ps = " . json_encode($ps) . ";</script>";

// for($i = 0; $i < count($ps); $i++)
// {
	// $p = $ps[$i];

	// echo "<h4>$p->id ($p->condition)</h4><p><i>Customs:</i></p>";
	// if(count($p->customs) == 0) {
	// 	echo "None";
	// } else  {
	// 	for($j = 0; $j < count($p->customs); $j++)
	// 	{
	// 		$c = $p->customs[$j];

	// 		echo "<p><b>[" . $c->id . "]:  " . $c->name . "</b>";
	// 		echo "<br />" . $c->description . "</p>";
	// 	}
	// }

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

	// echo "<p><i>Things</i></p>";
	// if(count($p->things) == 0) {
	// 	echo "None";
	// } else {
	// 	for($j = 0; $j< count($p->things); $j++)
	// 	{
	// 		$c = $p->things[$j];
	// 		echo "<p><b>[" . $c->id . "]:  " . $c->name . "</b><br />";
	// 		echo $c->description . "</p>";
	// 		// echo "<span class='label label-info' style='margin-right:5px;margin-top:2px;'>" . $p->things[$j]->name . "</span>";
	// 	}
	// }	

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
// }

?>
<script type="text/javascript">

	var leftPid = -1;
	var rightPid = -1;
	
	function go() {
		$.ajax({
			url: "abstract_getnext.php",
		}).done(function(msg){

			eval(msg);

			populate(startA, startB);

		}).fail(function ( jqXHR, textStatus, errorThrown ){ alert("Fail " + textStatus + ", " + errorThrown)});
	}

	function populate(a, b) {

		if(a == 0 && b == 0) {
			// Need to start at participant number 1, and then 2
			a = 1;
			b = a + 1;
		}

		if(b > 41) {
			a++;
			b = a + 1;
		}

		if(a == 41) {
			alert("Done!");
			return;
		}

		// The indexes in the array are one less than the number we want (at least until we get to 34)
		var left = a <= 34 ? ps[a - 1] : ps[a - 2];
		var right = b <= 34 ? ps[b - 1] : ps[b - 2];

		if(left.id == right.id)
			right = ps[b - 1];

		populateSide("left", left);
		populateSide("right", right);
	}

	function populateSide(side, thing) {
		var pid = $("#" + side + "_pid");
		var customs = $("#" + side + "_customs");
		var things = $("#" + side + "_things");
		var customH = $("#" + side + "_ch4");
		var thingH = $("#" + side + "_th4");

		if(side == "left") {
			leftPid = thing.id;
		} else {
			rightPid = thing.id;
		}

		pid.html(thing.id + " (" + thing.condition + ")&nbsp;&nbsp;&nbsp;&nbsp;[" + (thing.customs.length + thing.things.length) + "]");
		customH.html("Customs [" + thing.customs.length + "]");
		thingH.html("Things [" + thing.things.length + "]");

		customs.empty();
		for(var i = 0; i < thing.customs.length; i++) {
			var li = $("<li><b>" + thing.customs[i].name + "</b><br />" + thing.customs[i].description + "</li>");
			customs.append(li);
		}

		things.empty();
		for(var i = 0; i < thing.things.length; i++) {
			var li = $("<li><b>" + thing.things[i].name + "</b><br />" + thing.things[i].description + "</li>");
			things.append(li);
		}
	}

	function setAbstract(value) {

		if(leftPid == -1 || rightPid == -1) {
			return;
		}

		$.ajax({
			url: "abstract_set.php",
			type: "get",
			data: {
				a: leftPid,
				b: rightPid,
				a_b: value
			}
		}).done(function(msg){

			if(msg == "win") {
				populate(leftPid, (rightPid * 1) + 1);
			} else {
				alert(msg);
			}	

		}).fail(function ( jqXHR, textStatus, errorThrown ){ alert("Fail " + textStatus + ", " + errorThrown)});

	}

</script>
</head>
<body onload="go();">
	<div style='position:relative;height:100%;margin-top:0px;margin-bottom:0px;width:96%;margin-left:2%;margin-right:2%;'>
		<div style="height:100%;width:40%;position:absolute;top:0px;left:0px;margin:0;overflow:scroll;">
			<h1 id="left_pid">PID</h1>
			<h4 id="left_ch4">Customs</h4>
			<ul id="left_customs"></ul>
			<h4 id="left_th4">Things</h4>
			<ul id="left_things"></ul>
		</div>
		<div style="height:100%;width:60%;position:absolute;top:0px;left:40%;margin:0;">
			<div style="position:relative;with:100%;height:5%;">
				<button onclick="setAbstract(-1)">Left more Abstract</button>
				<button onclick="setAbstract(0)">Same</button>
				<button onclick="setAbstract(1)">Left more Concrete</button>
			</div>
			<div style="position:relative;with:100%;height:95%;overflow:scroll;">
				<h1 id="right_pid">PID</h1>
				<h4 id="right_ch4">Customs</h4>
				<ul id="right_customs"></ul>
				<h4 id="right_th4">Things</h4>
				<ul id="right_things"></ul>
			</div>
		</div>
	</div>
</body>
</html>