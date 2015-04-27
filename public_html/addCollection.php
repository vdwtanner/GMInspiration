<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>

</head>
<body>
	
<?php
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	
	$name = $_POST["name"];
	$game = $_POST["game"];
	$img = $_POST["img"];

	try{
		$stmt = $mysql->prepare("INSERT INTO collections (name, username, game, img) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss", $name, $_SESSION["username"], $game, $img);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$stmt->close();
		echo "Your collection \"".htmlspecialchars($name, ENT_QUOTES, "UTF-8")."\" was successfully created!";
		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
		echo "Whoops, something went wrong.";
	}

?>

</body>
</html>
