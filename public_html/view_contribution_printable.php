<?php
	session_start();
	mb_internal_encoding("UTF-8");
?>

<html>
<head>
	<title>Print Contribution - The GM's Inspiration</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all">
</head>
<body>
<div id='container' class='cf'>
<?php

	$id = $_GET["contid"];

	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
	die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	$stmt = $mysql->prepare("SELECT username, img, name, `type`, sub_type, game, `desc`, json, privacy FROM contributions WHERE id=?");
	$stmt->bind_param("i", $id);
	if(!$stmt->execute()){
		echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
	}
	$user=null; $img=null; $name=null; $type=null; $s_type=null; $game=null; $desc=null; $json=null; $privacy=null;
	$stmt->bind_result($user, $img, $name, $type, $s_type, $game, $desc, $json, $privacy);
	$stmt->fetch();
	$stmt->close();

	$user = htmlspecialchars($user, ENT_QUOTES, "UTF-8");
	$name = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
	$type = htmlspecialchars($type, ENT_QUOTES, "UTF-8");
	$s_type = htmlspecialchars($s_type, ENT_QUOTES, "UTF-8");
	$game = htmlspecialchars($game, ENT_QUOTES, "UTF-8");



	if($privacy==1 && $user!=$_SESSION["username"]){
		//echo "<h3>The contributor has currently set the privacy to \"private,\" so you cannot view it at this time.";
		exit("The contributor has currently set the privacy to \"private,\" so you cannot view it at this time.");
	}
	$fields = json_decode(($json));    //create associative array from json

		echo "<div id='contribution'>";
		echo "<div class='name_user_game' ><h2><span id='name'>".$name."</span> - <span id='type'>".$type.(($s_type)? " </span>(<span id='subtype' title='Sub Type'>".$s_type."</span>)":"")."</h2>";
		echo "<h3>submitted by <a href=profile.php?user=".$user.">".$user."</a></h3><h3 id='game'>".$game."</h3></div>";	
		echo "<div id='body' style='display: block; clear: both;'><h4 style='margin-bottom: .1em; padding-bottom: 0em'>Description</h4>";
		echo "<div id='desc' style='margin-top: .1em'>".$desc."</div>";
		$num=1;
		foreach($fields as $key => $value){
			echo "<h4 id='label ".$num."' name='label ".$num."' style='margin-bottom: .1em; padding-bottom: 0em'>".$value->label."</h4>";
			echo "<div id='text ".$num."' name='text ".$num."' style='margin-top: .1em'>".$value->text."</div>";
			$num++;
		}
		echo "</div>";
		echo "</div>";
		
        echo "<h6>Contribution ID: <span  id='contid'>".$id."</span></h6>";

?>
</div>
</body>
</html>
