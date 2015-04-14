<?php
	include "header.php";
	session_start();
?>
<DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="css/example/profile.css" media="all">
</head>
<body>

<script type="text/javascript" language="javascript">
	function toggle_vis(list, hide){
		var e = document.getElementById(list);
		if(e.style.display != "none") {
			e.style.display = "none";
		}else{
			e.style.display = "block";
		}
		if(hide.text != "[show]"){
			hide.text = "[show]";
		}else{
			hide.text = "[hide]";
		}
	}

</script>

<div id='container'>
<?php
	$keywords = $_GET["keywords"];
	$words = explode(" ", $keywords);

/*
	for($i = 0; $i<strlen($keyregex); $i++){
		if(!($i == 0 || $i == (strlen($keyregex)-1))){
			$keyregex[$i] = '%';
		}
	}
	var_dump($keyregex);
	print($keyregex);
*/
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}

	try{
		/******************************************************************
			$_GET["csort"] and $_GET["usort"] dictate sorting procedure
		********************************************************************/

		$mysql->query("START TRANSACTION");
		$udupecount = array();
		$dupecount = array();	


		// SORTED BY RELEVANCE
		if($_GET["usort"] == "relevance"){
			foreach($words as $value){
			/*	$result = $mysql->query("SELECT * FROM users WHERE username SOUNDS LIKE '".$value."' OR username LIKE '%".$value."%'
							ORDER BY CASE WHEN username = '".$value."' THEN 0
							WHEN username LIKE '".$value."%' THEN 1
							WHEN username LIKE '%".$value."%' THEN 2
							WHEN username LIKE '%".$value."' THEN 3
							ELSE 4 END, username ASC");*/

				$stmt = $mysql->prepare("SELECT username, picture, joined FROM users WHERE username SOUNDS LIKE ? OR username LIKE ?
							ORDER BY CASE WHEN username = ? THEN 0
							WHEN username LIKE ? THEN 1
							WHEN username LIKE ? THEN 2
							WHEN username LIKE ? THEN 3
							ELSE 4 END, username ASC");
			
				$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
				$stmt->bind_param("ssssss", $value, $pvaluep, $value, $valuep, $pvaluep, $pvalue);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$u = null; $p = null; $j = null;
				$stmt->bind_result($u, $p, $j);
				while($stmt->fetch()){
					$row["username"] = $u;
					$row["picture"] = $p;
					$row["joined"] = $j;
					$rowarr[$u] = $row;
					if(isset($udupecount[$row["username"]]))		// dupecount is gonna keep track of how many hits we get for each result
						$udupecount[$row["username"]]++;
					else
						$udupecount[$row["username"]] = 1;
				}
				$stmt->close();
			}

		}else if($_GET["usort"] == "joindate"){
			foreach($words as $value){
				/*$result = $mysql->query("SELECT * FROM users WHERE username SOUNDS LIKE '".$value."' OR username LIKE '%".$value."%'
							ORDER BY joined ASC");*/

				$stmt = $mysql->prepare("SELECT username, picture, joined FROM users WHERE username SOUNDS LIKE ? OR username LIKE ?
							ORDER BY joined ASC");
			
				$pvaluep = "%".$value."%";
				$stmt->bind_param("ss", $value, $pvaluep);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$u = null; $p = null; $j = null;
				$stmt->bind_result($u, $p, $j);
				while($stmt->fetch()){
					$row["username"] = $u;
					$row["picture"] = $p;
					$row["joined"] = $j;
					$rowarr[$u] = $row;
					if(isset($udupecount[$row["username"]]))		// dupecount is gonna keep track of how many hits we get for each result
						$udupecount[$row["username"]]++;
					else
						$udupecount[$row["username"]] = 1;
				}
				$stmt->close();			
			}		
		}


		// SORTED BY RELEVANCE (we should probably add more ordering conditions later to keep it more 'Relevant')
		// Contributions should check keywords against type, wtype, and game as well as name
		if($_GET["csort"] == "relevance"){
			foreach($words as $value){
				/*$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$value."' OR name LIKE '%".$value."%'
							OR type SOUNDS LIKE '".$value."'
							OR sub_type SOUNDS LIKE '".$value."' OR sub_type LIKE '%".$value."%'
							OR game SOUNDS LIKE '".$value."'
							ORDER BY CASE WHEN name = '".$value."' THEN 0
							WHEN name LIKE '".$value."%' THEN 1
							WHEN name LIKE '%".$value."%' THEN 2
							WHEN name LIKE '%".$value."' THEN 3
							ELSE 4 END, name ASC");*/

				$stmt = $mysql->prepare("SELECT id, img, name, game, username FROM contributions WHERE (name SOUNDS LIKE ? OR name LIKE ?
							OR type SOUNDS LIKE ? OR type LIKE ?
							OR sub_type SOUNDS LIKE ? OR sub_type LIKE ?
							OR game SOUNDS LIKE ? OR game LIKE ?) AND (privacy = 0 OR username = ?)
							ORDER BY CASE WHEN name = ? THEN 0
							WHEN name LIKE ? THEN 1
							WHEN name LIKE ? THEN 2
							WHEN name LIKE ? THEN 3
							ELSE 4 END, name ASC");
			
				$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
				$stmt->bind_param("sssssssssssss", $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $_SESSION["username"], $value, $valuep, $pvaluep, $pvalue);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$id = null; $img = null; $n = null; $g = null; $u = null;
				$stmt->bind_result($id, $img, $n, $g, $u);
				while($stmt->fetch()){
					$row["id"] = $id;
					$row["img"] = $img;
					$row["name"] = $n;
					$row["game"] = $g;
					$row["username"] = $u;
					$crowarr[$id] = $row;
					if(isset($dupecount[$row["id"]]))		// dupecount is gonna keep track of how many hits we get for each result
						$dupecount[$row["id"]]++;
					else
						$dupecount[$row["id"]] = 1;
				}

				$stmt->close();
	
			}	
		}else if($_GET["csort"] == "rating"){
			foreach($words as $value){
				/*$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$value."' OR name LIKE '%".$value."%'
							OR type SOUNDS LIKE '".$value."'
							OR sub_type SOUNDS LIKE '".$value."' OR sub_type LIKE '%".$value."%'
							OR game SOUNDS LIKE '".$value."'
							ORDER BY CASE WHEN name = '".$value."' THEN 0
							WHEN name LIKE '".$value."%' THEN 1
							WHEN name LIKE '%".$value."%' THEN 2
							WHEN name LIKE '%".$value."' THEN 3
							ELSE 4 END, name ASC");*/

				$stmt = $mysql->prepare("SELECT id, img, name, game, username FROM contributions WHERE (name SOUNDS LIKE ? OR name LIKE ?
							OR type SOUNDS LIKE ? OR type LIKE ?
							OR sub_type SOUNDS LIKE ? OR sub_type LIKE ?
							OR game SOUNDS LIKE ? OR game LIKE ?) AND (privacy = 0 OR username = ?)
							ORDER BY CASE WHEN name = ? THEN 0
							WHEN name LIKE ? THEN 1
							WHEN name LIKE ? THEN 2
							WHEN name LIKE ? THEN 3
							ELSE 4 END, name ASC");
			
				$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
				$stmt->bind_param("sssssssssssss", $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $_SESSION["username"], $value, $valuep, $pvaluep, $pvalue);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$id = null; $img = null; $n = null; $g = null; $u = null;
				$stmt->bind_result($id, $img, $n, $g, $u);
				while($stmt->fetch()){
					$row["id"] = $id;
					$row["img"] = $img;
					$row["name"] = $n;
					$row["game"] = $g;
					$row["username"] = $u;
					$crowarr[$id] = $row;
					if(isset($dupecount[$row["id"]]))		// dupecount is gonna keep track of how many hits we get for each result
						$dupecount[$row["id"]]++;
					else
						$dupecount[$row["id"]] = 1;
				}

				$stmt->close();
			}	
		}else if($_GET["csort"] == "submitdate"){
			foreach($words as $value){
				/*$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$value."' OR name LIKE '%".$value."%'
							OR type SOUNDS LIKE '".$value."'
							OR sub_type SOUNDS LIKE '".$value."' OR sub_type LIKE '%".$value."%'
							OR game SOUNDS LIKE '".$value."'
							ORDER BY CASE WHEN name = '".$value."' THEN 0
							WHEN name LIKE '".$value."%' THEN 1
							WHEN name LIKE '%".$value."%' THEN 2
							WHEN name LIKE '%".$value."' THEN 3
							ELSE 4 END, timestamp ASC");*/

				$stmt = $mysql->prepare("SELECT id, img, name, game, username FROM contributions WHERE (name SOUNDS LIKE ? OR name LIKE ?
							OR type SOUNDS LIKE ? OR type LIKE ?
							OR sub_type SOUNDS LIKE ? OR sub_type LIKE ?
							OR game SOUNDS LIKE ? OR game LIKE ?) AND (privacy = 0 OR username = ?)
							ORDER BY CASE WHEN name = ? THEN 0
							WHEN name LIKE ? THEN 1
							WHEN name LIKE ? THEN 2
							WHEN name LIKE ? THEN 3
							ELSE 4 END, timestamp ASC");
			
				$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
				$stmt->bind_param("sssssssssssss", $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $_SESSION["username"], $value, $valuep, $pvaluep, $pvalue);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$id = null; $img = null; $n = null; $g = null; $u = null;
				$stmt->bind_result($id, $img, $n, $g, $u);
				while($stmt->fetch()){
					$row["id"] = $id;
					$row["img"] = $img;
					$row["name"] = $n;
					$row["game"] = $g;
					$row["username"] = $u;
					$crowarr[$id] = $row;
					if(isset($dupecount[$row["id"]]))		// dupecount is gonna keep track of how many hits we get for each result
						$dupecount[$row["id"]]++;
					else
						$dupecount[$row["id"]] = 1;
				}

				$stmt->close();
			}
		}

		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}



	$resultcount = 0;

	echo "<div id='utop'>";
	if($rowarr){
		// USER RESULTS HEADER
		echo "<h2 class='zacsh2'>User Results</h2>";
		echo "<form method=GET class='inlineform' action='search_results.php'>";
		echo "<label for='usort' style='padding-left: .5em'>Sort by </label><select id='usort' name='usort' onchange='this.form.submit()'>";
		if($_GET["usort"] == "relevance"){
			echo "<option value='relevance' selected='selected'>Relevance</option>";
			echo "<option value='joindate'>Join Date</option>";
		}else{
			echo "<option value='relevance'>Relevance</option>";
			echo "<option value='joindate' selected='selected'>Join Date</option>";
		}
		echo "</select>";
		echo "<input type='hidden' name='csort' value='".$_GET["csort"]."'>";
		echo "<input type='hidden' name='keywords' value='".$_GET["keywords"]."'>";
		echo "</form>";
		echo "<div class='listshowhide'>";
		echo "<a href='#' id='uhide' onclick='toggle_vis(\"ulist\",this);'>[hide]</a>";
		echo "</div><hr>";
		echo "<ul id='ulist'>";

		// arsort() will sort our array in reverse order and maintain our index association.
		if($_GET["usort"] == "joindate")
			arsort($udupecount);	
		//print_r($udupecount);
		//print_r($rowarr);

		// PRINT OUT USER RESULTS
		foreach($udupecount as $key => $numdupes){
			if(count($words) == 1 || $numdupes >= (count($words)-1)){
				$value = $rowarr[$key];
				echo "<li class='searchlistitem'>";
				echo "<div class='searchresult'>";
				//echo "<img src='".$value["picture"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["username"]."' />";
				//echo "<a href='profile.php?user=".$value["username"]."'>".$value["username"]."</a>";
				echo "<a href='profile.php?user=".$value["username"]."'>";
				echo "<img src='".$value["picture"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["username"]."' />";
				echo "<p style='float:right;'><b>".$value["username"]."</b><br>User Since:<br>".$value["joined"]."</p>";
				echo "</a>";
				echo "</div>";
				echo "</li>";
				echo "<br>";
				echo "<br>";
				$resultcount++;
			}
		}
		echo "</ul>";
	}

	echo "</div>";

	echo "<div id='ctop'>";
	if($crowarr){
		// CONTRIBUTION RESULTS HEADER
		echo "<h2 class='zacsh2'>Contribution Results</h2>";
		echo "<form method=GET class='inlineform' action='search_results.php'>";
		echo "<label for='csort' style='padding-left: .5em'>Sort by </label><select id='csort' name='csort' onchange='this.form.submit()' selected='".$_GET["csort"]."'>";
		if($_GET["csort"] == "relevance"){
			echo "<option value='relevance' selected='selected'>Relevance</option>";
			echo "<option value='rating'>Rating</option>";
			echo "<option value='submitdate'>Submission Date</option>";
		}else if($_GET["csort"] == "rating"){
			echo "<option value='relevance'>Relevance</option>";
			echo "<option value='rating' selected='selected'>Rating</option>";
			echo "<option value='submitdate'>Submission Date</option>";
		}else{
			echo "<option value='relevance'>Relevance</option>";
			echo "<option value='rating'>Rating</option>";
			echo "<option value='submitdate' selected='selected'>Submission Date</option>";
		}
		echo "</select>";
		echo "<input type='hidden' name='usort' value='".$_GET["usort"]."'>";
		echo "<input type='hidden' name='keywords' value='".$_GET["keywords"]."'>";
		echo "</form>";
		echo "<div class='listshowhide'>";
		echo "<a href='#' id='chide' onclick='toggle_vis(\"clist\",this);'>[hide]</a>";
		echo "</div><hr>";
		echo "<ul id='clist'>";

		// arsort() will sort our array in reverse order and maintain our index association.
		if($_GET["csort"] == "submitdate")
			arsort($dupecount);	
		//print_r($dupecount);
		//print_r($crowarr);

		// PRINT OUT CONTRIBUTION RESULTS
		foreach($dupecount as $key => $numdupes){
			if(count($words) == 1 || $numdupes >= (count($words)-1)){	// The more keywords we have, the more trash we're likely to get, so lets set
				$value = $crowarr[$key];				// a minimum hit requirement in order to display to the page. 
				echo "<li class='searchlistitem'>";			// This will be n-1, where n is the amount of keywords we have to search by.
				echo "<div class='searchresult'>";
				//echo "<img src='".$value["img"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["name"]."' />";
				//echo "<a href='view_contribution.php?contid=".$value["id"]."'>".$value["name"]."</a>";
				echo "<a href='view_contribution_updateable.php?contid=".$value["id"]."'>";
				echo "<img src='".$value["img"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["name"]."' />";
				echo "<p style='float:right'><b>".$value["name"]."</b><br>".$value["game"]."<br>By ".$value["username"]."</p>";
				echo "</a>";
				echo "</div>";
				echo "</li>";
				echo "<br>";
				echo "<br>";
				$resultcount++;
			}
		}
	
	}
	echo "</ul>";
	echo "<h2>".$resultcount." results found!</h2>";


?>



</div>
</body>
</html>
