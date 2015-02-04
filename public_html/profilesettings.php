<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="css/example/profile.css" media="all">
	<div id='container'>
	<a id='homelink' href="index.html">Dungeon Crawlers</a>

	<style>
		a {
			cursor: pointer;
		}
		a.button {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #FFAD33;
			border-radius: 10px;
		}
		a.button:hover {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #EC9C2E;
			border-radius: 10px;
		}
		a.button:active {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #E8A643;
			border-radius: 10px;
		}
	</style>
</head>
<body>


	<?php

		$display = 1;
		echo "<hr>";
		if($_SESSION["username"]){
			$username=$_SESSION["username"];
			echo "<h4>Welcome, ".$_SESSION["username"]."</h4>";
		}else{
			echo "<a href='login.html'>Please log in! or else im afraid this page isnt terribly interesting.</a>";
			$display = 0;	
		}

		echo "<hr>";
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
				echo "<input type='submit' name='settingEdit' value='Save Changes'>";		
				echo "</form>";
			}catch(Exception $e){
						
			}

		}
	?>



</body>
</html>
