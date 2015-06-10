<?php
	include "header.php";
	session_start();
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}
	$stmt=$mysql->prepare("SELECT username FROM users ORDER BY username ASC");
	$result=null;
	if(!$stmt->execute()){
		echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
	}
	$stmt->bind_result($result);
	$json=array();
	while($stmt->fetch()){
		array_push($json, $result);
	}
	
?>
<DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="css/example/profile.css" media="all">
	<script type="text/javascript">
		$(function() {
			var tags= <?php echo "'".json_encode($json)."'";?>;
			tags=JSON.parse(tags);
			$("#recipient").autocomplete({source: tags});
		});
		
		function sendmsg(){
			var to = $("#recipient").val();
			var sub = $("#subject").val();
			var msg = $("#body").val();
			$.ajax({
				url: "scripts/sendmsg.php",
				type: "POST",
				data: {
					msgrecipient: to,
					msgsubject: sub,
					msgbody: msg
				},
				success: function(html){
					//console.log(html);
					$.notify(html, "Success");
					$("#body").val("");
				},
				error: function(xhr, status, error){
					console.log(error);
					$.notify(error,"error");
				}
			});
			
		}
	</script>
</head>
<body>
<div id='container'>

<?php
	if($_SESSION["username"]){

		try{

			echo "<form method='POST'>";
			if($_GET["recipient"]){
				echo "TO: ".htmlspecialchars($_GET["recipient"], ENT_QUOTES, "UTF-8")."<br>";
				echo "<input id='recipient' type='hidden' name='msgrecipient' value='".htmlspecialchars($_GET["recipient"], ENT_QUOTES, "UTF-8")."'>";
			}else{
				echo "<input id='recipient' type='text' name='msgrecipient' placeholder='Recipient' size='76' maxlength='255' autocomplete='off'/><br>";
			}
			echo "<input id='subject' type='text' name='msgsubject' placeholder='Message Subject' size='76' maxlength='255' value='";
			if($_GET["subject"]){
				if(substr($_GET["subject"],0,3) != "RE:"){
					echo "RE: ";
				}
				echo $_GET["subject"];
			}
			echo "'/><br>";
			echo "<textarea id='body' name='msgbody' rows=7 cols=75 placeholder='Enter your message here' style='resize:none' maxlength='6000'></textarea><br>";	
			//echo "<input class='but' type='submit' value='Send' onclick='sendmsg()'>";
			echo "</form>";
			echo "<a class='button' onclick='sendmsg()'>Send</a>";

		}catch(Exception $e){

		}

	
	}else{
		echo "<b>You must log in before viewing this page.</b>";
	}


?>

</div>
</body>
<?php include 'footer.php';?>