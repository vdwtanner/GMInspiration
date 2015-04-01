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
	
	function checkMessages(msgListType){
		var funct = "renderMsgList";
		if(msgListType == "INBOX" || msgListType == "SENT"){
			document.getElementById("message_pane").innerHTML = "";
		}
		if(msgListType == "INBOX"){
			document.getElementById("inboxButton").disabled = true;
			document.getElementById("sentButton").disabled = false;
		}
		if(msgListType == "SENT"){
			document.getElementById("inboxButton").disabled = false;
			document.getElementById("sentButton").disabled = true;
		}
		$.ajax({
			url: "scripts/renderMsgList.php",
			type: "POST",
			data: {
				action: funct,
				type: msgListType
			},
			success: function(html){
				$("#message_pane").prepend(html);
				console.log("checked messages");
			},
			error: function(xhr, status, error){
				console.log(error);
			}
		});
		//window.setInterval(function(){checkMessages()}, 5000);//update every 5 seconds
	}
	
	$(function(){
		window.setInterval(function(){checkMessages("INBOX_UNREAD")},30000);//update every 30 seconds
	});

</script>



<div id='container'>

<?php
	include "scripts/renderMsgList.php";	

	if($_SESSION["username"]){
		echo "<form method='GET' action='composemsg.php' style='display: inline; float:right; margin:0px'><button id='composeButton' type='submit'>Compose</button><input name='redirect' type='hidden' value='i'></form>";
		echo "<button id='inboxButton' type='button' onClick='checkMessages(\"INBOX\")' disabled>Inbox</button>";
		echo "<button id='sentButton' type='button' onClick='checkMessages(\"SENT\")'>Sent</button>";
		echo "<hr>";

	}

	echo "<div id='message_pane'>";
		if($_SESSION["username"]){
			renderMsgList("INBOX");		
		}else{
			echo "Please log in to see this page!";
		}
	echo "</div>";

?>

</div>
</body>
