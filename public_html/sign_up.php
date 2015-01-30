<DOCTYPE html>
<html>
<head>
	<title>Sign up</title>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
</head>
<body>
<?php
	function sendVerifyMsg($to,$usr,$pass,$hash){
		echo "<h2>TO:".$to."</h2>";
		$subject="Dungeon Crawlers Activation";
		$message="Thanks for signing up!
		Your account has been created, you can login with the following credentials after activating the account with the URL below:
		
		----------------------------
		Username: ".$usr."
		Password: ".$pass."
		----------------------------
		Please click this link to activate your account:
		http://dungeoncrawlers.webuda.com/verify.php?email=".$to."&hash=".$hash."
		
		";
		$headers="From:notify@dungeoncrawlers.webuda.com" . "\r\n";
		mail($to,$subject,$message,$headers);
		echo "<h4>Mail Sent</h4>";
	}
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	$usr = $mysql->real_escape_string($_POST["username"]);
	$email = $mysql->real_escape_string($_POST["email"]);
	$pass = $mysql->real_escape_string(md5($_POST["pass"]));
	$hash = md5( rand(0,1000) ); // Generate random 32 character hash and assign it to a local variable.
	try{
		$mysql->query("START TRANSACTION");
		$result = $mysql->query("SELECT * FROM `users` WHERE username='".$usr."'");
		if(!$result->num_rows){
			$result->free();
			$result = $mysql->query("SELECT * FROM `users` WHERE email='".$email."'");
			if(!$result->num_rows){
				$result->free();
				$mysql->query("INSERT into users (username,pass,email,hash) VALUES ('".$usr."','".$pass."','".$email."','".$hash."')");
				sendVerifyMsg($email,$_POST["username"],$_POST["pass"],$hash);
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
	<a href="index.html"><button style="border-radius: 4px;">Home</button></a>
</body>
</html>