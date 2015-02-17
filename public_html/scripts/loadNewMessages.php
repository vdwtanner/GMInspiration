<?php
	session_start();
	/*$time = $_POST["time"];//time to load after
	if(!$time){
		header("HTTP/1.1 412 No time provided");
		die("Need to provide time you fool");
	}*/
	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			$result=$mysql->query("SELECT * from private_messages where timestamp > now() - INTERVAL 5 SECOND  ORDER BY timestamp DESC");//Assumes call gets through every 5 seconds
			while($row=$result->fetch_array(MYSQL_BOTH)){
				echo "<div class='msgheader' id='".$row["id"]."'>";//This is set to just the ID number of the message to allow easier manipulation of the DB
				echo "<b>From:</b>&nbsp<a href='profile.php?user=".$row["sender"]."'>".$row["sender"]."</a>";
				echo "<b style='padding-left: 10px'>Subject:</b>&nbsp".$row["subject"];
				echo "<div class='listshowhide'>";
				echo "<a href='#' onclick='toggle_vis(\"b".$row["id"]."\",this);' style='float: right;'>[show]</a>";
				echo "<a href='#' onclick='del(".$row["id"].")' style='float: right;'>[delete]&nbsp</a>";
				echo "<p style='display:inline; color: grey'>".$row["timestamp"]."&nbsp</p>";
				echo "</div><hr>";
				echo "<div class='msgbody' id='b".$row["id"]."' style='display: none'>";//use ID instead of the original count so that we can load via AJAX
				echo "<p>".$row["message"]."<p>";
				echo "</div>";
				echo "<br><br>";
				echo "</div>";
			}
		}catch(Exception $e){
			$mysql->rollback();
		}
		$mysql->close();
?>