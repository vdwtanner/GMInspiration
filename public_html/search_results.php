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

		// FIRST PRIORITY (RELEVANCE)
		$result = $mysql->query("SELECT * FROM contributions WHERE name SOUNDS LIKE '".$keywords."'");

		while($row = $result->fetch_assoc()){
			$crowarr[] = $row;			
		}
		$result = $mysql->query("SELECT * FROM users WHERE username SOUNDS LIKE '".$keywords."'");
		
		while($row = $result->fetch_assoc()){
			$rowarr[] = $row;			
		}

		// SECOND PRIORITY (RELEVANCE)
		$result = $mysql->query("SELECT * FROM contributions WHERE name LIKE '%".$keywords."%'");

		while($row = $result->fetch_assoc()){
			$crowarr[] = $row;			
		}
		$result = $mysql->query("SELECT * FROM users WHERE username LIKE '%".$keywords."%'");

		while($row = $result->fetch_assoc()){
			$rowarr[] = $row;			
		}

		echo "<h2>".(count($rowarr)+count($crowarr))." results found!</h2>";

		echo "<div id='utop'>";
		if($rowarr){
			echo "<h2 class='zacsh2'>User Results</h2>";
			echo "<label for='usort' style='padding-left: .5em'>Sort by </label><select id='usort' name='usort'>";
			echo "<option value='relevance'>Relevance</option>";
			echo "<option value='rating'>Rating</option>";
			echo "<option value='joindate'>Join Date</option>";
			echo "</select>";
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
			echo "<label for='csort' style='padding-left: .5em'>Sort by </label><select id='csort' name='csort'>";
			echo "<option value='relevance'>Relevance</option>";
			echo "<option value='rating'>Rating</option>";
			echo "<option value='submitdate'>Submission Date</option>";
			echo "</select>";
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
