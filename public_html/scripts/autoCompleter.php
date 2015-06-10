<?php
	
	//use to load all names without displaying in js
	function userNames(){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
			if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		$value=$_POST["input"];
		$stmt=$mysql->prepare("SELECT username FROM `users` ORDER BY username ASC");
			/* CASE WHEN username = ? THEN 0
				WHEN username LIKE ? THEN 1
				WHEN username LIKE ? THEN 2
				WHEN username LIKE ? THEN 3
				ELSE 4 END,*/
		//$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalue = "%".$value; $pvalueu = "%".$value."_";
		//$stmt->bind_param("sssss", $pvaluep, $value, $valuep, $pvalueu, $pvalue);
		//$stmt->bind_param("s", $pvaluep);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$result=null;
		$stmt->bind_result($result);
		$json=array();
		while($stmt->fetch()){
			$res_arr=array( "label" => $result, "category"=>"users");
			array_push($json, $res_arr);
		}
		echo json_encode($json);
		//return json_encode($result);
	}
	
	function contributions(){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
			if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		$value=$_POST["input"];
		//$stmt=$mysql->prepare('SELECT username FROM users LIMIT 10');
		$stmt=$mysql->prepare("SELECT name FROM contributions WHERE name LIKE ? ORDER BY CASE WHEN name LIKE ? THEN 0
			WHEN name LIKE ? THEN 1
			WHEN name LIKE ? THEN 2
			ELSE 3 END, name ASC LIMIT 10");
		$pvaluep = "%".$value."%"; $valuep = $value."%"; $pvalueu = "%".$value."_";
		$stmt->bind_param("ssss", $pvaluep, $value, $valuep, $pvalueu);
		//$stmt->bind_param("s", $pvaluep);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$result=null;
		$stmt->bind_result($result);
		$json=array();
		while($stmt->fetch()){
			array_push($json, $result);
		}
		echo json_encode($json);
	}
	
	
	if($_POST["type"]=="userNames"){
		userNames();
	}else if($_POST["type"]=="contributions"){
		contributions();
	}
	
?>