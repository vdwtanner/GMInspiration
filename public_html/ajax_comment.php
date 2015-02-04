<?php
	$user = $_POST["user"];
	$cid = $_POST["c_id"];
	$comment = $_POST["comment"];
	$img;
	//echo print_r($_POST);
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	try{
		$mysql->query("START TRANSACTION");
		$mysql->query("INSERT INTO contribution_comments (contribution_id, username, comment) VALUES (".$cid.", '".$user."', '".$comment."')");
		//echo "WE GOOD";
		$result=$mysql->query("SELECT picture FROM users WHERE username='".$user."'");
		$row=$result->fetch_array(MYSQL_BOTH);
		$img=$row["picture"];
		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}
	$mysql->close();
	echo "<img src='".$img."' alt='".$user."&#39s profile picture' width='50' height='50'><div id='namedate'><h4 style='padding-left: 1em;'>".$user."</h4>";
	echo "<h5>".date("F j, Y g:i A")."</h5></div>";
	echo "<p>".$comment."</p>";
?>