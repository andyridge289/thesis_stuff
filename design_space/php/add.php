<html>
	<head></head>
	<body>
		<form action="add.php" method="post">
			<table>
				<tr><td>Name:&nbsp;</td><td><input type="text" name="name"></td></tr>
				<tr><td></td><td><select name="type">
					<option value="option">Option</option>
					<option value="decision">Decision</option>
					<option value="category">Category</option>
				</select></td></tr>
				<tr><td>Parent:&nbsp;</td><td><input type="text" name="parent"></td></tr>
				<tr><td></td><td><select name="parent_type">
					<option value="option">Option</option>
					<option value="decision">Decision</option>
					<option value="category">Category</option>
				</select></td></tr>
				<tr><td colspan="2" align="center"><input type="submit" /></td></tr>
			</table>
		</form>
	</body>
</html>
<?php

require_once "database.php";

if(!isset($_POST["name"]))
{
	return;
}

// Insert it into the relevant table
$q = "INSERT INTO `$_POST[type]` VALUES('', '$_POST[name]', 1)";
echo "$q<br />";

// Get the ID of the thing we just added
$q = "SELECT id FROM `$_POST[type]` WHERE name = '$_POST[name]'";
//$retval = $database->query($q);
//if(!$retval)
//	return;
	
//$row = mysqli_fetch_array($retval);
//$id = $row["id"];
echo "$q<br />";

// Get the ID of the relevant parent
$q = "SELECT id from `$_POST[parent_type]` WHERE name = '$_POST[parent]'";
$retval = $database->query($q);
if(!$retval)
	return;
	
$row = mysqli_fetch_array($retval);
$parentId = $row["id"];

// Then create the relevant connection
$q = "INSERT INTO `" . $_POST["parent_type"] . "_has_" . $_POST["type"] . "` VALUES('',(, )";
echo $q;
?>
