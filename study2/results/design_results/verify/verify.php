<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../../../css/bootstrap.css" />
		<style type="text/css">
		ul
		{
			margin: 0;
			padding: 0;
			overflow: scroll;
		}

		li
		{
			list-style: none;
			margin: 0;
			padding: 10px;
			width: 95%;
			border-bottom: 1px solid #AAA;
		}

		.bob
		{
			cursor: pointer;
		}

		.bob:hover
		{
			background-color: #eee;
		}

		.bob:active
		{
			background-color: #aaa;
		} 

		.label
		{
			cursor: pointer;
		}

		.label-success:hover
		{
			background-color: green;
		}

		.label-important:hover
		{
			background-color: red;
		}

		.label-inverse
		{
			border: 1px solid #FFF;
		}

		.label-inverse:hover
		{
			background-color: white;
			color: black;
			border: 1px solid #000;
		}

		.matching-label:hover
		{
			background-color: #666;
		}

		table, tr, input
		{
			width: 100%;
		}

		</style>
		<script type="text/javascript" src="../../../lib/jquery-2.0.0.js" ></script>
		<script type="text/javascript">

		function getAll()
		{
			var lis = $("li");
			for(var i = 0; i < lis.length; i++)
			{
				var customID = lis[i].id.substring(7);
				getForIndex(customID);
			}
		}

		function getForIndex(customID)
		{
			$.ajax({
				url: "lookup.php",
				type: "post",
			 	data: { 
			 		customId: customID
			 	}
			}).done(function( msg ){

				try
				{
					eval(msg);
				}
				catch(e)
				{
					$("#custom_container" + customID).html("Fail: " + customID);
					return;
				}

				var id = data.custom_id;

				var verifiedBox = $("#verified" + id);
				verifiedBox.empty();

				var thingBox = $("#custom_container" + id);
				thingBox.empty();

				var matchBox = $("#potential" + id);
				matchBox.empty();

				for(var i = 0; i < data.verified.length; i++)
				{
					var thing = data.verified[i];
					var textColor = thing.type == "category" ? "#54A854" : thing.type == "decision" ? "#000080" : "#FFFFFF";
					var bgColor = thing.stageRemoved == -1 ? "#3a87ad" : "#f00";
					var div = $("<span class='label' onclick='unVerify(" + thing.id + "," + customID + ")' style='margin:2px;color:" + textColor + ";background-color:" + bgColor + "'>" + thing.name + "</span>");
					verifiedBox.append(div);
				}

				for(var i = 0; i < data.unverified.length; i++)
				{
					var thing = data.unverified[i];

					var okButton = $("<span class='label label-success' style='margin-left:20px;width:10%;' onclick='validate(" + thing.relation + "," + id + ", " + thing.id + ")'>Okay</span>");
					var notOkButton = $("<span class='label label-important' style='width:10%;margin-left:2px;' onclick='unvalidate(" + thing.relation + "," + id + ", " + thing.id + ")'>Not okay</span>");
					
					var textColor = thing.stageRemoved != -1 ? "#f00" : thing.type == "category" ? "#54A854" : thing.type == "decision" ? "#000080" : "#000000";
					var div = $("<div id='thing" + id + "_" + thing.id + "' style='margin-left:20px;color:" + textColor + ";'>" + thing.name + "</div>");
					

					div.append(okButton);
					div.append(notOkButton);
					thingBox.append(div);
				}

				for(var i = 0; i < data.matches.length; i++)
				{
					var thing = data.matches[i];

					var className = thing.verified ? "label matching-label label-info" : "label matching-label";
					var doClick = thing.verified ? "doNothing()" : "newMatch(" + id + ", " + thing.id + ")";

					var textColor = thing.type == "category" ? "#54A854" : thing.type == "decision" ? "#000080" : "#FFFFFF";
					var div = $("<span class='" + className + "' style='margin:2px;color:" + textColor + "' onclick='" + doClick + "'>" + thing.name + "</span>");
					matchBox.append(div);
				}
			});
		}

		function unVerify(thingId, customId)
		{
			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "unverify",
					custom: customId, 
					thing: thingId
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					getForIndex(customId);
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function doNothing()
		{
			// Does nothing
		}

		function validate(id, customId, thingId)
		{

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "verify",
			 		id: id,
					custom: customId, 
					thing: thingId
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					getForIndex(customId);
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function unvalidate(id, customId, thingId)
		{
			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "not option",
			 		id: id,
					custom: customId, 
					thing: thingId
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					getForIndex(customId);
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function newMatch(customId, newId, clicked)
		{
			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "new match",
					custom: customId, 
					thing: newId
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					getForIndex(customId);
					// TODO Do another lookup
					// $(clicked).attr({ "class": "label label-info"});
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function done(customId)
		{	
			var container = $("#custom_container" + customId);
			
			if(container.children().length > 0)
			{
				alert("There are still children.")
				return;
			}

			$.ajax({

				url: "set_item.php",
				type: "post",
			 	data: {
			 		what: "done",
					custom: customId,
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					var row = $("#custom_" + customId);
					row.fadeOut(1000, function(){
						row.remove();
					});

					var count = $("#num").html() * 1;
					$("#num").html("" + (count - 1));
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function notDone(customId)
		{	
			var container = $("#custom_container" + customId);
			
			if(container.children().length > 0)
			{
				alert("There are still children.")
				return;
			}

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: {
			 		what: "undone",
					custom: customId,
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					var row = $("#custom_" + customId);
					row.fadeOut(1000, function(){
						row.remove();
					});

					var count = $("#num").html() * 1;
					$("#num").html("" + (count - 1));
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
			// alert("Not done " + customId);
		}

		</script>
	</head>
	<body onload=getAll()>
		<ul id="list">
<?php

require_once "../../../lib/database.php";

$things = array();
$q = "SELECT * FROM `thing`";
$ret = $db->q($q);
if(!$ret){ echo "Fail: $q<br />"; return; }
while($r = mysqli_fetch_array($ret))
{
	$id = $r["id"];
	$things[$id] = $r;
}

$matrix = array();
$q = "SELECT * FROM `custom_is_thing`";
$ret = $db->q($q);
if(!$ret){ echo "Fail: $q<br />"; return; }
while($r = mysqli_fetch_array($ret))
{
	if(!array_key_exists($r["custom_id"], $matrix))
	{
		$matrix[$r["custom_id"]] = array($r["thing_id"]);
	}
	else
	{
		array_push($matrix[$r["custom_id"]], $r["thing_id"]);
	}
}

$q = "SELECT pc.id AS id, pc.name AS name, pc.description AS description, pc.rationale AS rationale
		FROM `participant_has_custom` AS pc
		LEFT JOIN `progress_check` AS pgc ON pgc.custom_id = pc.id
		WHERE pgc.done_verify = 0 AND pc.split = 1 AND pc.ignore_custom = 0
		ORDER BY participant_id";
		
$ret = $db->q($q);
if(!$ret){ echo "Fail: $q<br />"; return; }

$customs = array();
$last;
$lastId = -1;

$num = mysqli_num_rows($ret);

echo "<h4><span id='num'>$num</span> to go</h4>";


while($r = mysqli_fetch_array($ret))
{
	// And now start the new one
		echo "<li id='custom_$r[id]'>
				<div style='margin-bottom:20px;position:relative;'>
					<button class='label label-inverse' onclick='done($r[id])'>Done</button>
					<span style='font-weight:bold;margin-left:10px;''>$r[id]: $r[name]</span>
					<span class='label label-inverse' onclick='notDone($r[id])' style='position:absolute;right:0px;top:0px'>Not done!</span>
					<div><b>Description:&nbsp;</b>$r[description]</div>
					<div><b>Rationale:&nbsp;</b>$r[rationale]</div>
					<div id='verified$r[id]'></div>
				</div>
				<div id='custom_container$r[id]'></div>
				<div style='font-weight:bold;padding-top:20px;'>Potential matches</div>
				<div id='potential$r[id]'></div>
			</li>";
}

// And now do the ones that aren't right

// $q = "SELECT ct.custom_id AS custom_id
// 	FROM  `custom_is_thing` AS ct
// 	LEFT JOIN  `thing` AS t ON ct.thing_id = t.id
// 	LEFT JOIN `participant_has_custom` AS pc on ct.custom_id = pc.id
// 	WHERE t.stage_removed != -1";

// $ret = $db->q($q);
// if(!$ret){ echo "Fail: $q<br />"; return; }

// while($r = mysqli_fetch_array($ret))
// {
// 	// Unverify the thing so it appears next time, and make it appear in the other page too
// 	$q1 = "UPDATE progress_check SET done_verify = 0, done_option = 0 WHERE custom_id = $r[custom_id]";
// 	$ret1 = $db->q($q1);
// 	if(!$ret1)
// 	{
// 		echo "Fail $q1<br />";
// 	}
// }



?>
	</ul>
	</body>
</html>