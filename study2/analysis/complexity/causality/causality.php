<html> 
	<head>
		<link href='../../../css/bootstrap.css' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="../../../lib/jquery-2.0.0.js"></script>
	<?php

require_once "../../../lib/database.php";
require_once "../../../lib/build_participants.php";

$ps = $participants;

function cmp($a, $b)
{
	return $a->id > $b->id;
}
usort($ps, "cmp");

$p = isset($_GET["p"]) ? $_GET["p"] : -1;
$l = isset($_GET["l"]) ? $_GET["l"] : -1;
$r = isset($_GET["r"]) ? $_GET["r"] : -1;

echo "<script type='text/javascript'>var ps = " . json_encode($ps) . ";
	var pid = $p
	var leftIndex = $l;
	var rightIndex = $r;</script>";

?>
<script type="text/javascript">
	
	function go() {

		for(var i = 0; i < ps.length; i++) {

			var p = ps[i];
			var all = new Array();

			for(var j = 0; j < p.customs.length; j++) {

				var add = true;
				for(var k = 0; k < p.split.length; k++) {

					if(p.split[k].other == p.customs[j].id) {
						// Skip this one
						add = false;
						break;
					}
				}
				
				if(add)
					all.push(p.customs[j]);
			}

			for(var j = 0; j < p.split.length; j++) {
				all.push(p.split[j]);
			}

			for(var j = 0; j < p.things.length; j++) {
				all.push(p.things[j]);
			}

			p.all = all;
		}

		if(pid != -1 && leftIndex != -1 && rightIndex != -1) {
			populate(pid, leftIndex, rightIndex);
			return;
		} 

		$.ajax({
			url: "causality_getnext.php",
		}).done(function(msg){

			eval(msg);

			populate(nextPid, 0, 1);

		}).fail(function ( jqXHR, textStatus, errorThrown ){ alert("Fail " + textStatus + ", " + errorThrown)});
	}

	function nope() { 
		populate(pid, leftIndex, rightIndex + 1);
	}

	function previous() {
		if(rightIndex > leftIndex + 1)
			populate(pid, leftIndex, rightIndex - 1);
		else
			populate(pid, leftIndex - 1, ps[pid].all.length - 1);
	}

	function populate(nextPid, left, right) {

		pid = nextPid;
		$("#pid").html(pid + "[" + ps[pid].all.length + "]");

		var leftName = $("#left_name");
		var leftDescription = $("#left_description");
		var leftRationale = $("#left_rationale");
		var rightName = $("#right_name");
		var rightDescription = $("#right_description");
		var rightRationale = $("#right_rationale");

		if(right < ps[pid].all.length) { // Both must be lower
			leftIndex = left;
			rightIndex = right;
		} else { // Right needs to roll over
			if(left < ps[pid].all.length - 2) { // Left needs to go up
				leftIndex = left + 1;
				rightIndex = leftIndex + 1;
			} else { // We're done with this participant
				setDone(pid);
				return;
			}
		}


		var l = ps[pid].all[leftIndex];
		var r = ps[pid].all[rightIndex];

		leftName.html("[" + (leftIndex + 1) + "] " + l.name + " (" + l.type + "[" + l.id + "] - " + l.other + ")");
		leftDescription.html(l.description);
		leftRationale.html(l.rationale);

		rightName.html("[" + (rightIndex + 1) + "] " + r.name + " (" + r.type + "[" + r.id + "] - " + r.other + ")");
		rightDescription.html(r.description);
		rightRationale.html(r.rationale);
	}

	function setDone(pid) {
		$.ajax({
			url: "causality_set.php",
			type: "get",
			data: {
				done: pid
			}
		}).done(function(msg){

			if(msg == "win") {
				populate(pid + 1, 0, 1);
			} else {
				alert(msg);
			}	

		}).fail(function ( jqXHR, textStatus, errorThrown ){ alert("Fail " + textStatus + ", " + errorThrown)});

	}

	function setAbstract(value) {

		var cause = null;
		var effect = null;

		if(value == -1) {
			cause = ps[pid].all[leftIndex];
			effect = ps[pid].all[rightIndex];
		} else {
			cause = ps[pid].all[rightIndex];
			effect = ps[pid].all[leftIndex];
		}

		var bijective = value == 0 ? 1 : 0;

		$.ajax({
			url: "causality_set.php",
			type: "get",
			data: {
				p: pid,
				cause_id: cause.id,
				cause_type: cause.type,
				effect_id: effect.id,
				effect_type: effect.type,
				b: bijective,
				add: true
			}
		}).done(function(msg){

			if(msg == "win") {
				nope();
			} else {
				alert(msg);
			}	

		}).fail(function ( jqXHR, textStatus, errorThrown ){ alert("Fail " + textStatus + ", " + errorThrown)});

	}

	function key(event) {
		// alert(event.keyCode);

		switch(event.keyCode) {
			case 32: // Space
				nope();
				break;

			case 37: // Left arrow, left button
				setAbstract(-1);
				break;

			case 40: // Down, bijective
				setAbstract(0);
				break;

			case 39:
				setAbstract(1);
				break;

			case 81:
				previous();
				break;
		}
	}

</script>
</head>
<body onload="go();" onkeydown="key(event)">
	<div style='position:relative;height:100%;margin-top:0px;margin-bottom:0px;width:96%;margin-left:2%;margin-right:2%;'>
		<div style="height:100%;width:40%;position:absolute;top:0px;left:0px;margin:0;overflow:scroll;">
			<h4 id="left_name">Name</h4>
			<p id="left_description">Description</p>
			<p id="left_rationale">Rationale</p>
		</div>
		<div style="height:100%;width:60%;position:absolute;top:0px;left:40%;margin:0;">
			<div style="position:relative;with:100%;height:5%;">
				<b><span id="pid">PID</span></b>
				<button onclick="setAbstract(-1)">A ==&gt; B</button>
				<button onclick="setAbstract(0)">A &lt;==&gt; B</button>
				<button onclick="setAbstract(1)">A &lt;== B</button>
				<button onclick="nope()">Nope</button>
			</div>
			<div style="position:relative;with:100%;height:95%;overflow:scroll;">
				<h4 id="right_name">Name</h4>
				<p id="right_description">Description</p>
				<p id="right_rationale">Rationale</p>
			</div>
		</div>
	</div>
</body>
</html>