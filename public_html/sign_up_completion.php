<?php
	function sendVerifyMsg($to,$usr,$pass,$hash){
		//echo "<h2>TO:".$to."</h2>";
		$subject="GMInspiration Account Activation";
		$message="Thanks for signing up!<br>
		Your account has been created, you can login with the following credentials after activating the account with the URL below:<br><br>
		
		----------------------------<br>
		Username: ".$usr."<br>
		For security purposes, your password is not shown here.<br>
		----------------------------<br>
		Please click this link to activate your account:<br>
		<a href='http://gminspiration.com/verify.php?email=".$to."&hash=".$hash."' target='_blank'>http://gminspiration.com/verify.php?email=".$to."&hash=".$hash."</a><br>
		
		";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Account Verification <notify@gminspiration.com>' . "\r\n";
		//$message=wordwrap($message, 70);
		$success = mail($to,$subject,$message,$headers);
		if($success){
			//echo "<h4>Mail Sent</h4>";
		}else{
			header("HTTP/1.1 500 There was an Error with the Mailing System");
			echo "<h4>There was an Error with the Mailing System</h4>";
		}
	}


	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	/******************************************
	   Lets get our sign up data and validate
	*******************************************/
	//$usrvalid1 = "/^[a-zA-Z_.-]+$/";
	$usrvalid1 = "/^[a-zA-Z]+[\w_.-]+/";
	$usrvalid2 = "/^[^\s]+$/";
	$usr = $_POST["username"];
	$email = $_POST["email"];
	$pass = md5($_POST["pass"]);
//	$pass = crypt($_POST["pass"], "$2a$09$anexamplestringforsalt$");
	$hash = md5( rand(0,1000) ); // Generate random 32 character hash and assign it to a local variable.
	if(preg_match($usrvalid1, $usr) && preg_match($usrvalid2, $usr) && strlen($usr) >= 3 && strlen($usr) <= 20){
		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
		try{
	/******************************************
		Check if the email is taken
	*******************************************/
			$mysql->query("START TRANSACTION");
			//$result = $mysql->query("SELECT * FROM `users` WHERE username='".$usr."'");
			$stmt = $mysql->prepare("SELECT email FROM users WHERE username=?");
			$stmt->bind_param("s", $usr);
			if(!$stmt->execute()){
				header("HTTP/1.1 500 Database Error");
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$e = null;
			$stmt->bind_result($e);
			$stmt->fetch();
			$stmt->close();
			if(!$e){
	/******************************************
		Check if the username is taken
	*******************************************/
				//$result = $mysql->query("SELECT * FROM `users` WHERE email='".$email."'");
				$stmt = $mysql->prepare("SELECT username FROM users WHERE email=?");
				$stmt->bind_param("s", $email);
				if(!$stmt->execute()){
					header("HTTP/1.1 500 Database Error");
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$u = null;
				$stmt->bind_result($u);
				$stmt->fetch();			
				$stmt->close();
				if(!$u){
	/******************************************
		If not, register the account
	*******************************************/
					//$mysql->query("INSERT into users (username,pass,email,hash, picture) VALUES ('".$usr."','".$pass."','".$email."','".$hash."', 'img/man_wearing_hat.svg', 'img/hat_profile200.png')");
					$stmt = $mysql->prepare("INSERT into users (username, pass, email, hash, picture) VALUES (?,?,?,?,?)");
					$img = "img/hat_profile200.png";
					//$img = "img/man_wearing_hat.svg";			
					$stmt->bind_param("sssss", $usr, $pass, $email, $hash, $img);
					if(!$stmt->execute()){
						header("HTTP/1.1 500 Database Error");
						echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
					}
					//echo mysqli_errno($mysql) . ": " . mysqli_error($mysql). "\n";
					$stmt->close();
					// send an email to verify the users email account.
					sendVerifyMsg($email, $usr, $pass, $hash);
					echo "<b>Successful signup. Check your inbox and spam filter for an activation email</b>";
					$mysql->commit();
				}else{
					header("HTTP/1.1 412 I'm sorry, that email is already in use.");
					//echo "<h4>I'm sorry, that email is already in use. Please go <a onclick='window.history.back()'>back</a> and use a new email.";
				}
			}else{
				header("HTTP/1.1 412 I'm sorry, that username is already taken.");
				//echo "<h4>I'm sorry, that username is already taken. Please go <a onclick='window.history.back()'>back</a> and choose a new name.</h4>";
			}
		}catch(Exception $e){
			header("HTTP/1.1 500 An error occurred while saving data to the database<");
			//echo "<h1 style='color:red'>An error occurred while saving data to the database</h1>";
			echo "<p>".$e."</p>";
			$mysql->rollback();
		}
		}else{
			header("HTTP/1.1 412 Invalid email");
			echo "<h2>Invalid Email</h2>";
		}
	}else{
		header("HTTP/1.1 412 Invalid username: ".$usr."<br>Usernames must start with a letter and contain only letters, numbers, underscores, and periods.");
		echo "<h2>Invalid Username</h2>";
	}
	$mysql->close();
?>
</div>
</body>
</html>
