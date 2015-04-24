<?php
	session_start();
	$id=$_POST["id"];
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
    if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }
	try{
		$mysql->query("START TRANSACTION");
		$stmt = $mysql->prepare("SELECT username, privacy from contributions WHERE id=?");
		$stmt->bind_param("i",$id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$creator=null; $privacy=null;
		$stmt->bind_result($creator, $privacy);
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
		if($privacy==0){//only decrement contributions count if it was public (aka, it was included in the count)
			$stmt=$mysql->prepare("SELECT contributions FROM users WHERE username=?");
			$stmt->bind_param("s",$_SESSION["username"]);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$num=null;
			$stmt->bind_result($num);
			$stmt->fetch();
			echo "num contributiuons: ".$num;
			$num--;
			$stmt->close();
			$stmt=$mysql->prepare("UPDATE users SET contributions=? WHERE username=?");
			$stmt->bind_param("is",$num,$_SESSION["username"]);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$stmt->close();
		}
		if(count==1){
			echo $count." entry deleted.";
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