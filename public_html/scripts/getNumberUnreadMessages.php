<?php
	session_start();
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}
	if(!$_SESSION["username"]){
		die("Not logged in");
	}
	try{
		$mysql->query("START TRANSACTION");
		//$result=$mysql->query("SELECT COUNT(*) FROM `private_messages` WHERE `recipient`='".$_SESSION["username"]."' AND `read`=0");
		$stmt = $mysql->prepare("SELECT COUNT(*) FROM `private_messages` WHERE `recipient`=? AND `read`=0");
		$stmt->bind_param("s", $_SESSION["username"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$msgCount=0;
		$stmt->bind_result($msgCount);
		$stmt->fetch();
		$stmt->close();
		//echo "SELECT COUNT(*) FROM `private_messages` WHERE `recipient`='".$_SESSION["username"]."' AND `read`=0";
		echo "Inbox";
		echo ($msgCount>0)?"[".$msgCount." unread]":"";
	}catch(Exception $e){
		die("ERROR READING MESSAGES");
	}
	$mysql->close();
?>
