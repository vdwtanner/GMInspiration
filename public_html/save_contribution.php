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
		$name=$_POST["name"];
		$game=$_POST["game"];
		$type=$_POST["type"];
		$wtype=$_POST["wtype"];
		$desc=$_POST["desc"];
		$img=$_POST["img"];
		if($game=="other"){
			$game=$_POST["other"];
		}
		$loc = 0;
		$extra=0;
		$array=array();
		foreach($_POST as $key => $item){
			$key=str_replace('_', ' ', $key);
			if($loc++>5){
				$array[$key] = $item;
				if(preg_match("[label .+]",$key))
					$extra++;
			}
		}
		for	($x=1; $x<=$extra; $x++){
			$key=$array["label ".$x];
			$value=$array["text ".$x];
			unset($array["label ".$x]);
			unset($array["text ".$x]);
			$array[$key]=$value;
		}
		//print_r($array);
		echo "</br>";
		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$json = $mysql->real_escape_string(json_encode($array));
		//print($json);
		try{
			$mysql->query("START TRANSACTION");
			//echo "Query text: INSERT INTO `contributions` (`id`, `username`, `name`, `type`, `wtype`, `game`, `desc`, `img`, `json`, `uses`, `timestamp`) VALUES (NULL, '".$mysql->real_escape_string($_SESSION["username"])."','".$name."','".$type."','".$wtype."','".$game."','".$desc."', NULL, '".$json."', '0', CURRENT_TIMESTAMP";
			if($img){
				$mysql->query("INSERT INTO contributions (username, name, `type`, wtype, game, `desc`, img, json) VALUES ('".$mysql->real_escape_string($_SESSION["username"])."','".$name."','".$type."','".$wtype."','".$game."','".$desc."','".$img."','".$json."')");
			}else{
				$mysql->query("INSERT INTO contributions (username, name, `type`, wtype, game, `desc`, json) VALUES ('".$mysql->real_escape_string($_SESSION["username"])."','".$name."','".$type."','".$wtype."','".$game."','".$desc."','".$json."')");
			}
			echo $_SESSION["username"].", Your contribution was successfully added.";
			$mysql->commit();
		}catch(Exception $e){
			echo "An error occurred while saving your contribution.</br>".$e;
		}
		$mysql->close();
	?>
	<a href="index.html"><button style="border-radius: 10px;">Home</button></a>
</body>
</html>