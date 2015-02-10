<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="css/example/profile.css" media="all">
	<?php include "header.php";?>

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
	$keyregex = $_GET["keywords"];
/*
	for($i = 0; $i<strlen($keyregex); $i++){
		if(!($i == 0 || $i == (strlen($keyregex)-1))){
			$keyregex[$i] = '%';
		}
	}
	var_dump($keyregex);
	print($keyregex);
*/
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}

	try{
		/******************************************************************
			$_GET["csort"] and $_GET["usort"] dictate sorting procedure
		********************************************************************/

		$mysql->query("START TRANSACTION");

		// SORTED BY RELEVANCE
		if($_GET["usort"] == "relevance"){
			$result = $mysql->query("SELECT * FROM users WHERE username SOUNDS LIKE '".$keywords."' OR username LIKE '%".$keywords."%'
							ORDER BY CASE WHEN username = '".$keywords."' THEN 0
							WHEN username LIKE '".$keywords."%' THEN 1
							WHEN username LIKE '%".$keywords."%' THEN 2
							WHEN username LIKE '%".$keywords."' THEN 3
							ELSE 4 END, username ASC");
			while($row = $result->fetch_assoc()){
				$rowarr[] = $row;			
			}		

		}else if($_GET["usort"] == "joindate"){
			$result = $mysql->query("SELECT * FROM users WHERE username SOUNDS LIKE '".$keywords."' OR username LIKE '%".$keywords."%'
							ORDER BY joined ASC");
			while($row = $result->fetch_assoc()){
				$rowarr[] = $row;			
			}				
		}


		// SORTED BY RELEVANCE (we should probably add more ordering conditions later to keep it more 'Relevant')
		// Contributions should check keywords against type, wtype, and game as well as name
		if($_GET["csort"] == "relevance"){
			$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$keywords."' OR name LIKE '%".$keywords."%'
							OR type SOUNDS LIKE '".$keywords."'
							OR sub_type SOUNDS LIKE '".$keywords."' OR sub_type LIKE '%".$keywords."%'
							OR game SOUNDS LIKE '".$keywords."'
							ORDER BY CASE WHEN name = '".$keywords."' THEN 0
							WHEN name LIKE '".$keywords."%' THEN 1
							WHEN name LIKE '%".$keywords."%' THEN 2
							WHEN name LIKE '%".$keywords."' THEN 3
							ELSE 4 END, name ASC");

			while($row = $result->fetch_assoc()){
				$crowarr[] = $row;			
			}
		}else if($_GET["csort"] == "rating"){
			$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$keywords."' OR name LIKE '%".$keywords."%'
							OR type SOUNDS LIKE '".$keywords."' 
							OR sub_type SOUNDS LIKE '".$keywords."' OR sub_type LIKE '%".$keywords."%'
							OR game SOUNDS LIKE '".$keywords."' 
							ORDER BY CASE WHEN name = '".$keywords."' THEN 0
							WHEN name LIKE '".$keywords."%' THEN 1
							WHEN name LIKE '%".$keywords."%' THEN 2
							WHEN name LIKE '%".$keywords."' THEN 3
							ELSE 4 END, name ASC");

			while($row = $result->fetch_assoc()){
				$crowarr[] = $row;			
			}
		}else if($_GET["csort"] == "submitdate"){
			$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$keywords."' OR name LIKE '%".$keywords."%'
							OR type SOUNDS LIKE '".$keywords."'
							OR sub_type SOUNDS LIKE '".$keywords."' OR sub_type LIKE '%".$keywords."%'
							OR game SOUNDS LIKE '".$keywords."' 
							ORDER BY timestamp ASC");

			while($row = $result->fetch_assoc()){
				$crowarr[] = $row;			
			}
		}

		echo "<h2>".(count($rowarr)+count($crowarr))." results found!</h2>";

		echo "<div id='utop'>";
		if($rowarr){
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
			foreach($rowarr as $key => $value){
				echo "<li class='searchlistitem'>";
				echo "<div class='searchresult'>";
				//echo "<img src='".$value["picture"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["username"]."' />";
				//echo "<a href='profile.php?user=".$value["username"]."'>".$value["username"]."</a>";
				echo "<a href='profile.php?user=".$value["username"]."'>";
				echo "<img src='".$value["picture"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["username"]."' />";
				echo "<h2 style='float:right'>".$value["username"]."</h2>";
				echo "</a>";
				echo "</div>";
				echo "</li>";
				echo "<br>";
				echo "<br>";
			}
			echo "</ul>";
		}

		echo "</div>";

		echo "<div id='ctop'>";
		if($crowarr){
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
			foreach($crowarr as $key => $value){
				echo "<li class='searchlistitem'>";
				echo "<div class='searchresult'>";
				//echo "<img src='".$value["img"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["name"]."' />";
				//echo "<a href='view_contribution.php?contid=".$value["id"]."'>".$value["name"]."</a>";
				echo "<a href='view_contribution.php?contid=".$value["id"]."'>";
				echo "<img src='".$value["img"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["name"]."' />";
				echo "<h2 style='float:right'>".$value["name"]."</h2>";
				echo "</a>";
				echo "</div>";
				echo "</li>";
				echo "<br>";
				echo "<br>";
			}
		
		}
		echo "</ul>";

	
	}catch(Exception $e){
	
	}

?>



</div>
</body>
</html>
