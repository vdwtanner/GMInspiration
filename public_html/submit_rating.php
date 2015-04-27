<?php
	session_start();
	if(!$_SESSION["username"]){
		header("HTTP/1.1 412 You must be logged in to submit ratings.");
		die();
	}
	if(!$_POST["id"]){
		header("HTTP/1.1 412 No contribution ID found.");
		die();
	}
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	$comment=$_POST["comment"];
	//$balance= $_POST["bal"];
	//echo $balance ." ". $balance/2 ." </br>";
	echo $balance;
	try{
		$mysql->query("START TRANSACTION");
		//$mysql->query("INSERT INTO ratings (contribution_id, username, fun, balance, comment) VALUES (".$_POST["id"].", '".$_SESSION["username"]."', ".$_POST["fun"].", ".$_POST["bal"].", '".$comment."')");
		$stmt = $mysql->prepare("INSERT INTO ratings (contribution_id, username, fun, balance, comment) VALUES (?,?,?,?,?)");
		$stmt->bind_param("isiis", $_POST["id"], $_SESSION["username"], $_POST["fun"], $_POST["bal"], $comment);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$stmt->close();

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

		$stmt = $mysql->prepare("UPDATE contributions SET avg_fun=?, avg_balance=? WHERE id=?");
		$stmt->bind_param("ddi", $avgFun, $avgBalance, $_POST["id"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$stmt->close();

		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
		header("HTTP/1.1 500 MySL Error has occurred.");
		echo $e;
	}
	$mysql->close();
	echo "<b>Rating successful!</b>";
?>
