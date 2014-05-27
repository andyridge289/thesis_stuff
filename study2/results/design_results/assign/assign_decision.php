<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
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
			padding: 0;
			width: 100%;
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

		table, tr, input
		{
			width: 100%;
		}

		</style>
		<script type="text/javascript" src="../lib/jquery-2.0.0.js" ></script>
		
		<?php

			require_once "../../lib/database.php";
			$q = "SELECT * FROM `thing` WHERE type = 'decision' AND stage_removed = -1";
			$ret = $db->q($q);
			if(!$ret)
			{
				echo "Fail: $q<br />";
			}
			$options = "<script type='text/javascript'>var options = [";
			while($r = mysqli_fetch_array($ret))
			{
				$options .= "{id:'$r[id]',name:\"" . trim(addslashes($r["name"])) . "\"},\n";
			}
			$options = substr($options, 0, strlen($options) - 1) . "];</script>";
			echo $options;

		?>
		<script type="text/javascript">
		
		function addOption(button)
		{
			var id = $(button).attr("id").substring(9);
			var addingRow = $("#adding_row" + id);

			addingRow.css({ "display": "block" });
			addingRow.empty();

			var contents = $("<div id='fred" + id + "'>" +
				"<input id='entry" + id + "' type='text' style='width:50%;' onkeyup='filter(event)'></input>" +
				"<button id='addoption" + id + "' onclick='submit(this)'>Set</button>" +
				"<div id='results" + id + "' style='width:50%;height:200px;overflow:scroll'>" +
			"</div>" +
		"</div>");

			addingRow.append(contents);
		}
		
		function submit(button)
		{
			var id = $(button).attr("id").substring(9);
			var thingId = ($("#entry" + id).attr("thing_id"));

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "decision",
			 		custom: id,
					decision: thingId
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					$("#adding_row" + id).fadeOut(1000, function(){
						$("#adding_row" + id).empty();
						$("#adding_row" + id).css({ "display": "none" });
					});
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function notOption(button)
		{
			var id = $(button).attr("id").substring(9);
			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "not decision",
			 		custom: id,
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					done(button);
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function done(button)
		{
			var id = $(button).attr("id").substring(9);

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "done decision",
			 		custom: id,
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					$("#contents" + id).fadeOut(1000, function(){
						$("#contents").css({ "display": "none" });
					});

					var count = $("#count").html() * 1;
					$("#count").html("" + (count - 1));
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
			});
		}

		function filter(event)
		{
			var input = $(event.target).val();
			var id = $(event.target).attr("id").substring(5);
			var results = [];

			for(var i = 0; i < options.length; i++)
			{
				if(options[i].name.toLowerCase().indexOf(input.toLowerCase()) != -1)
				{
					results.push(options[i]);
				}
			}

			var resultsContainer = $('#results' + id);
			resultsContainer.empty();
			var resultsList = $("<ul id='ul_results" + id + "'></ul>");
			for(var i = 0; i < results.length; i++)
			{
				var result = $("<li class='bob' thing_id='" + results[i].id + "' custom_id='" + id + "' onclick='choose(this)'>" + results[i].name + "</li>");
				// result.attr({ "thing_id": results[i].id, "custom_id": id });
				resultsList.append(result);
			}

			resultsContainer.append(resultsList);
		}

		function choose(thing)
		{
			thing = $(thing);
			var thingId = thing.attr("thing_id")
			var customId = thing.attr("custom_id");

			// Set the text box to have the right text and give it the thing_id of this one
			var entry = $("#entry" + customId)
			entry.val(thing.html());
			entry.attr({ "thing_id": thingId });
			
			// var customId = thing.attr("custom_id");
			$("#results" + customId).empty();
		}
		</script>
	</head>
	<body>
<?php

require_once "../lib/database.php";

$q = "SELECT * FROM `participant_has_custom` WHERE split = 1 AND ignore_custom = 0 ORDER BY participant_id";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

$customs = array();
while($r = mysqli_fetch_array($ret))
{
	$q2 = "SELECT * FROM `progress_check` WHERE custom_id = $r[id]";
	$ret2 = $db->q($q2);
	if(!$ret2)
	{
		echo "Fail: $q2<br />";
		continue;
	}

	$num = mysqli_num_rows($ret2);
	if($num == 0)
	{
		// If there's no entry it can't have been done yet
		array_push($customs, $r);
		continue;
	}	

	$r2 = mysqli_fetch_array($ret2);
	if($r2["done_decision"] == 1) // If the entry is 1 it's been done, move on.
		continue;

	$q2 = "SELECT * FROM `custom_is_option` WHERE custom_id = $r[id]";
	$ret2 = $db->q($q2);
	if(!$ret2)
	{
		echo "Fail: $q2<br />";
		continue;
	}

	$r2 = mysqli_fetch_array($ret2);
	if($r2["option_id"] != -1)
		continue;

	array_push($customs, $r);
}

$num = count($customs);

echo "<div style='width:100%;text-align:center'><b><span id='count'>$num</span> to assign</b></div><ul>";

$pid = 0;
for($i = 0; $i < count($customs); $i++)
{
	$r = $customs[$i];
	if($r["participant_id"] !== $pid)
	{
		if($pid !== 0)
			echo "</li >";
		
		$pid = $r["participant_id"];
		echo "<br /><li><div style='width:100%;'><div style='font-weight:bold;width:100%;text-align:center;'>P$pid</div>";
	}

	echo "<div id='contents$r[id]'>
		<div><b>$r[name]</b></div>
		<div>$r[description]<br /><br />$r[rationale]</div>
		<button id='addoption$r[id]' onclick='addOption(this)' style='margin-right:10px;'>Add</button>
		<button id='notoption$r[id]' type='button' onclick='done(this)'  style='margin-right:10px;'>Done</button>
		<button id='notoption$r[id]' type='button' onclick='notOption(this)' style='margin-right:20px;'>Not decision</button><br />
		<div id='adding_row$r[id]'></div>
	</div>";
}

echo "</div>";

function tableCombo($tableName, $customId)
{
	global $db;
	
	// return $options;

	$str = "<div id='fred'>
		<input id='entry' type='text' style='width:50%;' onkeyup='filter(event)'></input>
		<button onclick='set(this, $tableName, $customId)'>Set</button>
		<div id='results' style='width:50%;height:200px;'>
			
		</div>
	</div>";

	return "$options $str";
}

?>
		<a href="assign_decision_split.php" class="btn" style="margin:10px;">Next: Setting split customs to decision</a>
	</body>
</html>