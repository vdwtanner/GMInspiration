<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>
	<title>Contribute</title>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
</head>
<body>
	<h2>Dungeon Crawlers - Contribute</h2>
	<?php
		echo "Hi, ".$_SESSION["username"];
		if(!$_SESSION["username"]){
			die("You must be logged in in order to access this part of the site.");
		}
		//echo "Welcome to the contribution screen, ".$_SESSION["username"];
		echo "</br>";
		//print_r($_POST);
		//echo "</br>";
		$name=stripslashes($_POST["name"]);
		print_r($name);
		$name=htmlspecialchars($_POST["name"], ENT_QUOTES);
		$game=htmlspecialchars($_POST["game"]);
		$type=htmlspecialchars($_POST["type"]);
		$subtype=htmlspecialchars($_POST["subtype"]);
		$desc=($_POST["desc"]);
		$img=$_POST["img"];
		$json=($_POST["json"]);

		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			if($img){
				//$mysql->query("INSERT INTO contributions (username, name, `type`, sub_type, game, `desc`, img, json) VALUES ('".$mysql->real_escape_string($_SESSION["username"])."','".$name."','".$type."','".$subtype."','".$game."','".$desc."','".$img."','".$json."')");
				$stmt = $mysql->prepare("INSERT INTO contributions (username, name, `type`, sub_type, game, `desc`, img, json) VALUES (?,?,?,?,?,?,?,?)");
				$stmt->bind_param("ssssssss", $_SESSION["username"], $name, $type, $sub_type, $game, $desc, $img, $json);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$stmt->close();
			}else{
				//$mysql->query("INSERT INTO contributions (username, name, `type`, sub_type, game, `desc`, json) VALUES ('".$mysql->real_escape_string($_SESSION["username"])."','".$name."','".$type."','".$subtype."','".$game."','".$desc."','".$json."')");
				$stmt = $mysql->prepare("INSERT INTO contributions (username, name, `type`, sub_type, game, `desc`, json) VALUES (?,?,?,?,?,?,?)");
				$stmt->bind_param("sssssss", $_SESSION["username"], $name, $type, $sub_type, $game, $desc, $json);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$stmt->close();
			}
			echo $_SESSION["username"].", Your contribution was successfully added.";
	

			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
			echo "An error occurred while saving your contribution.</br>".$e;
		}
		$mysql->close();
	?>
	<a href="index.html"><button style="border-radius: 10px;">Home</button></a>
</body>
</html>
