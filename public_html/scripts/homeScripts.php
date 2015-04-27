<?php
	function getTopContributors(){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$json=array();
		try{
			$mysql->query("START TRANSACTION");
			$stmt=$mysql->prepare("SELECT username, picture, contributions, description FROM users WHERE contributions > 0 ORDER BY contributions DESC LIMIT 4");
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
	
	function getUpAndComing(){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$json=array();
		$json2=array();
		try{
			$mysql->query("START TRANSACTION");
			//by ratings
			$stmt=$mysql->prepare("SELECT id, name, avg_fun, avg_balance, type, sub_type, game, img, ratings FROM contributions WHERE privacy=0 AND ratings > 0 AND `timestamp` > DATE_SUB(now(), INTERVAL 30 DAY) AND img != 'NULL' ORDER BY avg_fun DESC, avg_balance DESC LIMIT 5");
			if(!$stmt->execute()){
				echo "MYSQL ERROR";
			}
			$id=null; $name=null; $fun=null; $bal=null; $type=null; $sub=null; $game=null; $img=null; $ratings=null;
			$stmt->bind_result($id, $name, $fun, $bal, $type, $sub, $game, $img, $ratings);
			while($stmt->fetch())
			{
				$bindResults = array("id"=>$id, "name"=>$name, "avg_fun"=>$fun, "avg_balance"=>$bal, "type"=>$type, "sub_type"=>$sub, "game"=>$game, "img"=>$img, "ratings"=>$ratings);
				array_push($json, $bindResults);
			}
			$stmt->close();
			//By num_ratings and ratings
			$stmt=$mysql->prepare("SELECT id, name, avg_fun, avg_balance, type, sub_type, game, img, ratings FROM contributions WHERE privacy=0 AND ratings > 0 AND `timestamp` > DATE_SUB(now(), INTERVAL 30 DAY) AND img != 'NULL' ORDER BY ratings DESC, avg_fun DESC, avg_balance DESC LIMIT 5");
			if(!$stmt->execute()){
				echo "MYSQL ERROR";
			}
			$id=null; $name=null; $fun=null; $bal=null; $type=null; $sub=null; $game=null; $img=null; $ratings=null;
			$stmt->bind_result($id, $name, $fun, $bal, $type, $sub, $game, $img, $ratings);
			while($stmt->fetch())
			{
				$bindResults = array("id"=>$id, "name"=>$name, "avg_fun"=>$fun, "avg_balance"=>$bal, "type"=>$type, "sub_type"=>$sub, "game"=>$game, "img"=>$img, "ratings"=>$ratings);
				array_push($json2, $bindResults);
			}
			$stmt->close();
			if(count($json)<5){
				$limit=5-count($json);
				$stmt=$mysql->prepare("SELECT id, name, avg_fun, avg_balance, type, sub_type, game, img, ratings FROM contributions WHERE ratings=0 AND privacy=0 AND `timestamp` > DATE_SUB(now(), INTERVAL 30 DAY) AND img != 'NULL' ORDER BY timestamp DESC LIMIT ?");
				$stmt->bind_param("i", $limit);
				if(!$stmt->execute()){
					echo "MYSQL ERROR";
				}
				$id=null; $name=null; $fun=null; $bal=null; $type=null; $sub=null; $game=null; $img=null; $ratings=null;
				$stmt->bind_result($id, $name, $fun, $bal, $type, $sub, $game, $img, $ratings);
				while($stmt->fetch())
				{
					$bindResults = array("id"=>$id, "name"=>$name, "avg_fun"=>$fun, "avg_balance"=>$bal, "type"=>$type, "sub_type"=>$sub, "game"=>$game, "img"=>$img, "ratings"=>$ratings);
					array_push($json, $bindResults);
				}
				$stmt->close();
			}
		}catch(Exception $e){
			echo "ERROR: ".$e;
		}
		$mysql->close();
		return json_encode($json);
	}
?>