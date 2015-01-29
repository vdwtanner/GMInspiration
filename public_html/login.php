<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>
</head>
<body>
<?php
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	$usr = $mysql->real_escape_string($_POST["username"]);
	$pass = $mysql->real_escape_string($_POST["pass"]);
	try{
		$mysql->query("START TRANSACTION");
		$result = $mysql->query("SELECT * FROM `users` WHERE username='".$usr."'");
		if($result->num_rows){
			$result->free();
			$result = $mysql->query("SELECT * FROM `users` WHERE pass='".$pass."'");
			if($result->num_rows){
				$result->free();
				echo "<h1>Successful login!</h1>";
				$_SESSION["username"]=$usr;
				$mysql->commit();
			}else{
				echo "<h4>Incorrect password. Please go <a onclick='window.history.back()'>back</a> and try again.";
				session_unset();
				session_destroy();
			}
		}else{
			echo "<h4>I'm sorry, that username is invalid. Please go <a onclick='window.history.back()'>back</a> and try again.</h4>";
			session_unset();
			session_destroy();
		}
	}catch(Exception $e){
		echo "<h1 style='color:red'>An error occurred while reading data from the database</h1>";
		echo "<p>".$e."</p>";
		$mysql->rollback();
		session_unset();
		session_destroy();
	}
	$mysql->close();
?>
</body>
</html>