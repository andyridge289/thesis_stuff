<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../../../css/bootstrap.css" />
		<style type="text/css">
		ul
		{
			margin: 0;
			padding: 0;
		}

		li
		{
			list-style: none;
			margin: 0;
			padding: 10px;
			width: 95%;
			border-bottom: 1px solid #AAA;
		}

		li.result
		{
			cursor: pointer;
		}

		li.result:hover
		{
			background-color: #eee;
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

		var results = [];
		var thing = null;

		function addNew(id, type)
		{
			var newThing = $("#entry_" + type + "_" + id).val();

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "add new " + type,
			 		new_thing: newThing, 
			 		custom_id: id
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					location.reload();
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
				// alert(msg);
			});
		}

		function lookup()
		{
			$.ajax({
				url: "lookup.php",
				type: "post",
			 	data: { 

			 	}
			}).done(function( msg ){
				eval(msg);
			});
		}

		function filter(event, type, id)
		{
			var name = $(event.target).val();
			var newStuff = [];

			for(var i = 0; i < results.length; i++)
			{
				if(results[i].name.toLowerCase().indexOf(name.toLowerCase()) != -1)
					newStuff.push(results[i]);
			}

			var container = $("#results_" + type + "_" + id);
			container.empty();

			var resultsList = $("<ul id='ul_results'></ul>");
			
			for(var i = 0; i < newStuff.length; i++)
			{
				var result = $("<li id='li" + newStuff[i].id + "' onclick='choose(this, \"" + type + "\", " + id + ")' class='result'>" + newStuff[i].name + "</li>");
				result.attr({ "thing_id": newStuff[i].id });
				resultsList.append(result);
			}

			container.append(resultsList);
		}

		function choose(thing, type, id)
		{
			thing = $(thing);

			// Set the text box to have the right text and give it the thing_id of this one
			$("#new_entry_" + type + "_" + id).val(thing.html());
			$("#new_entry_" + type + "_" + id).attr({ "thing_id": thing.attr("thing_id") });
			$("#results_" + type + "_" + id).empty();
		}

		function set(customId, type)
		{
			var thingId = $("#new_entry_" + type + "_" + customId).attr("thing_id");
			
			// var thingId = ($("#entry").attr("thing_id"));
			
			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "link new " + type,
			 		custom_id: customId,
					thing_id: thingId
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					location.reload();
				}
				else
				{
			// 		// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function done(customId, type)
		{	
			$.ajax({

				url: "set_item.php",
				type: "post",
			 	data: {
			 		what: "done " + type,
					custom: customId,
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					/*var row = $("#" + type + "_" + customId);
					row.fadeOut(1000, function(){
						row.remove();
					});

					var count = $("#" + type + "_num").html() * 1;
					$("#" + type + "_num").html("" + (count - 1));*/
					location.reload();
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function unverify(customId, isSplit)
		{
			var whatThing = isSplit ? "unverify split" : "unverify";

			$.ajax({

				url: "set_item.php",
				type: "post",
			 	data: {
			 		what: whatThing,
					custom_id: customId,
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					location.reload();
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		</script>
	</head>
	<body onload="lookup()">
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
		WHERE pgc.done_new = 0 AND pgc.done_verify = 1 AND pc.split = 1 AND pc.ignore_custom = 0
		ORDER BY participant_id";
		
$ret = $db->q($q);
if(!$ret){ echo "Fail: $q<br />"; return; }
$num = mysqli_num_rows($ret);

echo "<h3>Custom</h3><h4><span id='custom_num'>$num</span> to go</h4><ul id='custom_list'>";


while($r = mysqli_fetch_array($ret))
{
	// And now start the new one
	echo "<li id='custom_$r[id]'>
			<div style='margin-bottom:20px;position:relative;'>
				<button class='label label-inverse' onclick='done($r[id], \"custom\")'>Done</button>
				<span style='font-weight:bold;margin-left:10px;''>$r[id]: $r[name]</span>
				<span class='label label-inverse' onclick='unverify($r[id], false)' style='position:absolute;right:0px;top:0px'>Unverify</span>
				<div><b>Description:&nbsp;</b>$r[description]</div>
				<div><b>Rationale:&nbsp;</b>$r[rationale]</div>
				<div>";

	$q2 = "SELECT * FROM `custom_is_thing` AS ct
			LEFT JOIN `thing` AS t ON ct.thing_id = t.id
			WHERE ct.custom_id = $r[id]";
	$ret2 = $db->q($q2);
	while($r2 = mysqli_fetch_array($ret2))
	{
		// print_r($r2);
		$textColor = $r2["type"] == "option" ? "#000000" : "#FFFFFF";
		$bgColor = $r2["type"] == "decision" ? "#000080" : "#FFFFFF";
		if($r2["type"] == "category")
			$bgColor = "#54A854";

		echo "<span class='label' style='margin:2px;color:$textColor;background-color:$bgColor;border: 1px solid $textColor'>$r2[name]</span>";
	}

	$q2 = "SELECT * FROM `custom_has_new` AS cn
			LEFT JOIN `thing_new` AS tn ON cn.new_id = tn.id
			WHERE cn.custom_id = $r[id]";
	$ret2 = $db->q($q2);
	while($r2 = mysqli_fetch_array($ret2))
	{
		echo "<span class='label' style='margin:2px;color:#FFF;background-color:#F00;border: 1px solid #FFF'>$r2[name]</span>";
	}

			echo "</div>
			<table>
			<tr>
				<td style='width:50%'>
					<div id=''>
						<input id='new_entry_custom_$r[id]' type='text' style='width:80%;' onkeyup='filter(event, \"custom\", $r[id])'></input>
						<button onclick='set($r[id], \"custom\")'>Set</button>
						<div id='results_custom_$r[id]' style='width:80%;height:200px;max-height:200px;overflow:scroll;'></div>
					</div>
				</td>
				<td style='width:50%;'>
					<input id='entry_custom_$r[id]' type='text'></input>
					<button onclick='addNew($r[id], \"custom\")'>Add</button>
				</td>
			</tr>
			</table>
		</div>
	</li>";
}

$q = "SELECT pcs.split_id AS id, sc.name AS name, sc.description AS description, sc.rationale AS rationale
	FROM `participant_has_custom` AS pc
	LEFT JOIN `split_custom` AS sc ON pc.id = sc.old_id
	LEFT JOIN `progress_check_split` AS pcs ON sc.id = pcs.split_id
	WHERE pc.split = 3 AND pc.ignore_custom = 0 AND pcs.done_new = 0 AND pcs.done_verify = 1";

$ret = $db->q($q);
if(!$ret){ echo "Fail: $q<br />"; return; }
$num = mysqli_num_rows($ret);

echo "</ul><h3>Split</h3><h4><span id='split_num'>$num</span> to go</h4><ul id='split_list'>";

while($r = mysqli_fetch_array($ret))
{
	// And now start the new one
	echo "<li id='split_$r[id]'>
			<div style='margin-bottom:20px;position:relative;'>
				<button class='label label-inverse' onclick='done($r[id], \"split\")'>Done</button>
				<span style='font-weight:bold;margin-left:10px;''>$r[id]: $r[name]</span>
				<span class='label label-inverse' onclick='unverify($r[id], true)' style='position:absolute;right:0px;top:0px'>Unverify</span>
				<div><b>Description:&nbsp;</b>$r[description]</div>
				<div><b>Rationale:&nbsp;</b>$r[rationale]</div>
				<div>";

	$q2 = "SELECT * FROM `split_is_thing` AS ct
	 		LEFT JOIN `thing` AS t ON ct.thing_id = t.id
	 		WHERE ct.split_id = $r[id]";
	$ret2 = $db->q($q2);
	while($r2 = mysqli_fetch_array($ret2))
	{
		// print_r($r2);
		$textColor = $r2["type"] == "option" ? "#000000" : "#FFFFFF";
		$bgColor = $r2["type"] == "decision" ? "#000080" : "#FFFFFF";
		if($r2["type"] == "category")
			$bgColor = "#54A854";

		echo "<span class='label' style='margin:2px;color:$textColor;background-color:$bgColor;border: 1px solid $textColor'>$r2[name]</span>";
	}

	$q2 = "SELECT * FROM `split_has_new` AS cn
			LEFT JOIN `thing_new` AS tn ON cn.new_id = tn.id
			WHERE cn.split_id = $r[id]";
	$ret2 = $db->q($q2);
	while($r2 = mysqli_fetch_array($ret2))
	{
		echo "<span class='label' style='margin:2px;color:#FFF;background-color:#F00;border: 1px solid #FFF'>$r2[name]</span>";
	}

			echo "</div>
			<table>
			<tr>
				<td style='width:50%'>
					<div id=''>
						<input id='new_entry_split_$r[id]' type='text' style='width:80%;' onkeyup='filter(event, \"split\", $r[id])'></input>
						<button onclick='set($r[id], \"split\")'>Set</button>
						<div id='results_split_$r[id]' style='width:80%;height:200px;max-height:200px;overflow:scroll;'></div>
					</div>
				</td>
				<td style='width:50%;'>
					<input id='entry_split_$r[id]' type='text'></input>
					<button onclick='addNew($r[id], \"split\")'>Add</button>
				</td>
			</tr>
			</table>
		</div>
	</li>";
}

?>
	</ul>
	</body>
</html>