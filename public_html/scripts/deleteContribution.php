<?php
	session_start();
	$id=$_POST["id"];
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
    if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }
	try{
		$mysql->query("START TRANSACTION");
		$stmt = $mysql->prepare("SELECT username from contributions WHERE id=?");
		$stmt->bind_param("i",$id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$creator=null;
		$stmt->bind_result($creator);
		$stmt->fetch();
		if($_SESSION["username"]!=$creator){
			die("Username mismatch");
		}
		$count=0;
		$stmt->close();
		//delete ratings
		$stmt=$mysql->prepare("DELETE from ratings WHERE contribution_id=?");
		$stmt->bind_param("i",$id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$count+=$mysql->affected_rows;
		$stmt->close();
		//delete comments
		$stmt=$mysql->prepare("DELETE from contribution_comments WHERE contribution_id=?");
		$stmt->bind_param("i",$id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$count+=$mysql->affected_rows;
		$stmt->close();
		//delete contribution
		$stmt=$mysql->prepare("DELETE from contributions WHERE id=?");
		$stmt->bind_param("i",$id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$count+=$mysql->affected_rows;
		$stmt->close();
		if(count==1){
			echo $count." entry deleted."
		}else{
			echo $count." entries deleted.";
		}
	}catch(Exception $e){
		$mysql->rollback();
        header("HTTP/1.1 500 Unexpected Error occurred.");
        die($e);
    }
    $mysql->close();
?>