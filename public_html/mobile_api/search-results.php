<?php
	session_start();
	mb_internal_encoding("UTF-8");
?>

<?php
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}
	
	try{
		$stmt = $mysql->prepare("SELECT permissions FROM api_keys WHERE api_key=?");
		$stmt->bind_param("s", $_GET["api_key"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$p = null;
		$stmt->bind_result($p);	
		$stmt->fetch();
		$stmt->close();

		if(!$p)
			exit("{\"msg\":\"Error: Invalid Key\"}");


		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}

?>


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
	

	$resultjson = "";

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
				$row["username"] = $u;
				$row["img"] = $p;
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

		unset($row);


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
					$row["name"] = $n;
					$row["game"] = $g;
					$row["username"] = $u;
					$row["avg_fun"] = $af;
					$row["avg_balance"] = $ab;
					$row["desc"] = strip_tags($desc, "<br><p>");
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
					$row["name"] = $n;
					$row["game"] = $g;
					$row["username"] = $u;
					$row["avg_fun"] = $af;
					$row["avg_balance"] = $ab;
					$row["desc"] = strip_tags($desc, "<br><p>");
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
					$row["name"] = $n;
					$row["game"] = $g;
					$row["username"] = $u;
					$row["avg_fun"] = $af;
					$row["avg_balance"] = $ab;
					$row["desc"] = strip_tags($desc, "<br><p>");
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

		// arsort() will sort our array in reverse order and maintain our index association.
		if($_GET["usort"] == "relevance")
			arsort($udupecount);	

		// PRINT OUT USER RESULTS
		foreach($udupecount as $key => $numdupes){
			if($wordCount == 1 || $numdupes >= $wordCount-1){
				$value = $rowarr[$key];
				$resultarr[] = $value;
			}
		}

	}

	if($crowarr){
		// arsort() will sort our array in reverse order and maintain our index association.	
		//print_r($dupecount);
		//print_r($crowarr);

		$count = 1;
		// PRINT OUT CONTRIBUTION RESULTS
		foreach($crowarr as $key => $value){
				$resultarr[] = $value;
		}

	}

	$resultjson = json_encode($resultarr);
	print_r($resultjson);
?>

