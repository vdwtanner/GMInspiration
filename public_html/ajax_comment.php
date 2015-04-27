<?php		
	require_once dirname(__FILE__).'/HTMLPurifier/library/HTMLPurifier.auto.php';
	$purifier = new HTMLPurifier();

	$user = $_POST["user"];
	$cid = $_POST["c_id"];
	$comment = $purifier->purify($_POST["comment"]);
	$img;
	//echo print_r($_POST);
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	try{
		$mysql->query("START TRANSACTION");

		//$mysql->query("INSERT INTO contribution_comments (contribution_id, username, comment) VALUES (".$cid.", '".$user."', '".$mysql->real_escape_string($comment)."')");
		$stmt = $mysql->prepare("INSERT INTO contribution_comments (contribution_id, username, comment) VALUES (?,?,?)");
		$stmt->bind_param("iss", $cid, $user, $comment);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$stmt->close();

		//$result=$mysql->query("SELECT picture FROM users WHERE username='".$user."'");
		$stmt = $mysql->prepare("SELECT picture FROM users WHERE username=?");
		$stmt->bind_param("s", $user);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$result = $stmt->bind_result($img);
		$stmt->fetch();		
		$stmt->close();

		$img = $purifier->purify($img);

		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}
	$mysql->close();

	$user = htmlspecialchars($user, ENT_QUOTES, "UTF-8");
	$comment = htmlspecialchars($comment, ENT_QUOTES, "UTF-8");

	echo "<div class='comment'><img src='".$img."' alt='".$user."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$user."</h4>";
	echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date("F j, Y g:i A")."</h5></div></br>";
	echo "<p style='padding: 0em; margin: 0em;'>".$comment."</p></div>";
?>
