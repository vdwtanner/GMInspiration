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
		$result=$mysql->query("SELECT COUNT(*) FROM `private_messages` WHERE `recipient`='".$_SESSION["username"]."' AND `read`=0");
		//echo "SELECT COUNT(*) FROM `private_messages` WHERE `recipient`='".$_SESSION["username"]."' AND `read`=0";
		$row=$result->fetch_array(MYSQL_BOTH);
		echo "Inbox [".$row[0]." message";
		echo ($row[0]==1)?"]":"s]";
	}catch(Exception $e){
		die("ERROR READING MESSAGES");
	}
	$mysql->close();
?>