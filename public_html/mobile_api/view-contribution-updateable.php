<?php
	session_start();
	require dirname(__FILE__)."/scripts/parser.php";
?>


<?php
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}
	
	try{
		$stmt = $mysql->prepare("SELECT permissions FROM api_keys WHERE api_key=?");
		$stmt->bind_param("s", $_GET["api_key"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$p = null;
		$stmt->bind_result($p);	
		$stmt->fetch();

		if(!$p)
			exit("{\"msg\":\"Error: Invalid Key\"}");


		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}

?>

<?php
	$isCreator=false;
    try{
	/*********************************
		Avg Ratings Code
	**********************************/
        $mysql->query("START TRANSACTION");
		//$ratings=$mysql->query("SELECT COUNT(*), SUM(fun), SUM(balance) FROM ratings WHERE contribution_id=".$id);
		$stmt = $mysql->prepare("SELECT COUNT(*), SUM(fun), SUM(balance) FROM ratings WHERE contribution_id=?");
		$stmt->bind_param("i", $id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$c=null; $s=null; $b=null;
		$stmt->bind_result($c, $s, $b);
		$stmt->fetch();
		$stmt->close();
		$num_ratings=$c;
		if($c>0){
			$avgFun=$s/$c;
			$avgBalance=$b/$c;
		}
        //$result = $mysql->query("SELECT * from contributions where id='".$id."'");
	/*********************************
		Get Contribution Data
	**********************************/
	$stmt = $mysql->prepare("SELECT username, img, name, `type`, sub_type, game, `desc`, json, privacy FROM contributions WHERE id=?");
	$stmt->bind_param("i", $id);
	if(!$stmt->execute()){
		echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
	}
	$user=null; $img=null; $name=null; $type=null; $s_type=null; $game=null; $desc=null; $json=null; $privacy=null;
	$stmt->bind_result($user, $img, $name, $type, $s_type, $game, $desc, $json, $privacy);
	$stmt->fetch();
	$stmt->close();
	if($privacy==1 && $user!=$_SESSION["username"]){
		exit("{\"msg\":\"The contributor has currently set the privacy to private, so you cannot view it at this time.\"}");
	}
	$fields = json_decode(($json));    //create associative array from json

   }catch(Exception $e){
	$mysql->rollback();
	exit("{\"msg\":\"Exception thrown\"}");
   }
	 
?>

