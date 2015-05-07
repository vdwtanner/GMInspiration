<?php
	session_start();
	include "header.php";
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
	require_once dirname(__FILE__).'/HTMLPurifier/library/HTMLPurifier.auto.php';


	function saveSettings(){

		$purifier = new HTMLPurifier();
		global $username;

		$imgurl = $purifier->purify($_POST["imgurl"]);

		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$edescrEdit = $_POST["descrEdit"];
			if ($imgurl==""){
				$imgurl="img/hat_profile200.png";
			} 
			$mysql->query("START TRANSACTION");
			//$result = $mysql->query("UPDATE users SET description='".$edescrEdit."' where username='".$username."'");
			$stmt = $mysql->prepare("UPDATE users SET description=?, picture=? WHERE username=?");
			$stmt->bind_param("sss", $edescrEdit, $imgurl, $username);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$stmt->close();
			//$result = $mysql->query("UPDATE users SET picture='".$_POST["imgurl"]."' where username='".$username."'");

			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
		$mysql->close();

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
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
	


		try{
			$mysql->query("START TRANSACTION");
			//$result = $mysql->query("SELECT * from users where username='".$username."'");
			$stmt = $mysql->prepare("SELECT picture, joined, description from users where username=?");
			$stmt->bind_param("s", $username);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$picture = null; $joined = null; $description = null;
			$stmt->bind_result($picture, $joined, $description);
			$stmt->fetch();
			$stmt->close();

			$picture = htmlspecialchars($picture, ENT_QUOTES, "UTF-8");
			$description = htmlspecialchars($description, ENT_QUOTES, "UTF-8");
			$d_username = htmlspecialchars($username, ENT_QUOTES, "UTF-8");

			//IMG and PROFILE NAME
			echo "<img src='".$picture."' style='float:left' height='100' width='100' alt='An image depicting ".$d_username."' />";
			echo "<div id='namedate'>";
			echo "<h2 style=''>".$d_username."</h2>";
			echo "<h4> User since ".$joined;
			echo "<div id='pm'>";
			if($username == $_SESSION["username"])
				echo "<a href='profilesettings.php'>edit your profile settings</a>";
			else
				echo "<a href='composemsg.php?recipient=".$d_username."&redirect=p'>Send this user a private message</a>";
			echo "</div>";
			echo "</div>";

			//PLAYER DESCRIPTION
			echo "<div class='boxele'>";
			echo "<div style='padding-left: 2em; padding-top: 1em'";
			if($description)
				echo "<p>".$description."</p>";
			else
				echo "<p>There doesn't seem to be anything here. :(</p>";
			echo "</div>";
			echo "</div>";
		
			//PLAYER CONTRIBUTIONS
			//$cresult = $mysql->query("SELECT * from contributions where username='".$username."'");
			$stmt = $mysql->prepare("SELECT id, name, privacy FROM contributions WHERE username=?");
			$stmt->bind_param("s", $username);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}	
			$id=null; $name=null; $privacy=null;
			$stmt->bind_result($id, $name, $privacy);
			$stmt->fetch();

			$name = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
		
			echo "<div class='boxele'>";
			echo "<div style='padding-left: 2em'>";
			echo "<h5>Contributions</h5>";
			if($id){
				while($stmt->fetch()){
					if($_SESSION["username"]!=$username && $privacy!=0){
						echo "You don't have permission to see this contribution.";
					}else{
						echo "<a href='view_contribution_updateable.php?contid=".$id."'>".$name."</a>";
					}
					echo "<br><br>";
				}
			}else{
				echo $d_username." has yet to submit any contributions!";
			}
			echo "</div>";
			echo "</div>";
			$stmt->close();	

			
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
<?php include 'footer.php';?>