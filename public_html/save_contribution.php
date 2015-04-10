<?php
	session_start();
?>
<!------------------------------------------------------------------------------------------------------
 FILTER THE DATA WE GET FROM CONTRIBUTION.PHP IN HERE. IT IS NOT SAFE TO FILTER IN CONTRIBUTION.PHP,
	SINCE THE USER CAN JUST EDIT THE AJAX CALL TO INCLUDE UNFILTERED DATA. 

	NOTE: WE'RE FILTERING, NOT VALIDATING. VALIDATION IS BAD BECAUSE IT LETS THE POTENTIAL
		HACKER KNOW IF HIS INJECTION HAS WORKED OR NOT. THATS TOO MUCH INFO.
------------------------------------------------------------------------------------------------------->

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
		require_once dirname(__FILE__).'/HTMLPurifier/library/HTMLPurifier.auto.php';
		$purifier = new HTMLPurifier();

		echo "Hi, ".$_SESSION["username"];
		if(!$_SESSION["username"]){
			die("You must be logged in in order to access this part of the site.");
		}
		//echo "Welcome to the contribution screen, ".$_SESSION["username"];
		echo "</br>";
		$privacy=$purifier->purify($_POST["privacy"]);
		$name=$purifier->purify($_POST["name"]);	
		$game=$purifier->purify($_POST["game"]);	
		$type=$purifier->purify($_POST["type"]);	
		$subtype=$purifier->purify($_POST["subtype"]);	
		$desc=$purifier->purify($_POST["desc"]);	
		$img=$purifier->purify($_POST["img"]);
		$json=$purifier->purify($_POST["json"]);	


		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			if($img){
				//$mysql->query("INSERT INTO contributions (username, name, `type`, subtype, game, `desc`, img, json) VALUES ('".$mysql->real_escape_string($_SESSION["username"])."','".$name."','".$type."','".$subtype."','".$game."','".$desc."','".$img."','".$json."')");
				$stmt = $mysql->prepare("INSERT INTO contributions (username, name, `type`, sub_type, game, `desc`, img, json, privacy) VALUES (?,?,?,?,?,?,?,?,?)");
				$stmt->bind_param("ssssssssi", $_SESSION["username"], $name, $type, $subtype, $game, $desc, $img, $json, $privacy);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$stmt->close();
			}else{
				//$mysql->query("INSERT INTO contributions (username, name, `type`, subype, game, `desc`, json) VALUES ('".$mysql->real_escape_string($_SESSION["username"])."','".$name."','".$type."','".$subtype."','".$game."','".$desc."','".$json."')");
				$stmt = $mysql->prepare("INSERT INTO contributions (username, name, `type`, sub_type, game, `desc`, json, privacy) VALUES (?,?,?,?,?,?,?,?)");
				$stmt->bind_param("sssssssi", $_SESSION["username"], $name, $type, $subtype, $game, $desc, $json, $privacy);
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
