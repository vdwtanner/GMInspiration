<?php
	session_start();
?>

<?php
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	$usr = $mysql->real_escape_string($_POST["username"]);
	$pass = $mysql->real_escape_string(md5($_POST["pass"]));
	$msg;
	try{
		$mysql->query("START TRANSACTION");
		//$result = $mysql->query("SELECT * FROM `users` WHERE (username='".$usr."' OR email='".$usr."')");
		$stmt = $mysql->prepare("SELECT username FROM `users` WHERE (username=? OR email=?)");
		$stmt->bind_param("ss", $usr, $usr);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$username=null;
		$stmt->bind_result($username);
		$stmt->fetch();
		$stmt->close();
		if($username){
			$result=null;
			//$result = $mysql->query("SELECT * FROM `users` WHERE pass='".$pass."' AND (username='".$usr."' OR email='".$usr."')");
			$stmt = $mysql->prepare("SELECT username, active FROM `users` WHERE pass=? AND (username=? OR email=?)");
			$stmt->bind_param("sss", $pass, $usr, $usr);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$username=null; $active=null;
			$stmt->bind_result($username, $active);
			$stmt->fetch();
			$stmt->close();
			if($username){
				if($active){
					$msg = "<h4>Successful login!</h4>";
					$_SESSION["username"] = $username;
				}else{
					$msg= "<h4>This account is not active yet. Please check your inbox for the verification email. It may have been caught by your spam filter.</h4>";
				}
				$mysql->commit();
			}else{
				$msg="<h4>Incorrect password.";
			}
		}else{
			$msg= "<h4>I'm sorry, that username is invalid.</h4>";
		}
	}catch(Exception $e){
		echo "<h1 style='color:red'>An error occurred while saving data to the database</h1>";
		echo "<p>".$e."</p>";
		$mysql->rollback();
	}
	$mysql->close();
	if($msg!="<h4>Successful login!</h4>"){
		header("HTTP/1.1 412 ".$msg);
		die($msg);
	}else{
		echo $msg;
	}
?>
