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
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	$usr = $_POST["username"];
	$email = $_POST["email"];
	$pass = md5($_POST["pass"]);
//	$pass = crypt($_POST["pass"], "$2a$09$anexamplestringforsalt$");
	$hash = md5( rand(0,1000) ); // Generate random 32 character hash and assign it to a local variable.
	try{
		$mysql->query("START TRANSACTION");
		//$result = $mysql->query("SELECT * FROM `users` WHERE username='".$usr."'");
		$stmt = $mysql->prepare("SELECT email FROM users WHERE username=?");
		$stmt->bind_param("s", $usr);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$e = null;
		$stmt->bind_result($e);
		$stmt->fetch();
		$stmt->close();
		if(!$e){
			//$result = $mysql->query("SELECT * FROM `users` WHERE email='".$email."'");
			$stmt = $mysql->prepare("SELECT username FROM users WHERE email=?");
			$stmt->bind_param("s", $email);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$u = null;
			$stmt->bind_result($u);
			$stmt->fetch();			
			$stmt->close();
			if(!$u){
				//$mysql->query("INSERT into users (username,pass,email,hash, picture) VALUES ('".$usr."','".$pass."','".$email."','".$hash."', 'img/man_wearing_hat.svg')");
				$stmt = $mysql->prepare("INSERT into users (username, pass, email, hash, picture) VALUES (?,?,?,?,?)");
				$img = "img/man_wearing_hat.svg";			
				$stmt->bind_param("sssss", $usr, $pass, $email, $hash, $img);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				echo mysqli_errno($mysql) . ": " . mysqli_error($mysql). "\n";
				$stmt->close();
				sendVerifyMsg($usr, $email, $pass, $hash);
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
