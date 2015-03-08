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
			$msgsub = stripslashes($_POST["msgsubject"]);
			$msgbody= stripslashes($_POST["msgbody"]);


			
			$mysql->query("START TRANSACTION");
			//$result = $mysql->query("INSERT INTO private_messages (sender, recipient, subject, message) VALUES ('".$_SESSION["username"]."','".$_GET["recipient"]."','".$emsgsub."','".$emsgbody."')");
			
			$stmt = $mysql->prepare("INSERT INTO private_messages (sender, recipient, subject, message) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $_SESSION["username"], $_GET["recipient"], $msgsub, $msgbody);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$stmt->close();


			
			echo "<b>Your message was successfully sent! Yay!";
			
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
			echo "<b>An error occurred while trying to send your message! Please try again.</b>";
		}

	
	}else{
		echo "<b>You must log in before viewing this page.</b>";
	}


?>

</div>
</body>
