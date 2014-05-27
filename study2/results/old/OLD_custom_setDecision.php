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
			 		what: "decision", 
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
					alert("Fail!");
				}
				alert(msg);
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

$q = "SELECT * FROM `participant_has_custom`";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

while($r = mysqli_fetch_array($ret))
{
	$q2 = "SELECT * FROM `custom_is_decision` WHERE custom_id = $r[id]";
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
		<td><br /><select id='custom$r[id]' style='margin-right:5px;'>
			$comboString
		</select></td>
		<td>
			<button id='submit$r[id]' onclick='submit(this)' style='margin-right:10px;'>Go</button><br />
			<button id='ignore$r[id]' type='button' onclick='submit(this, true)' class='close' style='margin-right:20px;'>&times;</button>
		</td>
	</tr>";
}
?>
		</table>
		<a href="custom_setOption.php" class="btn" style="margin:10px;">Next: Setting options</a>
	</body>
</html>