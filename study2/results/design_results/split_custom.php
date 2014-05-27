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

		table, tr, input
		{
			width: 100%;
		}

		</style>
		<script type="text/javascript" src="../lib/jquery-2.0.0.js" ></script>
		<script type="text/javascript">

		function addSplit(button)
		{
			var customId = $(button).attr("id").substring(7);
			var row = $(button.parentNode.parentNode);
			var addingRow = $("#adding_row" + customId);

			addingRow.css({ "display": "block" });
			addingRow.empty();

			var contents = $("<table style='background-color:#EEE;'>" +
						"<tr><td>Name:</td><td><input id='new_name" + customId + "' /></td></tr>" +
						"<tr><td>Description:</td><td><input id='new_description" + customId + "' /></td></tr>" +
						"<tr><td>Rationale:</td><td><input id='new_rationale" + customId + "' /></textarea></td></tr>" +
						"<tr><td><button id='submit" + customId + "' onclick='submit(this)' style='margin-right:10px;'>Submit</button></td></tr></table>");

			addingRow.append(contents);
		}
		
		function submit(button)
		{
			var id = $(button).attr("id").substring(6);
			var name = $("#new_name" + id).val();
			var description = $("#new_description" + id).val();
			var rationale = $("#new_rationale" + id).val();

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "add split",
			 		id: id, 
			 		name: name,
					description: description,
					rationale: rationale
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					$("#adding_row" + id).fadeOut(1000, function(){
						$("#adding_row" + id).css({ "display": "none" });
					});
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
				// alert(msg);
			});
		}

		function done(button)
		{
			var id = $(button).attr("id").substring(7);

			// TODO Hide the whole row
			// TODO Set split to be 3 in the other table
			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "done split", 
			 		id: id
		// 	 		name: name,
		// 			description: description,
		// 			rationale: rationale
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
		// 		// alert(msg);
			});

		}

		</script>
	</head>
	<body>
<?php

require_once "../../lib/database.php";

$q = "SELECT * FROM `participant_has_custom` WHERE split = 2 ORDER BY participant_id";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

$num = mysqli_num_rows($ret);

echo "<div style='width:100%;text-align:center'><b><span id='count'>$num</span> to split</b></div><ul>";

$pid = 0;
while($r = mysqli_fetch_array($ret))
{
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
		<button id='dosplit$r[id]' onclick='addSplit(this)' style='margin-right:10px;'>Add</button>
		<button id='nosplit$r[id]' type='button' onclick='done(this)'  style='margin-right:20px;'>Done</button><br />
		<div id='adding_row$r[id]'></div>
	</div>";
}

echo "</div>";

?>
	</body>
</html>