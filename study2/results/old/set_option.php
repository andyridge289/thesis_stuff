<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
		<script type="text/javascript" src="../lib/jquery-2.0.0.js" ></script>
		<script type="text/javascript">

		function addSplit(button)
		{
			var customId = $(button).attr("id").substring(7);
			var row = $(button.parentNode.parentNode);
			var addingRow = $("#adding_row");

			var contents = $("<tr><td><textarea id='new_name'></textarea></td><td><textarea id='new_description'></textarea>
				<br /><textarea id='new_rationale'></textarea></td><td><button id='submit$r[id]' onclick='submit(this)' style='margin-right:10px;'>Add</button></td></tr>");

			addingRow.append(contents);
		}
		
		function submit(button)
		{
			

			
			// var split = $(button).attr("id").substring(0, 7);
			// // var ds = setZero ? 0 : dropdown.val();

			// // var sayWhat = (split == "dosplit") ? 
			// // return;

			

			// $.ajax({
			// 	url: "set_item.php",
			// 	type: "post",
			//  	data: { 
			//  		what: split, 
			//  		custom: customId, 
			//  		// ds: ds 
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
			// 		// Don't do anything
			// 		alert("Fail " + msg);
			// 	}
			// 	// alert(msg);
			// });
		}

		</script>
	</head>
	<body>
		<table>
			<tr>
				<th>Decision</th>
				<th>Options</th>
			</tr>
			<tr id='adding_row'></tr>
<?php

require_once "../lib/database.php";

$q = "SELECT * FROM `participant_has_custom` WHERE split = 2";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

while($r = mysqli_fetch_array($ret))
{
	// List each decision along with an ok/split button
	echo "<tr>
		<td><b>$r[name]</b><br />$r[description]</td>
		<td>
			<button id='dosplit$r[id]' onclick='addSplit(this)' style='margin-right:10px;'>Add</button><br />" .
			// <button id='nosplit$r[id]' type='button' onclick='submit(this)'  style='margin-right:20px;'>Don't split</button>
		"</td>
	</tr>";
}

?>
		</table>
		<a href="custom_setDecision.php" class="btn" style="margin:10px;">Next: Setting decisions</a>
	</body>
</html>