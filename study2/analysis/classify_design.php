<?php

echo "<html><head>";

require_once "../lib/database.php";

$names = array("Functional", "Non-Functional","Structural","Entity");
$things = array(array(), array(), array(), array());
$q = "SELECT * FROM `sub_classification`";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail $q";
	return;
}

while($r = mysqli_fetch_array($ret)) {
	array_push($things[$r["ds"] - 1], new Pair($r["id"], $r["name"], "", ""));
}
echo "<script type='text/javascript'>var names = " . json_encode($names) . ";\nvar classifiers = " . json_encode($things) . ";</script>";

// Check for the ones that've already been classified
$q = "SELECT * FROM `classified_custom`";
$ret = $db->q($q);
if(!$ret){ echo "Fail $q"; return; }
$done_customs = array();
$ret = $db->q($q);
while($r = mysqli_fetch_array($ret)) {
	array_push($done_customs, $r["custom_id"]);
}
 
// CUSTOMS
$q = "SELECT * FROM `participant_has_custom` WHERE ignore_custom = 0 AND split = 1";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail $q";
	return;
}
$customs = array();
while($r = mysqli_fetch_array($ret)) {

	if(in_array($r["id"], $done_customs))
		continue;

	$p = new Pair($r["id"], $r["name"], $r["description"], $r["rationale"]);
	$p->participant = $r["participant_id"];
	array_push($customs, $p);
}
echo "<script type='text/javascript'>var customs = " . json_encode($customs) . ";</script>";


// SPLIT CUSTOMS



class Pair 
{
	public $id;
	public $name;
	public $description;
	public $rationale;

	public $participant;

	function Pair($id, $name, $description, $rationale) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->rationale = $rationale;
	}
}

?>
	
	<script type="text/javascript" src="../lib/jquery-2.0.0.js"></script>
	<script type="text/javascript">

		var currentIndex = 0;

		function go() {

			// Set up the controls
			var controls = $("#controls");
			var tableString = "<table style='width:100%;'><tr>";
			for(var i = 0; i < names.length; i++)
				tableString += "<td style='text-align:center;'>" + names[i] + "</td>";

			tableString += "</tr>";

			var row = 0;

			while(true) {

				var stop = true;
				var thisRow = "";
				for(var i = 0; i < classifiers.length; i++) {

					if(classifiers[i].length > row) {
						thisRow += "<td><button style='width:100%;' id='" + classifiers[i][row].id +
								"' onclick='clickButton(event)'>" + classifiers[i][row].name + "</button></td>";
						stop = false;
					} else {
						thisRow += "<td></td>";
					}

				}

				if(stop)
					break;

				tableString += "<tr>" + thisRow + "</tr>";
				row++;
			}
			controls.append($(tableString));

			// Set up the contents of the main thing
			loadIndex(0);
		}

		function loadIndex(newIndex) {
			currentIndex = newIndex;
			var current = customs[currentIndex];

			$("#thing_name").html(current.name);
			$("#participant").html(current.participant);
			$("#thing_description").html(current.description);
			$("#thing_rationale").html(current.rationale);
		}

		function clickButton(event) {
			var id = event.target.id;

			$.ajax({
				url: "classify.php",
				type: "post",
				data: 
				{
					custom: customs[currentIndex].id,
					classifier: id
				}
			}).done(function(msg){

				// console.log(msg);
				msg = "win";

				if(msg == "win") {
					customs.splice(0, 1);
					loadIndex(0);
				}

			}).fail(function ( jqXHR, textStatus, errorThrown ){ alert("Fail " + textStatus + ", " + errorThrown)});

		}

	</script>
	<link href="../css/bootstrap.css" rel="stylesheet"/>
</head>
<body onload="go()">

	<div id="content">
		<div id="thing" style="margin-bottom:50px;">
			<h4 style="width:100%;text-align:center;margin-top:50px;" id="thing_name"></h4>
			<h4 style="width:100%;text-align:center;margin-top:10px;" id="participant"></h4>
			<div id="thing_description" style="width:100%;text-align:center;"></div>
			<div id="thing_rationale" style="width:100%;text-align:center;"></div>
		</div>
		<div id="controls">
		</div>
	</div>

</body>
</html>