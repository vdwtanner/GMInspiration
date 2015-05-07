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
<ul id="quick_search" class="quick_search">
		<li id="armor">
				
				<a href="search_results.php?keywords=armor&csort=rating"><img src="img/Armor15.png">Armor</a></li>
		&nbsp <li id="classes">
				
				<a href="search_results.php?keywords=class&csort=rating"><img src="img/Classes15.png">Classes</a></li>
		&nbsp <li id="feats">
				
				<a href="search_results.php?keywords=feat&csort=rating"><img src="img/Feats15.png">Feats</a></li>
		&nbsp <li id="items">
				
				<a href="search_results.php?keywords=item&csort=rating"><img src="img/Items15.png">Items</a></li>
		&nbsp <li id="monsters">
				
				<a href="search_results.php?keywords=monster&csort=rating"><img src="img/Monsters15.png">Monsters</a></li>
		&nbsp <li id="races">
				
				<a href="search_results.php?keywords=race&csort=rating"><img src="img/Races15.png">Races</a></li>
		&nbsp <li id="spells">
				
				<a href="search_results.php?keywords=spell&csort=rating"><img src="img/Spells15.png">Spells</a></li>
		&nbsp <li id="weapons">
				
				<a href="search_results.php?keywords=weapon&csort=rating"><img src="img/Weapons15.png">Weapons</a></li>
	</ul>
<?php
	$keywords = $_GET["keywords"];
	$ekeywords = htmlspecialchars($keywords, ENT_QUOTES, "UTF-8");
	if(strlen($keywords) > 200){
		$keywords = substr($keywords, 0, 200);
	}
	$words = explode(" ", $keywords);
	//$matchwords = implode("* ", $words)."*";
	$matchwords = ">";
	for($i = 0; $i<count($words); $i++){
		$matchwords = $matchwords.$words[$i]."* ";
	}

	//print_r(htmlspecialchars($matchwords, ENT_QUOTES, "UTF-8"));
	//RESULTCOUNT KEEPS TRACK OF THE NUMBER OF RESULTS WE DISPLAY
	$resultcount = 0;
	// HARDCODE THE RESULTS PER PAGE TO 10
	$resultLimit = 10;
	// OUR MINIMUM RELEVANCE THAT A RESULT MUST MEET TO BE DISPLAYED
	$minRel = (count($words)*2) + 4;
	// RELEVANCE WEIGHTS
	$username_weight = 5;
	$name_weight = 5;
	$type_weight = 5;
	$sub_type_weight = 3;
	$game_weight = 3;
	$desc_weight = 1;

	// GET OUR OFFSET FROM THE URL BAR FOR PAGING
	if($_GET["offset"])
		$offset = htmlspecialchars($_GET["offset"], ENT_QUOTES, "UTF-8");
	else
		$offset = 0;
	

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
		foreach($words as $value){

			$stmt = $mysql->prepare("SELECT username, picture, joined, description, admin FROM users WHERE username SOUNDS LIKE ?
						ORDER BY CASE WHEN username = ? THEN 0
						WHEN username LIKE ? THEN 1
						WHEN username LIKE ? THEN 2
						WHEN username LIKE ? THEN 3
						ELSE 4 END, username ASC LIMIT 10 OFFSET ?");
	
			$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
			$stmt->bind_param("sssssi", $value, $value, $valuep, $pvaluep, $pvalue, $offset);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}	
			$u = null; $p = null; $j = null; $d = null; $admin = null;
			$stmt->bind_result($u, $p, $j, $d, $admin);
			while($stmt->fetch() || !(count($rowarr) != 15)){
				$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
				$row["picture"] = $p;
				$row["joined"] = $j;
				$row["description"] = $d;
				$row["admin"] = $admin;
				$rowarr[$u] = $row;
				if(isset($udupecount[$row["username"]]))		// dupecount is gonna keep track of how many hits we get for each result
					$udupecount[$row["username"]]++;
				else
					$udupecount[$row["username"]] = 1;
			}
			$stmt->close();
			//print_r($rowarr);
			//print_r($udupecount);
		}

		// SORTED BY RELEVANCE (we should probably add more ordering conditions later to keep it more 'Relevant')
		// Contributions should check keywords against type, wtype, and game as well as name
		if($_GET["csort"] == "relevance"){
	
				// LETS GET OUR TOTAL RESULT COUNT FIRST BEFORE WE LIMIT IT.
				$stmt = $mysql->prepare("SELECT * FROM (SELECT *, MATCH(username) AGAINST(? IN BOOLEAN MODE) AS username_rel,
							MATCH(name) AGAINST(? IN BOOLEAN MODE) AS name_rel, MATCH(sub_type) AGAINST(? IN BOOLEAN MODE) AS sub_type_rel,
							MATCH(type) AGAINST(? IN BOOLEAN MODE) AS type_rel, MATCH(game) AGAINST(? IN BOOLEAN MODE) AS game_rel,
							MATCH(`desc`) AGAINST(?) AS desc_rel FROM contributions WHERE (privacy = 0 OR username = ?)) AS RESULTS
							WHERE ((username_rel*?)+(name_rel*?)+(sub_type_rel*?)+(type_rel*?)+(game_rel*?)+(desc_rel*?) > ?)");
			
			
				$stmt->bind_param("sssssssiiiiiii", $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $_SESSION["username"], $username_weight, $name_weight, $sub_type_weight, $type_weight, $game_weight, $desc_weight, $minRel);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$stmt->store_result();
				$resulttotal += $stmt->num_rows;
				$stmt->close();

				$stmt = $mysql->prepare("SELECT id, img, name, game, username, avg_fun, avg_balance, `desc`, privacy, type, sub_type FROM (SELECT *,
							MATCH(username) AGAINST (? IN BOOLEAN MODE) AS username_rel,
							MATCH(name) AGAINST(? IN BOOLEAN MODE) AS name_rel, MATCH(sub_type) AGAINST(? IN BOOLEAN MODE) AS sub_type_rel,
							MATCH(type) AGAINST(? IN BOOLEAN MODE) AS type_rel, MATCH(game) AGAINST(? IN BOOLEAN MODE) AS game_rel,
							MATCH(`desc`) AGAINST(?) AS desc_rel FROM contributions WHERE (privacy = 0 OR username = ?)) AS RESULTS
							WHERE ((username_rel*?)+(name_rel*?)+(sub_type_rel*?)+(type_rel*?)+(game_rel*?)+(desc_rel*?) > ?)
							ORDER BY (username_rel*?)+(name_rel*?)+(sub_type_rel*?)+(type_rel*?)+(game_rel*?)+(desc_rel*?) DESC LIMIT ? OFFSET ?");
			
			
				$stmt->bind_param("sssssssiiiiiiiiiiiiiii", $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $_SESSION["username"], $username_weight, $name_weight, $sub_type_weight, $type_weight, $game_weight, $desc_weight, $minRel, $username_weight, $name_weight, $sub_type_weight, $type_weight, $game_weight, $desc_weight, $resultLimit, $offset);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$id = null; $img = null; $n = null; $g = null; $u = null; $af = null; $ab = null; $desc = null; $priv = null; $t = null; $s_t = null;
				$stmt->bind_result($id, $img, $n, $g, $u, $af, $ab, $desc, $priv, $t, $s_t);
				$stmt->store_result();
				$resultcount += $stmt->num_rows;
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
					$row["type"] = $t;
					$row["sub_type"] = $s_t;
					$crowarr[$id] = $row;
					/*if(isset($dupecount[$row["id"]]))		// dupecount is gonna keep track of how many hits we get for each result
						$dupecount[$row["id"]]++;
					else
						$dupecount[$row["id"]] = 1;*/
				}
				$stmt->close();
	
		}else if($_GET["csort"] == "rating"){
				/*$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$value."' OR name LIKE '%".$value."%'
							OR type SOUNDS LIKE '".$value."'
							OR sub_type SOUNDS LIKE '".$value."' OR sub_type LIKE '%".$value."%'
							OR game SOUNDS LIKE '".$value."'
							ORDER BY CASE WHEN name = '".$value."' THEN 0
							WHEN name LIKE '".$value."%' THEN 1
							WHEN name LIKE '%".$value."%' THEN 2
							WHEN name LIKE '%".$value."' THEN 3
							ELSE 4 END, name ASC");*/

				/*$stmt = $mysql->prepare("SELECT id, img, name, game, username, avg_fun, avg_balance, `desc`, privacy FROM contributions WHERE (name SOUNDS LIKE ? OR name LIKE ?
							OR type SOUNDS LIKE ? OR type LIKE ? OR username SOUNDS LIKE ?
							OR sub_type SOUNDS LIKE ? OR sub_type LIKE ?
							OR game SOUNDS LIKE ? OR game LIKE ?) AND (privacy = 0 OR username = ?)
							ORDER BY avg_fun DESC");*/
			
				//$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
				//$stmt->bind_param("ssssssssss", $value, $pvaluep, $value, $pvaluep, $value, $value, $pvaluep, $value, $pvaluep, $_SESSION["username"]);




				// LETS GET OUR TOTAL RESULT COUNT FIRST BEFORE WE LIMIT IT.
				$stmt = $mysql->prepare("SELECT * FROM (SELECT *, MATCH(username) AGAINST(? IN BOOLEAN MODE) AS username_rel,
							MATCH(name) AGAINST(? IN BOOLEAN MODE) AS name_rel, MATCH(sub_type) AGAINST(? IN BOOLEAN MODE) AS sub_type_rel,
							MATCH(type) AGAINST(? IN BOOLEAN MODE) AS type_rel, MATCH(game) AGAINST(? IN BOOLEAN MODE) AS game_rel,
							MATCH(`desc`) AGAINST(?) AS desc_rel FROM contributions WHERE (privacy = 0 OR username = ?)) AS RESULTS
							WHERE ((username_rel*?)+(name_rel*?)+(sub_type_rel*?)+(type_rel*?)+(game_rel*?)+(desc_rel*?) > ?)");
			
			
				$stmt->bind_param("sssssssiiiiiii", $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $_SESSION["username"], $username_weight, $name_weight, $sub_type_weight, $type_weight, $game_weight, $desc_weight, $minRel);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$stmt->store_result();
				$resulttotal = $stmt->num_rows;
				$stmt->close();


				// NOW LETS GET THE ACTUAL DATA
				$stmt = $mysql->prepare("SELECT id, img, name, game, username, avg_fun, avg_balance, `desc`, privacy, type, sub_type FROM (SELECT *,
							MATCH(username) AGAINST (? IN BOOLEAN MODE) AS username_rel,
							MATCH(name) AGAINST(? IN BOOLEAN MODE) AS name_rel, MATCH(sub_type) AGAINST(? IN BOOLEAN MODE) AS sub_type_rel,
							MATCH(type) AGAINST(? IN BOOLEAN MODE) AS type_rel, MATCH(game) AGAINST(? IN BOOLEAN MODE) AS game_rel,
							MATCH(`desc`) AGAINST(?) AS desc_rel FROM contributions WHERE (privacy = 0 OR username = ?)) AS RESULTS
							WHERE ((username_rel*?)+(name_rel*?)+(sub_type_rel*?)+(type_rel*?)+(game_rel*?)+(desc_rel*?) > ?)
							ORDER BY ((avg_fun + avg_balance)/2) DESC LIMIT ? OFFSET ?");


				$stmt->bind_param("sssssssiiiiiiiii", $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $_SESSION["username"], $username_weight, $name_weight, $sub_type_weight, $type_weight, $game_weight, $desc_weight, $minRel, $resultLimit, $offset);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$id = null; $img = null; $n = null; $g = null; $u = null; $af = null; $ab = null; $desc = null; $priv = null; $t = null; $s_t = null;
				$stmt->bind_result($id, $img, $n, $g, $u, $af, $ab, $desc, $priv, $t, $s_t);
				$stmt->store_result();
				$resultcount += $stmt->num_rows;
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
					$row["type"] = $t;
					$row["sub_type"] = $s_t;
					$crowarr[$id] = $row;
					/*if(isset($dupecount[$row["id"]]))		// dupecount is gonna keep track of how many hits we get for each result
						$dupecount[$row["id"]]++;
					else
						$dupecount[$row["id"]] = 1;*/
				}


				$stmt->close();	
		}else if($_GET["csort"] == "submitdate"){
				/*$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$value."' OR name LIKE '%".$value."%'
							OR type SOUNDS LIKE '".$value."'
							OR sub_type SOUNDS LIKE '".$value."' OR sub_type LIKE '%".$value."%'
							OR game SOUNDS LIKE '".$value."'
							ORDER BY CASE WHEN name = '".$value."' THEN 0
							WHEN name LIKE '".$value."%' THEN 1
							WHEN name LIKE '%".$value."%' THEN 2
							WHEN name LIKE '%".$value."' THEN 3
							ELSE 4 END, timestamp ASC");*/

				/*$stmt = $mysql->prepare("SELECT id, img, name, game, username, avg_fun, avg_balance, `desc`, privacy FROM contributions WHERE (name SOUNDS LIKE ? OR name LIKE ?
							OR type SOUNDS LIKE ? OR type LIKE ? OR username SOUNDS LIKE ?
							OR sub_type SOUNDS LIKE ? OR sub_type LIKE ?
							OR game SOUNDS LIKE ? OR game LIKE ?) AND (privacy = 0 OR username = ?)
							ORDER BY CASE WHEN name = ? THEN 0
							WHEN name LIKE ? THEN 1
							WHEN name LIKE ? THEN 2
							WHEN name LIKE ? THEN 3
							ELSE 4 END, timestamp DESC");*/
			
				//$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value;
				//$stmt->bind_param("ssssssssssssss", $value, $pvaluep, $value, $pvaluep, $value, $value, $pvaluep, $value, $pvaluep, $_SESSION["username"], $value, $valuep, $pvaluep, $pvalue);

				// LETS GET OUR TOTAL RESULT COUNT FIRST BEFORE WE LIMIT IT.
				$stmt = $mysql->prepare("SELECT * FROM (SELECT *, MATCH(username) AGAINST(? IN BOOLEAN MODE) AS username_rel,
							MATCH(name) AGAINST(? IN BOOLEAN MODE) AS name_rel, MATCH(sub_type) AGAINST(? IN BOOLEAN MODE) AS sub_type_rel,
							MATCH(type) AGAINST(? IN BOOLEAN MODE) AS type_rel, MATCH(game) AGAINST(? IN BOOLEAN MODE) AS game_rel,
							MATCH(`desc`) AGAINST(?) AS desc_rel FROM contributions WHERE (privacy = 0 OR username = ?)) AS RESULTS
							WHERE ((username_rel*?)+(name_rel*?)+(sub_type_rel*?)+(type_rel*?)+(game_rel*?)+(desc_rel*?) > ?)");
			
			
				$stmt->bind_param("sssssssiiiiiii", $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $_SESSION["username"], $username_weight, $name_weight, $sub_type_weight, $type_weight, $game_weight, $desc_weight, $minRel);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$stmt->store_result();
				$resulttotal = $stmt->num_rows;
				$stmt->close();


				// NOW LETS GET THE ACTUAL DATA
				$stmt = $mysql->prepare("SELECT id, img, name, game, username, avg_fun, avg_balance, `desc`, privacy, type, sub_type FROM (SELECT *,
							MATCH(username) AGAINST (? IN BOOLEAN MODE) AS username_rel,
							MATCH(name) AGAINST(? IN BOOLEAN MODE) AS name_rel, MATCH(sub_type) AGAINST(? IN BOOLEAN MODE) AS sub_type_rel,
							MATCH(type) AGAINST(? IN BOOLEAN MODE) AS type_rel, MATCH(game) AGAINST(? IN BOOLEAN MODE) AS game_rel,
							MATCH(`desc`) AGAINST(?) AS desc_rel FROM contributions WHERE (privacy = 0 OR username = ?)) AS RESULTS
							WHERE ((username_rel*?)+(name_rel*?)+(sub_type_rel*?)+(type_rel*?)+(game_rel*?)+(desc_rel*?) > ?)
							ORDER BY timestamp DESC LIMIT ? OFFSET ?");


				$stmt->bind_param("sssssssiiiiiiiii", $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $matchwords, $_SESSION["username"], $username_weight, $name_weight, $sub_type_weight, $type_weight, $game_weight, $desc_weight, $minRel, $resultLimit, $offset);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}	
				$id = null; $img = null; $n = null; $g = null; $u = null; $af = null; $ab = null; $desc = null; $priv = null; $t = null; $s_t = null;
				$stmt->bind_result($id, $img, $n, $g, $u, $af, $ab, $desc, $priv, $t, $s_t);
				$stmt->store_result();
				$resultcount += $stmt->num_rows;
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
					$row["type"] = $t;
					$row["sub_type"] = $s_t;
					$crowarr[$id] = $row;
					/*if(isset($dupecount[$row["id"]]))		// dupecount is gonna keep track of how many hits we get for each result
						$dupecount[$row["id"]]++;
					else
						$dupecount[$row["id"]] = 1;*/
				}

				$stmt->close();
		}

		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}

	if(($count + $resultcount)-1 >= $resulttotal)
		$endCount = $resulttotal;
	else
		$endCount = $count + $resultcount;

	
	// RESULTS HEADER
	if($resulttotal != 0)
		echo "<h2 class='zacsh2 ellipsis'>Showing Results ".($offset+1)."-".($offset+$resultcount)." out of ".$resulttotal."</h2>";
	else
		echo "<h2 class='zacsh2'>No Results Found!</h2>";
	echo "<form method=GET class='inlineform' action='search_results.php'>";
	echo "<div style='padding-top: .3em; float: right;'>";
	echo "<label for='csort' style='padding-left: .5em;'>Sort by </label><select id='csort' name='csort' onchange='this.form.submit()' selected='".$_GET["csort"]."'>";
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
	echo "<div style='float: right; padding-left: .5em;'>";
	if(($offset - $resultLimit) >= 0){
		$csort = htmlspecialchars($_GET["csort"], ENT_QUOTES, "UTF-8");
		echo "<a href='search_results.php?keywords=".$keywords."&csort=".$csort."&offset=".($offset - $resultLimit)."'>[prev]</a>";
	}	
	if(($offset + $resultLimit) < $resulttotal){

		$csort = htmlspecialchars($_GET["csort"], ENT_QUOTES, "UTF-8");
		echo "<a class='button' href='search_results.php?keywords=".$keywords."&csort=".$csort."&offset=".($offset + $resultLimit)."'>next</a>";
	}

	echo "</div>";
	echo "</div><hr>";

	//echo "<div id='ctop'>";
	if($crowarr || $rowarr){
		$wordCount = count($words);
		if($rowarr)
			foreach($udupecount as $key => $numdupes)
				if($wordCount == 1 || $numdupes >= $wordCount-1)
					$resultcount++;
		if($crowarr)
			foreach($dupecount as $key => $numdupes)
				if($wordCount == 1 || $numdupes >= $wordCount-1)
					$resultcount++;


	}

	if($rowarr){
		echo "<ul id='ulist'>";

		// arsort() will sort our array in reverse order and maintain our index association.
		if($_GET["usort"] == "relevance")
			arsort($udupecount);	

		// PRINT OUT USER RESULTS
		foreach($udupecount as $key => $numdupes){
			if($wordCount == 1 || $numdupes >= $wordCount-1){
				$value = $rowarr[$key];
				echo "<li class='searchlistitem clearfix'>";		// This will be n-1, where n is the amount of keywords we have to search by.
				echo "<div class='searchthumbnail'>";
				if($value["admin"] == 1)
					echo "<span class='verticalspan' style='background-color: red;'><span class='verticaltext ellipsis'>ADMIN</span></span>";
				else
					echo "<span class='verticalspan' style='background-color: green;'><span class='verticaltext ellipsis'>USER</span></span>";
				echo "<a href='view_contribution_updateable.php?contid=".$value["id"]."' style='inline-block'>";
				echo "<img src='".$value["picture"]."' alt='A picture of ".$value["username"]."' height='100px' width='100px'>";
				echo "</a>";
				echo "</div>";
				echo "<div class='searchtext'>";
				echo "<a href='profile.php?user=".$value["username"]."'>";
				echo "<b class='searchitemname'>".$value["username"]."</b><br>";
				echo "</a>";
				$joinPieces = explode(" ", $value["joined"]);
				echo "<span class='small_desctext'>Joined ".$joinPieces[0]."</span><br>";
				echo "<span class='searchdesc'>".$value["description"]."</span>";
				//echo "<span class='small_desctext searchgame'>".$value["game"]."</span>";

				echo "</div>";
				echo "</li>";
			}
		}
		echo "</ul>";
		echo "<hr>";

	}

	if($crowarr){
		// arsort() will sort our array in reverse order and maintain our index association.	
		//print_r($dupecount);
		//print_r($crowarr);

		echo "<ul id='clist'>";
		$count = 1;
		// PRINT OUT CONTRIBUTION RESULTS
		foreach($crowarr as $key => $value){
				echo "<li class='searchlistitem clearfix'>";
				echo "<span class='searchnumbers'>".($offset+($count++))."</span>";
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
				if($value["sub_type"])
					echo "<b class='searchitemname ellipsis'>".$value["name"]." - ".$value["type"]." (".$value["sub_type"].")</b><br>";
				else
					echo "<b class='searchitemname ellipsis'>".$value["name"]." - ".$value["type"]."</b><br>";
				echo "</a>";
				echo "<span class='small_desctext'>By <a href='profile.php?user=".$value["username"]."'>".$value["username"]."</a> for ".$value["game"]."</span><br>";
				if($value["avg_fun"] != -1)
					echo "<table class='rating_table searchratings'><tr><td>Fun</td><td><span class='stars'>".$value["avg_fun"]."</span></td><td>Balance</td><td><span class='stars'>".$value["avg_balance"]."</span></td></tr></table>";
				else
					echo "<span class='small_desctext'>Not yet rated.</span><br>";
				echo "<span class='searchdesc'>".$value["desc"]."</span>";
				//echo "<span class='small_desctext searchgame'>".$value["game"]."</span>";

				echo "</div>";
				echo "</li>";
		}
		echo "</ul>";

		echo "<hr>";
		echo "<div style='float: right; padding-left: .5em; display: inline-block'>";
		if(($offset - $resultLimit) >= 0){
			$csort = htmlspecialchars($_GET["csort"], ENT_QUOTES, "UTF-8");
			echo "<a class='button' href='search_results.php?keywords=".$ekeywords."&csort=".$csort."&offset=".($offset - $resultLimit)."'>prev</a>";
		}	
		if(($offset + $resultLimit) < $resulttotal){

			$csort = htmlspecialchars($_GET["csort"], ENT_QUOTES, "UTF-8");

			echo "<a class='button' href='search_results.php?keywords=".$ekeywords."&csort=".$csort."&offset=".($offset + $resultLimit)."'>next</a>";
		}

		echo "</div>";
		echo "<br><hr>";
	}

?>

</div>

</body>
</html>
