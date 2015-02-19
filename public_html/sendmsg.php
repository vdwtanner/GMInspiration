<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>
<?php
	if($_GET["redirect"] == "p")	
		echo "<meta http-equiv='refresh' content='2; url=profile.php?user=".$_GET["recipient"]."'/>";
	elseif($_GET["redirect"] == "i")
		echo "<meta http-equiv='refresh' content='2; url='inbox.php'/>";
	else
		echo "<meta http-equiv='refresh' content='2; url='home.php'/>";
	
?>
	

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="css/example/profile.css" media="all">
	<?php include "header.php";?>
</head>
<body>
<div id='container'>

<?php


	if($_SESSION["username"]){
		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}

		try{
			$emsgsub = htmlspecialchars($_POST["msgsubject"]);
			$emsgbody= htmlspecialchars($_POST["msgbody"]);


			
			$mysql->query("START TRANSACTION");
			$result = $mysql->query("INSERT INTO private_messages (sender, recipient, subject, message) VALUES ('".$_SESSION["username"]."','".$_GET["recipient"]."','".$emsgsub."','".$emsgbody."')");
			echo "<b>Your message was successfully sent! Yay!";
			
		}catch(Exception $e){
			echo "<b>An error occurred while trying to send your message! Please try again.</b>";
		}

	
	}else{
		echo "<b>You must log in before viewing this page.</b>";
	}


?>

</div>
</body>
