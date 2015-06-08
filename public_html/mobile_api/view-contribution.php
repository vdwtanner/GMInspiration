<?php
	session_start();
?>


<?php
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}
	
	$isCreator=false;

	try{
		$mysql->query("START TRANSACTION");
		$stmt = $mysql->prepare("SELECT permissions FROM api_keys WHERE api_key=?");
		$stmt->bind_param("s", $_GET["api_key"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$p = null;
		$stmt->bind_result($p);	
		$stmt->fetch();
		$stmt->close();

		if(!$p)
			exit("{\"msg\":\"Error: Invalid Key\"}");

	/*********************************
		Get Contribution Data
	**********************************/
		$stmt = $mysql->prepare("SELECT username, img, name, `type`, sub_type, game, `desc`, json, avg_fun, avg_balance, privacy FROM contributions WHERE id=?");
		$stmt->bind_param("i", $_GET["contid"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$user=null; $img=null; $name=null; $type=null; $s_type=null; $game=null; $desc=null; $avg_fun=null; $avg_balance=null; $json=null; $privacy=null;
		$stmt->bind_result($user, $img, $name, $type, $s_type, $game, $desc, $json, $avg_fun, $avg_balance, $privacy);
		$stmt->fetch();
		$stmt->close();
		if($privacy==1 && $user!=$_SESSION["username"]){
			exit("{\"msg\":\"The contributor has currently set the privacy to private, so you cannot view it at this time.\"}");
		}
		$row["username"] = $user;
		$row["img"] = $img;
		$row["name"] = $name;
		$row["type"] = $type;
		$row["sub_type"] = $s_type;
		$row["game"] = $game;
		$row["desc"] = $desc;
		$row["json"] = $json;
		$row["avg_fun"] = $avg_fun;
		$row["avg_balance"] = $avg_balance;
		$row["privacy"] = $privacy;
 
		print_r(json_encode($row));

		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
		exit("{\"msg\":\"Exception thrown\"}");
	}
	 
?>

