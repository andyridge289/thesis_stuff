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
			 		what: "update option", 
			 		custom: customId, 
			 		option: option
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
			 		what: "verify option", 
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

$q = "SELECT * FROM `option` ORDER BY name";
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

$q = "SELECT co.option_id, `option`.name, pc.name AS cName, pc.description, pc.id AS custom_id, ds.name AS dsName
		FROM `custom_is_option` AS co
		LEFT JOIN `participant_has_custom` AS pc ON co.custom_id = pc.id
		LEFT JOIN `option` ON co.option_id = `option`.id
		LEFT JOIN `decision_has_option` AS do ON `option`.id = do.option_id 
		LEFT JOIN `ds` ON do.category_id = ds.id 
		WHERE co.verified = 0
		ORDER BY ds.id";
$ret = $db->q($q);
if(!$ret) { echo "Fail: $q<br />"; return; }

$last = 0;

// This needs to be organised by DS I suppose?

while($r = mysqli_fetch_array($ret))
{
	if($last !== $r["option_id"])
	{
		if($last !== 0)
		{
			echo "</table>";
		}

		echo "<h4>$r[dsName]</h4>";
		$last = $r["option_id"];

		echo "<table>";
	}

	$find = "value='$r[option_id]'";
	$pos = strpos($comboString, $find) + strlen($find);

	$comboString = substr($comboString, 0, $pos) . " selected " . substr($comboString, $pos);

	echo "
		<tr>
			<td><b>$r[cName]</b><br />$r[description]</td>
			<td><b>$r[name]</b></td>
			<td id='button_box$r[custom_id]'>
				<button id='edit$r[custom_id]' onclick='edit(this)'>Edit</button>
				<button id='close$r[custom_id]'  onclick='hideRow(this)' class='label-success button' style='margin-right:10px;'>Verify</button>
			</td>
			<td id='dropdown_box$r[custom_id]' style='display:none;'>
				<select id='newds$r[custom_id]'>
					$comboString
				</select>
				<button id='submit$r[custom_id]' onclick='submit(this)'>Submit</button>
			</td>
		</tr>";
}

?>
		</table>
	</body>
</html>