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
	
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	function resetPassword(){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$email=$_POST["email"];
		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
			try{
				$mysql->query("START TRANSACTION");
				$pass=generateRandomString();
				$stmt=$mysql->prepare("UPDATE users SET pass=? WHERE email=?");
				$stmt->bind_param("ss", md5($pass), $email);
				if(!$stmt->execute()){
					echo "ERROR: MYSQL";
				}else if($stmt->affected_rows<1){
					header("HTTP/1.1 412 Invalid email.");
				}else{
					$subject= "GMInspiration Password Reset";
					$message= "Your password has been reset to:<br><br>".$pass."<br><br>Please <a href='http://gminspiration.com' target='_blank'>log into your account</a> and change it as soon as possible.<br>";
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: Password Reset <notify@gminspiration.com>' . "\r\n";
					//$message=wordwrap($message, 70);
					$success = mail($email,$subject,$message,$headers);
					if($success){
						$mysql->commit();
						echo "Password reset. Please check your email for your temporary password.";
					}else{
						$mysql->rollback();
						echo "Message failed to send. Please try again.";
					}
				}
				$stmt->close();
			}catch(Exception $e){
				echo "An error occurred:<br>".$e;
				$mysql->rollback();
			}
			$mysql->close();
		}
		
	}
	
	if($_POST["funct"]=="changePassword"){
		changePassword();
	}
	if($_POST["funct"]=="resetPassword"){
		resetPassword();
	}
?>