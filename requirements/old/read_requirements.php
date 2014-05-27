<?php

require_once "database.php";

$stuff = File("requirements.txt");

$SOURCE = "Source: ";
$COTS = "COTS: ";
$SPECIFIC = "Specific";
$SUBSEQUENT = "Subsequent";
$DEPENDS_REQ = "Dependent:";
$DEPENDS_OTHER = "Dependency:";
$CONFLICT = "Conflict:";
$PRIOR = "Prior identification:";

$tables = array("reqs", "req_has_tag", "req_has_tool", "prior", "req_has_prior");

foreach($tables AS $table)
{
	$q = "TRUNCATE TABLE `$table`";
	$ret = $db->query($q);
	if(!$ret)
	{
		return;
	}
}

$index = 1;
for($i = 0; $i < count($stuff); $i++)
{
	$pos = strpos($stuff[$i], $index . ".");
	
	// This is an actual requirement
	if($pos === 0)
	{
		$req = $stuff[$i];
		if($index < 10)
			$req = substr($req, 2);
		else if($index < 100)
			$req = substr($req, 3);
		else
			$req = substr($req, 4);
	
		$q = "INSERT INTO reqs VALUES($index, \"" . trim(addslashes($req)) . "\", 0, 0, 0, '', 0)";

		$ret = $db->query($q);
		if(!$ret)
		{
			continue;
		}
		
		$index++;
	}
	else if(strpos($stuff[$i], $SOURCE) === 0)
	{	
	 	$sources = addslashes(trim(substr($stuff[$i], strlen($SOURCE))));
	 	$sources = str_replace("[", "", $sources);
	 	$sources = str_replace("]", "", $sources);
	 	$sources = explode(",", $sources);
	 	
	 	foreach($sources AS $source)
	 	{
	 		$source = trim($source);
		 	$q = "SELECT * FROM code WHERE code = \"$source\"";
		 	$ret = $db->query($q);
		 	if(!$ret)
				continue;
		
			if(mysqli_num_rows($ret) == 0)
			{
				// There aren't any with this name, so insert it
			 	//$q = "INSERT INTO tags VALUES('', \"$source\")";
			 	//echo "&nbsp;&nbsp;&nbsp;&nbsp;$q<br />";
			 	//$ret = $db->query($q);
			 	//if(!$ret)
			 	//	continue;
		 		
		 		//$q = "SELECT * FROM tags WHERE tag = \"$source\"";
	 			//$ret = $db->query($q);
			}
			
			$r = mysqli_fetch_array($ret);
		
			// Then I need to link them up
			$q = "INSERT INTO req_has_tag VALUES('', $index, $r[id])";
			echo "$q<br />";
			$ret = $db->query($q);
			if(!$ret)
				continue;
		}
	}
	else if(strpos($stuff[$i], $COTS) === 0)
	{
		$cots = explode(",", substr($stuff[$i], strlen($COTS)));
		foreach($cots AS $tool)
		{
			$tool = trim($tool);
			$q = "SELECT * FROM tool WHERE name = \"$tool\"";
			$ret = $db->query($q);
			if(!$ret)
				continue;
				
			$r = mysqli_fetch_array($ret);
			$q = "INSERT INTO req_has_tool VALUES('', $index, $r[id])";
			$ret = $db->query($q);
			if(!$ret)
				continue;
		}
	}
	else if(strpos($stuff[$i], $SPECIFIC) === 0)
	{
		$q = "UPDATE reqs SET `specific` = 1 WHERE `id` = $index";
		$ret = $db->query($q);
		if(!$ret)
			continue;
	}
	else if(strpos($stuff[$i], $SUBSEQUENT) === 0)
	{
		$q = "UPDATE reqs SET `subsequent` = 1 WHERE `id` = $index";
		$ret = $db->query($q);
		if(!$ret)
			continue;
	}
	else if(strpos($stuff[$i], $DEPENDS_REQ) === 0)
	{
		$dep = trim(substr($stuff[$i], strlen($DEPENDS_REQ)));
		$q = "UPDATE reqs SET `depends_req` = $dep WHERE `id` = $index";
		$ret = $db->query($q);
		if(!$ret)
			continue;
	}
	else if(strpos($stuff[$i], $DEPENDS_OTHER) === 0)
	{
		$dep = addslashes(trim(substr($stuff[$i], strlen($DEPENDS_OTHER))));
		$q = "UPDATE reqs SET `depends_other` = \"$dep\" WHERE `id` = $index";
		$ret = $db->query($q);
		if(!$ret)
			continue;
	}
	else if(strpos($stuff[$i], $CONFLICT) === 0)
	{
		$conflict = trim(substr($stuff[$i], strlen($CONFLICT)));
		$q = "UPDATE reqs SET `conflict` = $conflict WHERE `id` = $index";
		$ret = $db->query($q);
		if(!$ret)
			continue;
	}
	else if(strpos($stuff[$i], $PRIOR) === 0)
	{
		$priors = substr($stuff[$i], strlen($PRIOR));
		$priors = explode(",", $priors);
		
		foreach($priors AS $prior)
		{
			$prior = addslashes(trim($prior));
		
			$q = "SELECT * FROM prior WHERE prior = \"$prior\"";
			$ret = $db->query($q);
			if(!$ret)
				continue;
				
			if(mysqli_num_rows($ret) === 0)
			{
				$q = "INSERT INTO prior VALUES(\"\", \"$prior\")";
				$ret = $db->query($q);
				if(!$ret)
					continue;
					
				$q = "SELECT * FROM prior WHERE prior = \"$prior\"";
				$ret = $db->query($q);
			}

			$r = mysqli_fetch_array($ret);
			
			$q = "INSERT INTO req_has_prior VALUES ('', $index, $r[id])";
			$ret = $db->query($q);
			if(!$ret)
				continue;
		}
	}
	else 
	{
		echo "$stuff[$i]<br />";
	}
}

?>