<DOCTYPE html>
<html>
<head>
</head>
<body>
<?php
	//echo "<h2>".$_POST["username"]."</h2></br>";
	//echo "<h2>".$_POST["email"]."</h2></br>"; 
	//echo "<h2>".$_POST["pass"]."</h2></br>"; 
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	$usr = $mysql->real_escape_string($_POST["username"]);
	$email = $mysql->real_escape_string($_POST["email"]);
	$pass = $mysql->real_escape_string($_POST["pass"]);
	try{
		$mysql->query("START TRANSACTION");
		$result = $mysql->query("SELECT * FROM `users` WHERE username='".$usr."'");
		if(!$result->num_rows){
			$result->free();
			$result = $mysql->query("SELECT * FROM `users` WHERE email='".$email."'");
			if(!$result->num_rows){
				$result->free();
				$mysql->query("INSERT into users (username,pass,email) VALUES ('".$usr."','".$pass."','".$email."')");
				echo "<h1>Successful sign up!</h1>";
				$mysql->commit();
			}else{
				echo "<h4>I'm sorry, that email is already in use. Please go <a onclick='window.history.back()'>back</a> and use a new email.";
			}
		}else{
			echo "<h4>I'm sorry, that username is already taken. Please go <a onclick='window.history.back()'>back</a> and choose a new name.</h4>";
		}
	}catch(Exception $e){
		echo "<h1 style='color:red'>An error occurred while saving data to the database</h1>";
		echo "<p>".$e."</p>";
		$mysql->rollback();
	}
	$mysql->close();
?>
</body>
</html>