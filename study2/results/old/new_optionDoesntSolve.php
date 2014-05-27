<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
		<script type="text/javascript" src="../lib/jquery-2.0.0.js" ></script>
		<script type="text/javascript">

		function submit(button)
		{
			// var customId = $(button).attr("id").substring(6);
			// var dropdown = $("#custom" + customId);
			// var decision = dropdown.val();

			// var row = $(button.parentNode.parentNode);

			// $.ajax({
			// 	url: "set_item.php",
			// 	type: "post",
			//  	data: { 
			//  		what: "new option solves old decision", 
			//  		custom: customId, 
			//  		decision: decision 
			//  	}
			// }).done(function( msg ){
			// 	if(msg == "win")
			// 	{
			// 		// Shrink the row
			// 		// Then remove it at the end
			// 		row.fadeOut(1000, function(){
			// 			row.css({ "display": "none" });
			// 		});
			// 	}
			// 	else
			// 	{
			// 		alert("Fail! " + msg);
			// 	}
			// });
		}

		</script>
	</head>
	<body>
		<table>
			<tr>
				<th>Custom</th>
				<th></th>
			</tr>
<?php

require_once "../lib/database.php";

// Get the stuff that is in new option but isn't in new_option_solves_old_decision

$q = "SELECT * FROM `custom_new_option` AS co
		LEFT JOIN `participant_has_custom` AS pc ON co.custom_id = pc.id";
$ret = $db->q($q);
if(!$ret) { echo "Fail: $q<br />"; return; }

while($r = mysqli_fetch_array($ret))
{
	$q2 = "SELECT * FROM `new_option_solves_old_decision` WHERE new_option = $r[custom_id]";
	$ret2 = $db->q($q2);
	if(!$ret2)
	{
		echo "Fail: $q<br />";
		continue;
	}

	if(mysqli_num_rows($ret2) > 0)
		continue;

	echo "<tr>
		<td><b>$r[name]</b><br />$r[description]</td>
		</tr>";
}

// Not really sure what to do with these yet

?>
		</table>
		<a href="new_optionDoesntSolve.php" class="btn" style="margin:10px;">Next: New options that don't solve old decisions</a>
	</body>
</html>