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
<div id='container'>
<?php
	$keywords = $_GET["keywords"];
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}

	try{
		$mysql->query("START TRANSACTION");
		$result = $mysql->query("SELECT * FROM users WHERE username LIKE '%".$keywords."%'");
		
		while($row = $result->fetch_assoc()){
			$rowarr[] = $row;			
		}

		$result = $mysql->query("SELECT * FROM contributions WHERE name LIKE '%".$keywords."%'");	
		while($row = $result->fetch_assoc()){
			$crowarr[] = $row;			
		}		
		echo "<h2>".(count($rowarr)+count($crowarr))." results found!</h2>";
		echo "<ul id='searchlist'>";
		if($rowarr){
			foreach($rowarr as $key => $value){
				echo "<li class='searchlistitem'>";
				echo "<div class='searchresult'>";
				echo "<img src='".$value["picture"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["username"]."' />";
				echo "<a href='profile.php?user=".$value["username"]."'>".$value["username"]."</a>";
				echo "</div>";
				echo "</li>";
				echo "<br>";
				echo "<br>";
			}
		}
		if($crowarr){
			foreach($crowarr as $key => $value){
				echo "<li class='searchlistitem'>";
				echo "<div class='searchresult'>";
				echo "<img src='".$value["img"]."' style='float:left' height='100' width='100' alt='An image depicting ".$value["name"]."' />";
				echo "<a href='view_contribution.php?contid=".$value["id"]."'>".$value["name"]."</a>";
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
