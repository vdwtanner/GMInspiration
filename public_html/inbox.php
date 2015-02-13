<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="css/example/profile.css" media="all">
	<?php include "header.php";?>
</head>
<body>
<script type="text/javascript" language="javascript">
	function toggle_vis(list, toggle){//list=msg div id, toggle=show/hide button
		var e = document.getElementById(list);
		if(e.style.display != "none") {
			e.style.display = "none";
		}else{
			e.style.display = "block";
		}
		if(toggle.text != "[show]"){
			toggle.text = "[show]";
		}else{
			toggle.text = "[hide]";
			read($(toggle).parent().parent().attr("id"));
		}
	}
	
	function read(id){
		$.ajax({
			url: "scripts/readMsg.php",
			type: "POST",
			data: {
				msg: id,
			},
			success: function(html){
				console.log(html);
			}
		});
	}
	
	function del(id){
		$.ajax({
			url: "scripts/deleteMsg.php",
			type: "POST",
			data: {
				msg: id,
			},
			success: function(html){
				console.log(html);
				$("#"+id).remove();
			}
		});
	}

</script>



<div id='container'>

<?php
	function deleteMsg($id){

		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			$result = $mysql->query("DELETE FROM private_messages WHERE id='".$id."'");
		}catch(Exception $e){

		}

	}


	if($_SESSION["username"]){

		if($_GET["delete"])
			deleteMsg($_GET["delete"]);

		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}

		try{

			$mysql->query("START TRANSACTION");
			$result = $mysql->query("SELECT * FROM private_messages WHERE recipient='".$_SESSION["username"]."'");
			//$result = $mysql->query("SELECT * FROM private_messages WHERE recipient='vdwtanner'");
			//$result = $mysql->query("SELECT * FROM private_messages");
			//print_r($row);
			while($row = $result->fetch_assoc()){
				$rowarr[] = $row;		
			}	

			$count = 0;
			if($rowarr){
				foreach($rowarr as $key => $value){
					echo "<div class='msgheader' id='".$value["id"]."'>";//This is set to just the ID number of the message to allow easier manipulation of the DB
					echo "<b>From:</b>&nbsp<a href='profile.php?user=".$value["sender"]."'>".$value["sender"]."</a>";
					echo "<b style='padding-left: 10px'>Subject:</b>&nbsp".$value["subject"];
					echo "<div class='listshowhide'>";
					echo "<a href='#' onclick='toggle_vis(\"b".$count."\",this);' style='float: right;'>[show]</a>";
					echo "<a href='#' onclick='del(".$value["id"].")' style='float: right;'>[delete]&nbsp</a>";
					echo "<p style='display:inline; color: grey'>".$value["timestamp"]."&nbsp</p>";
					echo "</div><hr>";
					echo "<div class='msgbody' id='b".$count."' style='display: none'>";
					echo "<p>".$value["message"]."<p>";
					echo "</div>";
					echo "<br><br>";
					echo "</div>";
					$count++;
				}
			}else{
				echo "<b>You have no messages!</b>";
			}
		}catch(Exception $e){

		}

	
	}else{
		echo "<b>You must log in before viewing this page.</b>";
	}


?>

</div>
</body>
