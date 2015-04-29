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
	<style>
		span.stars, span.stars span {
			display: inline-block;
			background: url(img/dice64x64.png) 0 -12px repeat-x;
			background-size: 12px 24px;
			width: 60px;
			height: 12px;
		}
		span.stars span {
			background-position: 0 0;
		}
	</style>


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

		$.fn.stars = function() {
			return $(this).each(function() {
				// Get the value
				var val = parseFloat($(this).html());
				// Make sure that the value is in 0 - 5 range, multiply to get width
				var size = Math.max(0, (Math.min(5, val))) * 12;
				// Create stars holder
				var $span = $('<span />').width(size);
				// Replace the numerical value with stars
				$(this).html($span);
			});
		}
		
		$(function() {
			$('span.stars').stars();
		});

	</script>
</head>
<body>


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

				$stmt = $mysql->prepare("SELECT username, picture, joined, description FROM users WHERE username SOUNDS LIKE ? OR username LIKE ?
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
				$u = null; $p = null; $j = null; $d = null;
				$stmt->bind_result($u, $p, $j, $d);
				while($stmt->fetch()){
					$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
					$row["picture"] = $p;
					$row["joined"] = $j;
					$row["description"] = $d;
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

				$stmt = $mysql->prepare("SELECT username, picture, joined, description FROM users WHERE username SOUNDS LIKE ? OR username LIKE ?
							ORDER BY joined DESC");
			
				$pvaluep = "%".$value."%";
				$stmt->bind_param("ss", $value, $pvaluep);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$u = null; $p = null; $j = null; $d = null;
				$stmt->bind_result($u, $p, $j, $d);
				while($stmt->fetch()){
					$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
					$row["picture"] = $p;
					$row["joined"] = $j;
					$row["description"] = $d;
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

				$stmt = $mysql->prepare("SELECT id, img, name, game, username, avg_fun, avg_balance, `desc`, privacy FROM contributions WHERE (name SOUNDS LIKE ? OR name LIKE ?
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
				$id = null; $img = null; $n = null; $g = null; $u = null; $af = null; $ab = null; $desc = null; $priv = null;
				$stmt->bind_result($id, $img, $n, $g, $u, $af, $ab, $desc, $priv);
				while($stmt->fetch()){
					$row["id"] = $id;
					$row["img"] = $img;
					$row["name"] = htmlspecialchars($n, ENT_QUOTES, "UTF-8");
					$row["game"] = htmlspecialchars($g, ENT_QUOTES, "UTF-8");
					$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
					$row["avg_fun"] = $af;
					$row["avg_balance"] = $ab;
					$row["desc"] = strip_tags($desc);
					$row["privacy"] = $priv;
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

				$stmt = $mysql->prepare("SELECT id, img, name, game, username, avg_fun, avg_balance, `desc`, privacy FROM contributions WHERE (name SOUNDS LIKE ? OR name LIKE ?
							OR type SOUNDS LIKE ? OR type LIKE ?
							OR sub_type SOUNDS LIKE ? OR sub_type LIKE ?
							OR game SOUNDS LIKE ? OR game LIKE ?) AND (privacy = 0 OR username = ?)
							ORDER BY CASE WHEN name = ? THEN 0
							WHEN name LIKE ? THEN 1
							WHEN name LIKE ? THEN 2
							WHEN name LIKE ? THEN 3
							ELSE 4 END, avg_fun ASC");
			
				$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
				$stmt->bind_param("sssssssssssss", $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $_SESSION["username"], $value, $valuep, $pvaluep, $pvalue);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$id = null; $img = null; $n = null; $g = null; $u = null; $af = null; $ab = null; $desc = null; $priv = null;
				$stmt->bind_result($id, $img, $n, $g, $u, $af, $ab, $desc, $priv);
				while($stmt->fetch()){
					$row["id"] = $id;
					$row["img"] = $img;
					$row["name"] = htmlspecialchars($n, ENT_QUOTES, "UTF-8");
					$row["game"] = htmlspecialchars($g, ENT_QUOTES, "UTF-8");
					$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
					$row["avg_fun"] = $af;
					$row["avg_balance"] = $ab;
					$row["desc"] = strip_tags($desc);
					$row["privacy"] = $priv;
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

				$stmt = $mysql->prepare("SELECT id, img, name, game, username, avg_fun, avg_balance, `desc`, privacy FROM contributions WHERE (name SOUNDS LIKE ? OR name LIKE ?
							OR type SOUNDS LIKE ? OR type LIKE ?
							OR sub_type SOUNDS LIKE ? OR sub_type LIKE ?
							OR game SOUNDS LIKE ? OR game LIKE ?) AND (privacy = 0 OR username = ?)
							ORDER BY CASE WHEN name = ? THEN 0
							WHEN name LIKE ? THEN 1
							WHEN name LIKE ? THEN 2
							WHEN name LIKE ? THEN 3
							ELSE 4 END, timestamp DESC");
			
				$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
				$stmt->bind_param("sssssssssssss", $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $value, $pvaluep, $_SESSION["username"], $value, $valuep, $pvaluep, $pvalue);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$id = null; $img = null; $n = null; $g = null; $u = null; $af = null; $ab = null; $desc = null; $priv = null;
				$stmt->bind_result($id, $img, $n, $g, $u, $af, $ab, $desc, $priv);
				while($stmt->fetch()){
					$row["id"] = $id;
					$row["img"] = $img;
					$row["name"] = htmlspecialchars($n, ENT_QUOTES, "UTF-8");
					$row["game"] = htmlspecialchars($g, ENT_QUOTES, "UTF-8");
					$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
					$row["avg_fun"] = $af;
					$row["avg_balance"] = $ab;
					$row["desc"] = strip_tags($desc);
					$row["privacy"] = $priv;
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
		echo "<input type='hidden' name='csort' value='".htmlspecialchars($_GET["csort"], ENT_QUOTES, "UTF-8")."'>";
		echo "<input type='hidden' name='keywords' value='".htmlspecialchars($_GET["keywords"], ENT_QUOTES, "UTF-8")."'>";
		echo "</form>";
		echo "<div class='listshowhide'>";
		echo "<a id='uhide' onclick='toggle_vis(\"ulist\",this);'>[hide]</a>";
		echo "</div><hr>";
		echo "<ul id='ulist'>";

		// arsort() will sort our array in reverse order and maintain our index association.
		if($_GET["usort"] == "relevance")
			arsort($udupecount);	
		//print_r($udupecount);
		//print_r($rowarr);

		// PRINT OUT USER RESULTS
		foreach($udupecount as $key => $numdupes){
			if(count($words) == 1 || $numdupes >= (count($words)-1)){
				$value = $rowarr[$key];
				echo "<li class='searchlistitem clearfix'>";		// This will be n-1, where n is the amount of keywords we have to search by.
				echo "<div class='searchthumbnail'>";
				echo "<span class='verticalspan' style='background-color: green;'><span class='verticaltext ellipsis'>USER</span></span>";
				echo "<a href='view_contribution_updateable.php?contid=".$value["id"]."' style='inline-block'>";
				if($value["privacy"] == 1){
					echo "<div class='search_img_overlay'>";
					echo "<p class='search_overlay_text'>You have set this contribution to private</p>";
					echo "</div>";
				}
				echo "<img src='".$value["picture"]."' alt='A picture of ".$value["username"]."' height='100px' width='100px'>";
				echo "</a>";
				echo "</div>";
				echo "<div class='searchtext'>";
				echo "<a href='view_contribution_updateable.php?contid=".$value["id"]."'>";
				echo "<b class='searchitemname'>".$value["username"]."</b><br>";
				echo "</a>";
				$joinPieces = explode(" ", $value["joined"]);
				echo "<span class='small_desctext'>Joined ".$joinPieces[0]."</span><br>";
				echo "<span class='searchdesc'>".$value["description"]."</span>";
				//echo "<span class='small_desctext searchgame'>".$value["game"]."</span>";

				echo "</div>";
				echo "</li>";
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
		echo "<input type='hidden' name='usort' value='".htmlspecialchars($_GET["usort"], ENT_QUOTES, "UTF-8")."'>";
		echo "<input type='hidden' name='keywords' value='".htmlspecialchars($_GET["keywords"], ENT_QUOTES, "UTF-8")."'>";
		echo "</form>";
		echo "<div class='listshowhide'>";
		echo "<a id='chide' onclick='toggle_vis(\"clist\",this);'>[hide]</a>";
		echo "</div><hr>";
		echo "<ul id='clist'>";

		// arsort() will sort our array in reverse order and maintain our index association.
		if($_GET["csort"] == "relevance")
			arsort($dupecount);	
		//print_r($dupecount);
		//print_r($crowarr);

		// PRINT OUT CONTRIBUTION RESULTS
		foreach($dupecount as $key => $numdupes){
			if(count($words) == 1 || $numdupes >= (count($words)-1)){	// The more keywords we have, the more trash we're likely to get, so lets set
				$value = $crowarr[$key];				// a minimum hit requirement in order to display to the page. 
				echo "<li class='searchlistitem clearfix'>";		// This will be n-1, where n is the amount of keywords we have to search by.
				echo "<div class='searchthumbnail'>";
				echo "<span class='verticalspan'><span class='verticaltext ellipsis'>".$value["game"]."</span></span>";
				echo "<a href='view_contribution_updateable.php?contid=".$value["id"]."' style='inline-block'>";
				if($value["privacy"] == 1){
					echo "<div class='search_img_overlay'>";
					echo "<p class='search_overlay_text'>You have set this contribution to private</p>";
					echo "</div>";
				}
				echo "<img src='".$value["img"]."' alt='A picture of ".$value["name"]."' height='100px' width='100px'>";
				echo "</a>";
				echo "</div>";
				echo "<div class='searchtext'>";
				echo "<a href='view_contribution_updateable.php?contid=".$value["id"]."'>";
				echo "<b class='searchitemname'>".$value["name"]."</b><br>";
				echo "</a>";
				echo "<span class='small_desctext'>By ".$value["username"]." for ".$value["game"]."</span><br>";
				if($value["avg_fun"] != -1)
					echo "<table class='rating_table searchratings'><tr><td>Fun</td><td><span class='stars'>".$value["avg_fun"]."</span></td><td>Balance</td><td><span class='stars'>".$value["avg_balance"]."</span></td></tr></table>";
				else
					echo "<span class='small_desctext'>Not yet rated.</span><br>";
				echo "<span class='searchdesc'>".$value["desc"]."</span>";
				//echo "<span class='small_desctext searchgame'>".$value["game"]."</span>";

				echo "</div>";
				echo "</li>";
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
