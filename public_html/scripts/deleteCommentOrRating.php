<?php
	session_start();
	/**For use by the commenter*/
	function deleteComment($id){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			$stmt=$mysql->prepare("UPDATE contribution_comments SET comment=?, username=? WHERE id=? AND username=?");
			$date = getdate();
			$comment= "[".$_SESSION["username"]." deleted their comment on ".$date["weekday"].", ".$date["month"]." ".$date["mday"].", ".$date["year"].".]";
			$user="GMInspiration.com";
			$stmt->bind_param("ssis",$comment, $user, $id, $_SESSION["username"]);
			if(!$stmt->execute()){
				echo "ERROR: MYSQL";
			}else if($stmt->affected_rows<1){
				header("HTTP/1.1 412 Precondition failed.");
			}
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
		$mysql->close();
		echo "Comment successfully deleted!";
	}
	
	/**For use by the commenter*/
	function deleteRating($id){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			$stmt=$mysql->prepare("DELETE FROM ratings WHERE id=? AND username=?");
			$stmt->bind_param("is", $id, $_SESSION["username"]);
			if(!$stmt->execute()){
				echo "ERROR: MYSQL";
			}else if($stmt->affected_rows<1){
				header("HTTP/1.1 412 Precondition failed.");
			}
			$stmt->close();
			
			//calc new averages
			$stmt = $mysql->prepare("SELECT COUNT(*), SUM(fun), SUM(balance) FROM ratings WHERE contribution_id=?");
			$stmt->bind_param("i", $_POST["id"]);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$c=null; $s=null; $b=null;
			$stmt->bind_result($c, $s, $b);
			$stmt->fetch();
			$stmt->close();
			$num_ratings=$c;
			if($c>0){
				$avgFun=$s/$c;
				$avgBalance=$b/$c;
			}

			$stmt = $mysql->prepare("UPDATE contributions SET avg_fun=?, avg_balance=?, ratings=? WHERE id=?");
			$stmt->bind_param("ddii", $avgFun, $avgBalance, $num_ratings, $_POST["id"]);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$stmt->close();
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
		echo "Rating successfully deleted!";
		$mysql->close();
	}
	
	if($_POST["type"]=="comment"){
		deleteComment($_POST["id"]);
	}else if($_POST["type"]=="rating"){
		deleteRating($_POST["id"]);
	}
?>