<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
		<script type="text/javascript" src="../lib/jquery-2.0.0.js" ></script>
		<script type="text/javascript">

		function submit(button)
		{
			var customId = $(button).attr("id").substring(6);
			var dropdown = $("#custom" + customId);
			var decision = dropdown.val();

			var row = $(button.parentNode.parentNode);

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "new option solves old decision", 
			 		custom: customId, 
			 		decision: decision 
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					// Shrink the row
					// Then remove it at the end
					row.fadeOut(1000, function(){
						row.css({ "display": "none" });
					});
				}
				else
				{
					alert("Fail! " + msg);
				}
			});
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

$q = "SELECT * FROM `decision` ORDER BY name";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

$comboString = "";
while($r = mysqli_fetch_array($ret))
{
	$comboString .= "<option value='$r[id]'>$r[name]</option>";
}

$q = "SELECT * FROM `custom_new_option` AS cd
		LEFT JOIN `participant_has_custom` AS pc ON cd.custom_id = pc.id";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

while($r = mysqli_fetch_array($ret))
{
	echo "<tr>
		<td><b>$r[name]</b><br />$r[description]</td>
		<td><select id='custom$r[id]' style='margin-right:5px;'>$comboString</select></td>
		<td><button id='submit$r[id]' onclick='submit(this)'>Submit</button></td>
		</tr>";
}

?>
		</table>
		<a href="new_optionDoesntSolve.php" class="btn" style="margin:10px;">Next: New options that don't solve old decisions</a>
	</body>
</html>