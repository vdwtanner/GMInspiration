<?php
	session_start();
	if($_SESSION["username"]){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$msgrecip = $_POST["msgrecipient"];
			$msgsub = $_POST["msgsubject"];
			$msgbody= $_POST["msgbody"];
			$mysql->query("START TRANSACTION");
			//$result = $mysql->query("INSERT INTO private_messages (sender, recipient, subject, message) VALUES ('".$_SESSION["username"]."','".$_GET["recipient"]."','".$emsgsub."','".$emsgbody."')");
			
			$stmt = $mysql->prepare("INSERT INTO private_messages (sender, recipient, subject, message) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $_SESSION["username"], $msgrecip, $msgsub, $msgbody);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$stmt->close();
			echo "Your message was successfully sent! Yay!";
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
			header("HTTP/1.1 500 Unexpected Error occurred.");
			echo "<b>An error occurred while trying to send your message! Please try again.</b>";
		}
	}else{
		print_r($_SESSION);
		header("HTTP/1.1 412 You must be logged in to send a message.");
	}
?>