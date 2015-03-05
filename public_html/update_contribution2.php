<?php
	session_start();
	require dirname(__FILE__)."/scripts/parser.php";
?>
<DOCTYPE html>
<html>
<head>
	<title>Contribution Update</title>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
</head>
<body>
	<?php
		if(!$_SESSION["username"]){
			header("HTTP/1.1 401 You are not logged in");
			die("You must be logged in in order to access this part of the site.");
		}
		if(!$_POST["id"]){
			print_r($_POST);
			//header("HTTP/1.1 412 Contribution ID not found");
			die("No ID found, cannot complete update.");
		}
		//echo "Welcome to the contribution screen, ".$_SESSION["username"];
		echo "</br>";
		$parser = new parser;
		$id=$_POST["id"];
		$name=htmlspecialchars($_POST["name"], ENT_QUOTES);
		$game=htmlspecialchars($_POST["game"]);
		$type=htmlspecialchars($_POST["type"]);
		$subtype=htmlspecialchars($_POST["subtype"]);
		$desc=($_POST["desc"]);
		//echo $desc;
		$img=$_POST["img"];
		if($game=="other"){
			$game=htmlspecialchars($_POST["other"]);
		}
		$json=($_POST["json"]);
		//echo $json;
		//print_r($array);
		//echo "</br>";
		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		//$json = $mysql->real_escape_string(json_encode($array));
		//print($json);
		try{
			$mysql->query("START TRANSACTION");
			if($img){
				//$stmt=$mysql->prepare("UPDATE contributions SET name=?");
				$stmt=$mysql->prepare("UPDATE contributions SET name=?, `type`=?, sub_type=?, game=?, `desc`=?, json=?, img=? WHERE id=?");
				//$stmt->bind_param("s", $name);
				$stmt->bind_param("sssssssd", $name, $type, $subtype, $game, $desc, $json, $img, $id);
				if(!$stmt->execute()){
					header("HTTP/1.1 500 Failed to execute update command1.</br> (".$stmt->errno.") ".$stmt->error);
				}
				
				//$mysql->query("UPDATE contributions SET name='".$name."', `type`='".$type."', sub_type='".$subtype."', game='".$game."', `desc`='".$desc."', json='".$json."', img='".$img."' WHERE id=".$id."");
			}else{
				$stmt=$mysql->prepare("UPDATE contributions SET name=?, `type`=?, sub_type=?, game=?, `desc`=?, json=? WHERE id=?");
				$stmt->bind_param("ssssssd", $name, $type, $sub_type, $game, $desc, $json, $id);
				if(!$stmt->execute()){
					header("HTTP/1.1 500 Failed to execute update command2.</br> (".$stmt->errno.") ".$stmt->error);
				}
				//$mysql->query("UPDATE contributions SET name='".$name."', `type`='".$type."', sub_type='".$subtype."', game='".$game."', `desc`='".$desc."', json='".$json."' WHERE id=".$id."");
			}
			echo $_SESSION["username"].", Your contribution was successfully updated.";
			$mysql->commit();
			$stmt->close();
		}catch(Exception $e){
			$mysql->rollback();
			header("HTTP/1.1 500 Unexpected Error occurred.");
			die($e);
		}
		$mysql->close();
	?>
</body>
</html>