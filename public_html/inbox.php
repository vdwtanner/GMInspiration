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
	
	function checkMessages(){
		$.ajax({
			url: "scripts/loadNewMessages.php",
			type: "POST",
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
		window.setInterval(function(){checkMessages()},30000);//update every  seconds
	});

</script>



<div id='container'>

<?php
	include "scripts/renderMsgList.php";	


	if($_SESSION["username"]){

		if($_GET["delete"])
			deleteMsg($_GET["delete"]);

		renderMsgList("INBOX", $_SESSION["username"]);	// This is done externally so the checkmessages function doesnt have to create a redundant file
	
	}

?>

</div>
</body>
