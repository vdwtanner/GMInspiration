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
<div id='container'>

<?php

	$display = 1;
	if($_SESSION["username"]){
		$username=$_SESSION["username"];
	}else{
		$display = 0;	
	}

	if($display == 1){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}

		try{
			$mysql->query("START TRANSACTION");
			//$result = $mysql->query("SELECT * from users where username='".$username."'");
			$stmt = $mysql->prepare("SELECT description, picture FROM users WHERE username=?");
			$stmt->bind_param("s", $username);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$description = null; $picture = null;
			$stmt->bind_result($description, $picture);
			$stmt->fetch();
			$stmt->close();

			$description = htmlspecialchars($description, ENT_QUOTES, "UTF-8");
			$picture = htmlspecialchars($picture, ENT_QUOTES, "UTF-8");

			echo "<form method='POST' action='profile.php'>";
			echo "Description<br>";
			echo "<textarea name='descrEdit' rows=7 cols=75>".$description."</textarea><br>";
			echo "Profile Image Data URL<br>";
			if ($picture=="img/hat_profile200.png"){
				echo "<textarea name='imgurl' rows=2 cols=75></textarea><br>";
			}
			else {
			echo "<textarea name='imgurl' rows=2 cols=75>".$picture."</textarea><br>";	
			}
			echo "<input type='submit' name='settingEdit' value='Save Changes'>";		
			echo "</form>";
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
		$mysql->close();

	}
?>


</div>
</body>
</html>
