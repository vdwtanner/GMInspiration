<?php
	$user = $_POST["user"];
	$cid = $_POST["c_id"];
	$comment = htmlspecialchars($_POST["comment"], ENT_QUOTES);
	$img;
	//echo print_r($_POST);
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	try{
		$mysql->query("START TRANSACTION");
		$mysql->query("INSERT INTO contribution_comments (contribution_id, username, comment) VALUES (".$cid.", '".$user."', '".$mysql->real_escape_string($comment)."')");
		//echo "WE GOOD";
		$result=$mysql->query("SELECT picture FROM users WHERE username='".$user."'");
		$row=$result->fetch_array(MYSQL_BOTH);
		$img=$row["picture"];
		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}
	$mysql->close();
	echo "<div class='comment'><img src='".$img."' alt='".$user."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$user."</h4>";
	echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date("F j, Y g:i A")."</h5></div></br>";
	echo "<p style='padding: 0em; margin: 0em;'>".$comment."</p></div>";
?>