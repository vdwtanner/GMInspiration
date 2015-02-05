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

	function saveSettings(){
		global $username;

		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			$result = $mysql->query("UPDATE users SET description='".$_POST["descrEdit"]."' where username='".$username."'");
			$result = $mysql->query("UPDATE users SET picture='".$_POST["imgurl"]."' where username='".$username."'");
		}catch(Exception $e){

		}

	}
	$display = true;
	
	if($_GET["user"]){
		$username=$_GET["user"];
	}else if($_SESSION["username"]){
		$username=$_SESSION["username"];
	}else{
		echo "<h4>Please log in! or else im afraid this page isnt terribly interesting.</h4>";
		$display = false;	
	}

	// IF WE'RE RETURNING FROM THE EDIT SETTINGS PAGE
	if($_POST["settingEdit"]){
		saveSettings();
		unset($_POST["settingEdit"]);
	}

	if($display){
		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
	


		try{
			$mysql->query("START TRANSACTION");
			$result = $mysql->query("SELECT * from users where username='".$username."'");

			$row = $result->fetch_array(MYSQL_BOTH);

			//IMG and PROFILE NAME
			echo "<img src='".$row["picture"]."' style='float:left' height='100' width='100' alt='An image depicting ".$row["username"]."' />";
			echo "<div id='namedate'>";
			echo "<h2 style=''>".$row["username"]."</h2>";
			echo "<h4> User since ".$row["joined"];
			echo "<div id='pm'>";
			if($username == $_SESSION["username"])
				echo "<a href='profilesettings.php'>edit your profile settings</a>";
			else
				echo "<a href='privatemessage.php'>Send this user a private message</a>";
			echo "</div>";
			echo "</div>";

			//PLAYER DESCRIPTION
			echo "<div class='boxele'>";
			echo "<div style='padding-left: 2em; padding-top: 1em'";
			if($row["description"])
				echo "<p>".$row["description"]."</p>";
			else
				echo "<p>There doesn't seem to be anything here. :(</p>";
			echo "</div>";
			echo "</div>";
		
			//PLAYER CONTRIBUTIONS
			$cresult = $mysql->query("SELECT * from contributions where username='".$username."'");	
		
			while($crow = $cresult->fetch_assoc()){
				$crowarr[] = $crow;			
			}		
		
			echo "<div class='boxele'>";
			echo "<div style='padding-left: 2em'>";
			echo "<h5>Contributions</h5>";
			if($crowarr){
				foreach($crowarr as $key => $value){
					echo "<a href='view_contribution.php?contid=".$value["id"]."'>".$value["name"]."</p>";
				}
			}else{
				echo $username." has yet to submit any contributions!";
			}
			echo "</div>";
			echo "</div>";
		

		}catch(Exception $e){

		}

	}

?>
</div>
</body>
</html>
