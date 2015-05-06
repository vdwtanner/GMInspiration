<?php
	session_start();
	function changePassword(){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$stmt=$mysql->prepare("SELECT pass FROM users WHERE username=? AND pass=?");
		$stmt->bind_param("si", $_SESSION["username"], md5($_POST["pass"]));
		if(!$stmt->execute()){
				echo "ERROR: MYSQL";
		}
		$u=null;
		$stmt->bind_result($u);
		$stmt->fetch();
		if(empty($u)){
			header("HTTP/1.1 412 Password incorrect.");
			die($_SESSION["username"].": ".$_POST["pass"]);
		}
		$stmt->close();
		if(!$_POST["newPass"]==$_POST["confirmPass"]){
			header("HTTP/1.1 412 New password doesn't match confirmation password.");
			die();
		}else if(strlen($_POST["newPass"])<6 || strlen($_POST["newPass"])>25){
			header("HTTP/1.1 412 Password must be between 6 and 25 characters");
			die();
		}else{
			try{
				$mysql->query("START TRANSACTION");
				$stmt=$mysql->prepare("UPDATE users SET pass=? WHERE username=? AND pass=?");
				$stmt->bind_param("sss", md5($_POST["newPass"]), $_SESSION["username"], md5($_POST["pass"]));
				if(!$stmt->execute()){
					echo "ERROR: MYSQL";
				}else if($stmt->affected_rows<1){
					header("HTTP/1.1 412 Precondition failed.");
				}
				$stmt->close();
				$mysql->commit();
				echo "Your password has been changed!";
			}catch(Exception $e){
				$mysql->rollback();
			}
		}
		$mysql->close();
	}
	
	if($_POST["funct"]=="changePassword"){
		changePassword();
	}
?>