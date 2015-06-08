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
	$id = $_POST["id"];

	try{
		$stmt = $mysql->prepare("UPDATE collections SET name=?, game=?, img=? WHERE id=? AND username=?");
		$stmt->bind_param("sssis", $name, $game, $img, $id, $_SESSION["username"]);
		if(!$stmt->execute()){
			header("HTTP/1.1 412 Precondition failed.");
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$stmt->close();
		echo "Your collection \"".htmlspecialchars($name, ENT_QUOTES, "UTF-8")."\" was successfully updated!";
		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
		echo "Whoops, something went wrong.";
	}

?>

</body>
</html>