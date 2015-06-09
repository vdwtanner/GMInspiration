<?php
	include "header.php";
	session_start();
?>
<DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="css/example/profile.css" media="all">
</head>
<body>
<div id='container'>

<?php
	if($_SESSION["username"]){

		try{

			echo "<form method='POST' action='sendmsg.php?redirect=".htmlspecialchars($_GET["redirect"], ENT_QUOTES, "UTF-8")."'>";
			if($_GET["recipient"]){
				echo "TO: ".htmlspecialchars($_GET["recipient"], ENT_QUOTES, "UTF-8")."<br>";
				echo "<input type='hidden' name='msgrecipient' value='".htmlspecialchars($_GET["recipient"], ENT_QUOTES, "UTF-8")."'>";
			}else{
				echo "<input type='text' name='msgrecipient' placeholder='Recipient' size='76' maxlength='255'></input><br>";
			}
			echo "<input type='text' name='msgsubject' placeholder='Message Subject' size='76' maxlength='255' value='";
			if($_GET["subject"]){
				if(substr($_GET["subject"],0,3) != "RE:"){
					echo "RE: ";
				}
				echo $_GET["subject"];
			}
			echo "'/><br>";
			echo "<textarea name='msgbody' rows=7 cols=75 placeholder='Enter your message here' style='resize:none' maxlength='6000'></textarea><br>";	
			echo "<input class='but' type='submit' value='Send'>";
		
			echo "</form>";

		}catch(Exception $e){

		}

	
	}else{
		echo "<b>You must log in before viewing this page.</b>";
	}


?>

</div>
</body>
<?php include 'footer.php';?>