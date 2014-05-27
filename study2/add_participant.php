<?php

require_once "lib/database.php";

if(isset($_POST["name"]))
{
	$id = $_POST["number"];
	$name = $_POST["name"];
	$q = "INSERT INTO participant VALUES($id, \"$name\")";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}

	$condition = $_POST["condition"];
	$q = "INSERT INTO `participant_has_condition` VALUES('', $id, $condition)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}

	$sc = $_POST["sc"];
	$sc_tools = $_POST["sc_tools"];
	$designed = $_POST["design"];

	$platforms = "";
	$otherPlatforms = "";

	if(isset($_POST["platform_desktop"]))
		$platforms = "desktop";

	if(isset($_POST["platform_mobile"]))
		if(strcmp($platforms, "") === 0)
			$platforms = "mobile";
		else
			$platforms .= ",mobile";

	if(isset($_POST["platform_tablet"]))
		if(strcmp($platforms, "") === 0)
			$platforms = "tablet";
		else
			$platforms .= ",tablet";

	if(isset($_POST["platform_web"]))
		if(strcmp($platforms, "") === 0)
			$platforms = "web";
		else
			$platforms .= ",web";

	if(isset($_POST["platform_other"]))
	{
		if(strcmp($platforms, "") === 0)
			$platforms = "other";
		else
			$platforms .= ",other";

		$otherPlatforms = $_POST["other_platform"];	
	}

	$purpose = "";
	$otherPurpose = "";

	if(isset($_POST["purpose_commercial"]))
		$purpose = "commercial";

	if(isset($_POST["purpose_academic"]))
		if(strcmp($purpose, "") === 0)
			$purpose = "academic";
		else
			$purpose .= ",academic";

	if(isset($_POST["purpose_personal"]))
		if(strcmp($purpose, "") === 0)
			$purpose = "personal";
		else
			$purpose .= ",personal";

	if(isset($_POST["purpose_other"]))
	{
		if(strcmp($purpose, "") === 0)
			$purpose = "other";
		else
			$purpose .= ",other";

		$otherPurpose = $_POST["other_purpose"];
	}

	$numDesigns = $_POST["num_designs"];
	$programming = $_POST["programming"];
	$mashedApis = $_POST["mashed_apis"];
	$swJob = $_POST["sw_in_job"];

	$q = "INSERT INTO participant_sc VALUES('', $id, $sc, \"$sc_tools\", $designed, \"$platforms\", \"$otherPlatforms\", \"$purpose\", \"$otherPurpose\", \"$numDesigns\", \"$programming\", $mashedApis, $swJob)";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}

	$age = $_POST["age"];
	$gender = $_POST["gender"];
	$qualifications = $_POST["qualifications"];
	$field = $_POST["field"];
	$employment = $_POST["employment"];

	$q = "INSERT INTO participant_demographics VALUES('', $id, $age, \"$gender\", \"$qualifications\", \"$field\", \"$employment\")";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}

	$dc = $db->escape($_POST["design_comments"]);
	$hc = $db->escape($_POST["how_comments"]);
	$tc = $db->escape($_POST["tool_comments"]);

	$q = "INSERT INTO participant_postq VALUES('', $id, \"$dc\", \"$hc\", \"$tc\")";
	$ret = $db->q($q);
	if(!$ret)
	{
		echo "Fail: $q<br />";
	}

}

?>
<html>
	<head>
		<link rel="stylesheet" href="css/bootstrap.css" />
		<style type="text/css">
			input
			{
				margin-top: 8px;
				height: 30px;
			}

			label
			{
				display: inline;
				margin: 5px 10px 0px 5px;
			}
		</style>
	</head>
	<body>
		<form action="add_participant.php" method="post">
			<table>

				<tr><th colspan="2">Basic</th></tr>
				<tr><td>ID:</td><td><input style="height:30px;" type="number" name="number" /></td></tr>
				<tr><td>Name:</td><td><input style="height:30px;" type="text" name="name" /></td></tr>
				<tr><td>Condition:</td><td><input style="height:30px;" type="number" name="condition" /></td></tr>


				<tr><th colspan="2">Service Composition</th></tr>
				<tr><td>SC?</td><td><select name="sc"><option value="1">Yes</option><option value="0">No</option></td></tr>
				<tr><td>SC tools</td><td><input style="height:30px;" type="text" name="sc_tools" /></td>

				<tr><th colspan="2">Design</th></tr>
				<tr><td>Software?</td><td><select name="design"><option value="1">Yes</option><option value="0">No</option></td></tr>
				<tr><td>Platforms:</td></td>
					<td>
						<input type="checkbox" name="platform_desktop" value="desktop"></option><label for="desktop">Desktop</label>
						<input type="checkbox" name="platform_mobile" value="mobile"></option><label for="mobile">Mobile</label>
						<input type="checkbox" name="platform_tablet" value="tablet"></option><label for="tablet">Tablet</label><br />
						<input type="checkbox" name="platform_web" value="web"></option><label for="web">Web</label>
						<input type="checkbox" name="platform_other" value="other"></option><label for="other">Other</label>
					</td></tr>
				<tr><td>Other platform:</td><td><input type="text" style="height:30px;" name="other_platform" /></td></tr>
				<tr><td>Design purpose</td>
					<td>
						<input type="checkbox" name="purpose_commercial" value="commercial"></option><label for="commercial">Commercial</label>
						<input type="checkbox" name="purpose_academic" value="academic"></option><label for="academic">Academic</label><br />
						<input type="checkbox" name="purpose_personal" value="personal"></option><label for="personal">Personal</label>
						<input type="checkbox" name="purpose_other" value="other"></option><label for="other">Other</label>
				</td></tr>
				<tr><td>Other purpose:</td><td><input style="height:30px;" type="text" name="other_purpose" /></td></tr>
				<tr><td>Num designs:</td><td><input style="height:30px;" type="text" name="num_designs" /></td></tr>
				<tr><td>Programming languages:</td><td><input type="text" style="height:30px;" name="programming" /></td></tr>
				<tr><td>Mashed up APIs?</td><td><select name="mashed_apis"><option value="1">Yes</option><option value="0">No</option></td></tr>
				<tr><td>SW Design in job?</td><td><select name="sw_in_job"><option value="1">Yes</option><option value="0">No</option></td></tr>

				<tr><th colspan="2">Demographics</th></tr>
				<tr><td>Age:</td><td><input type="number" name="age" style="height:30px;" /></td></tr>
				<tr><td>Gender:</td>
					<td><select name="gender" >
						<option value="male">Male</option>
						<option value="female">Female</option>
				</select></td></tr>
				<tr><td>Qualifications:</td>
					<td><select name="qualifications">
						<option value="none">None</option>
						<option value="gcse">GCSE</option>
						<option value="a2">A levels</option>
						<option value="bsc">BSc</option>
						<option value="msc">MSc</option>
						<option value="phd">PhD</option>
				</select></td></tr>
				<tr><td>Field:</td><td><input type="text" name="field" style="height:30px;" /></td></tr>
				<tr><td>Employment:</td>
					<td><select name="employment">
						<option value="unemployed">Unemployed</option>
						<option value="selfemployed">Self-employed</option>
						<option value="employed">Employed</option>
						<option value="student">Student</option>
						<option value="retired">Retired</option>
						<option value="unable">Unable to work</option>
				</select></td></tr>

				<tr><th colspan="2">Post Questionnaire</th></tr>
				<tr><td>Design comments</td><td><textarea name="design_comments" rows="5" columns="200"></textarea></td></tr>
				<tr><td>How comments</td><td><textarea name="how_comments" rows="5" columns="200"></textarea></td></tr>
				<tr><td>Tool comments</td><td><textarea name="tool_comments" rows="5" columns="200"></textarea></td></tr>


				<tr><td colspan="2"><input type="submit" /></td></tr>
			</table>
		</form>
	</body>
</html>