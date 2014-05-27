<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../../css/bootstrap.css" />
		<script type="text/javascript" src="../../lib/jquery-2.0.0.js" ></script>
		<script type="text/javascript">

		function submit(button)
		{
			var customId = $(button).attr("id").substring(7);
			var split = $(button).attr("id").substring(0, 7);
			// var ds = setZero ? 0 : dropdown.val();

			// var sayWhat = (split == "dosplit") ? 
			// return;

			var row = $(button.parentNode.parentNode);

			$.ajax({
				url: "set_item.php",
				type: "post",
			 	data: { 
			 		what: split, 
			 		custom: customId, 
			 		// ds: ds 
			 	}
			}).done(function( msg ){
				if(msg == "win")
				{
					// Shrink the row
					// Then remove it at the end
					row.fadeOut(1000, function(){
						row.css({ "display": "none" });
					});

					var count = $("#count").html() * 1;
					$("#count").html("" + (count - 1));
				}
				else
				{
					// Don't do anything
					alert("Fail " + msg);
				}
				// alert(msg);
			});
		}

		</script>
	</head>
	<body>
		<table>
<?php

require_once "../../lib/database.php";

$q = "SELECT * FROM `participant_has_custom` WHERE split = 0 AND ignore_custom = 0 ORDER BY participant_id";
$ret = $db->q($q);
if(!$ret)
{
	echo "Fail: $q<br />";
	return;
}

$num = mysqli_num_rows($ret);

echo "<tr>
			<th><span id='count'>$num</span> Decisions</th>
			<th>Thisislongsothatthebuttons</th>
		</tr>";

$pid = 0;
while($r = mysqli_fetch_array($ret))
{
	if($r["participant_id"] !== $pid)
	{
		$pid = $r["participant_id"];
		echo "<tr><th>$pid</th></tr>";
	}


	// List each decision along with an ok/split button
	echo "<tr>
		<td><br /><b>$r[name]</b><br />$r[description]<br /><br />$r[rationale]</td>
		<td>
			<button id='dosplit$r[id]' onclick='submit(this)' style='margin-right:10px;'>Split</button>
			<button id='nosplit$r[id]' type='button' onclick='submit(this)' style='margin-right:20px;'>Don't split</button>
			<button id='ignored$r[id]' type='button' onclick='submit(this)' style='margin-right:20px;' class='close'>&times</button>
 		</td>
	</tr>";
}

?>
		</table>
		<a href="split_custom.php" class="btn" style="margin:10px;">Next: Actually split custom</a>
	</body>
</html>