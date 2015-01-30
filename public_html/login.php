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
	$pass = $mysql->real_escape_string(md5($_POST["pass"]));
	try{
		$mysql->query("START TRANSACTION");
		$result = $mysql->query("SELECT * FROM `users` WHERE username='".$usr."'");
		if($result->num_rows){
			$result->free();
			$result = $mysql->query("SELECT * FROM `users` WHERE pass='".$pass."'");
			if($result->num_rows){
				$row=$result->fetch_array(MYSQLI_BOTH);
				$result->free();
				if($row["active"]){
					echo "<h1>Successful login!</h1>";
				}else{
					echo "<h4>This account is not active yet. Please check your inbox for the verification email. It may have been caught by your spam filter.</h4>";
				}
				$mysql->commit();
			}else{
				echo "<h4>Incorrect password. Please go <a onclick='window.history.back()'>back</a> and try again.";
			}
		}else{
			echo "<h4>I'm sorry, that username is invalid. Please go <a onclick='window.history.back()'>back</a> and try again.</h4>";
		}
	}catch(Exception $e){
		echo "<h1 style='color:red'>An error occurred while saving data to the database</h1>";
		echo "<p>".$e."</p>";
		$mysql->rollback();
	}
	$mysql->close();
?>
	<a href="index.html"><button style="radius: 4px;">Home</button></a>
</body>
</html>