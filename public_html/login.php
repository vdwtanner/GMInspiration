<?php
	session_start();
?>

<?php
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	$usr = $mysql->real_escape_string($_POST["username"]);
	$pass = $mysql->real_escape_string(md5($_POST["pass"]));
	$msg;
	try{
		$mysql->query("START TRANSACTION");
		$result = $mysql->query("SELECT * FROM `users` WHERE (username='".$usr."' OR email='".$usr."')");
		if($result->num_rows){
			$result->free();
			$result = $mysql->query("SELECT * FROM `users` WHERE pass='".$pass."' AND (username='".$usr."' OR email='".$usr."')");
			if($result->num_rows){
				$row=$result->fetch_array(MYSQLI_BOTH);
				$result->free();
				if($row["active"]){
					$msg = "<h4>Successful login!</h4>";
					$_SESSION["username"] = $row["username"];
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
