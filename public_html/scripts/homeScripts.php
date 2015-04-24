<?php
	function getTopContributors(){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$json=array();
		try{
			$stmt=$mysql->prepare("SELECT username, picture, contributions, description FROM users ORDER BY contributions DESC LIMIT 4");
			if(!$stmt->execute())
				echo "MYSQL ERROR";
			$username=null; $picture=null; $contributions=null; $desc=null;
			$stmt->bind_result($username, $picture, $contributions, $desc);
			//$stmt->bind_result($username);
			while($stmt->fetch())
			{
				$bindResults = array("username"=>$username, "contributions"=>$contributions, "picture"=>$picture, "desc"=>$desc);
				array_push($json, $bindResults);
			}
		}catch(Exception $e){
			echo "error occurred";
		}
		$mysql->close();
		return json_encode($json);
	}
?>