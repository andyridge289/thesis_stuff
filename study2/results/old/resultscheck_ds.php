<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
		<style type="text/css">
			h4
			{
				width: 100%;
				text-align: center;
			}
		</style>
		<script type="text/javascript" src="../lib/jquery-2.0.0.js" ></script>
		<script type="text/javascript">

		function submit(button)
		{
			var customId = $(button).attr("id").substring(6);
			var dropdown = $("#newds" + customId);
			var ds = dropdown.val();

			var row = $(button.parentNode.parentNode);

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "update ds", 
			 		custom: customId, 
			 		ds: ds 
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
					// Don't do anything
					alert("Fail");
				}
				// alert(msg);
			});
		}

		function edit(button)
		{	
			var id = $(button).attr("id").substring(4);
			
			var buttonBox = $("#button_box" + id);
			buttonBox.css({ "display": "none" });

			var dropdownBox = $("#dropdown_box" + id);
			dropdownBox.css({ "display": "block" });
		}

		function hideRow(button)
		{
			var customId = $(button).attr("id").substring(5);
			var row = $(button.parentNode.parentNode);

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: "verify ds", 
			 		custom: customId, 
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
					// Don't do anything
					alert("Fail: " + msg);
				}
				// alert(msg);
			});
		}

		</script>
	</head>
	<body>
<?php

require_once "../lib/database.php";

$q = "SELECT cd.ds, ds_table.name AS dsName, pc.name AS cName, pc.description, pc.id AS custom_id
		FROM `custom_in_ds` AS cd
		LEFT JOIN `participant_has_custom` AS pc ON cd.custom_id = pc.id
		LEFT JOIN `ds` AS ds_table ON cd.ds = ds_table.id
		WHERE cd.verified = 0
		ORDER BY cd.ds";
$ret = $db->q($q);
if(!$ret) { echo "Fail: $q<br />"; return; }

$last = 0;

while($r = mysqli_fetch_array($ret))
{
	if($last !== $r["ds"])
	{
		if($last !== 0)
		{
			echo "</table>";
		}

		echo "<h4>$r[dsName]</h4>";
		$last = $r["ds"];

		echo "<table>";
	}

	echo "
		<tr>
			<td><b>$r[cName]</b><br />$r[description]</td>
			<td id='button_box$r[custom_id]'>
				<button id='edit$r[custom_id]' onclick='edit(this)'>Edit</button>
				<button id='close$r[custom_id]'  onclick='hideRow(this)' class='label-success button' style='margin-right:10px;'>Verify</button>
			</td>
			<td id='dropdown_box$r[custom_id]' style='display:none;'>
				<select id='newds$r[custom_id]'>
					<option value='1'>Functional</option>
					<option value='2'>Non-functional</option>
					<option value='3'>Structural</option>
					<option value='4'>Entity</option>
				</select>
				<button id='submit$r[custom_id]' onclick='submit(this)'>Submit</button>
			</td>
		</tr>";
}

?>
		</table>
		<a href="resultscheck_decision.php" class="btn" style="margin:10px;">Next: Verifying decisions</a> 
	</body>
</html>