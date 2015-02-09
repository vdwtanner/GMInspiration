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

	$display = 1;
	if($_SESSION["username"]){
		$username=$_SESSION["username"];
	}else{
		$display = 0;	
	}

	if($display == 1){
		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}

		try{
			$mysql->query("START TRANSACTION");
			$result = $mysql->query("SELECT * from users where username='".$username."'");

			$row = $result->fetch_array(MYSQL_BOTH);
			echo "<form method='POST' action='profile.php'>";
			echo "Description<br>";
			echo "<textarea name='descrEdit' rows=7 cols=75>".$row["description"]."</textarea><br>";
			echo "Profile Image Data URL<br>";
			echo "<textarea name='imgurl' rows=2 cols=75>".$row["picture"]."</textarea><br>";	
			echo "<input type='submit' name='settingEdit' value='Save Changes'>";		
			echo "</form>";
		}catch(Exception $e){
					
		}

	}
?>


</div>
</body>
</html>
