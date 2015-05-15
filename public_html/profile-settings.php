<?php
	// Define variables for SEO
	$pageTitle = "Profile Settings - The GM's Inspiration";
	$pageDescription = "Change your profile settings: description, profile picture, and password are currently supported";
	include "header.php";
	session_start();
?>
<DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="css/example/profile.css" media="all">
	<!--<script src="scripts/js/utils.js" ></script> -->
	<script type="text/javascript" language="javascript">
		function changePassword(){
			var div=document.createElement("div");
			$(div).html("<input type='password' id='pass' placeholder='Current Password'/><br>" +
				"<input type='password' id='newPass' placeholder='New Password'/><br>" +
				"<input type='password' id='confirm' placeholder='Confirm Password'/><br>");
			$(div).dialog({
				title: "Change Password",
				height: 250,
				width: 400,
				modal: true,
				position: {my: "center", at: "center", of: window },
				buttons:{
					"Submit": function(){
						$.ajax({
							url: "scripts/profileScripts.php",
							type: "POST",
							data: {
								pass: $("#pass").val(),
								newPass: $("#newPass").val(),
								confirmPass: $("#confirm").val(),
								funct: "changePassword"
							},
							success: function(html){
								$(div).html(html);
								$(div).dialog("option", "buttons", [{
									text: "Close",
									click: function(){
										$(this).dialog("close");
									}
								}]);
							},
							error: function(xhr, status, html){
								$(div).html(html);
								$(div).dialog("option", "buttons", [{
									text: "Close",
									click: function(){
										$(this).dialog("close");
									}
								}]);
							}
						});
					},
					"Cancel": function(){
						$(div).dialog("close");
					}
				}
			});
		}
		
		function updateImgurl(){
			$("#imgurl").text(document.getElementById("img").src);
			console.log(document.getElementById("img").src);
		}
		
		function updateImg(){
			console.log($("#imgurl").text());
			document.getElementById("img").src = $("#imgurl").text();
		}
		function editImgSrc(img){
			//var div = document.createElement("div");
			//$(div).html('<label for="src">URL: </label><input type="text" id="src" placeholder="'+img.src+'" />');
			$("#temp_div").dialog({
				height: 300,
				width: 450,
				position: {my: "center top", at: "center top", of: window},
				buttons: ({
					"Accept": function(){
						console.log($("#temp_div").html());
						img.src=$("#temp").val();
						$("#imgurl").val($("#temp").val());
						console.log(img);
						//img.src=document.getElementById("src").value;
						$("#temp_div").dialog("close");
						//div.parentNode.removeChild(div);
					},
					"Cancel": function(){
						$("#temp_div").dialog("close");
					}
				})
			});
		}
	</script>
</head>
<body>
<div id='container'>

<?php

	$display = 1;
	if($_SESSION["username"]){
		$username=$_SESSION["username"];
	}else{
		$display = 0;	
	}

	if($display == 1){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}

		try{
			$mysql->query("START TRANSACTION");
			//$result = $mysql->query("SELECT * from users where username='".$username."'");
			$stmt = $mysql->prepare("SELECT description, picture FROM users WHERE username=?");
			$stmt->bind_param("s", $username);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$description = null; $picture = null;
			$stmt->bind_result($description, $picture);
			$stmt->fetch();
			$stmt->close();

			$description = htmlspecialchars($description, ENT_QUOTES, "UTF-8");
			$picture = htmlspecialchars($picture, ENT_QUOTES, "UTF-8");

			echo "<form method='POST' action='profile.php'>";
			echo "Description<br>";
			echo "<textarea name='descrEdit' rows=7 cols=75>".$description."</textarea><br>";
			echo "Click to edit profile image<br>";
			if ($picture=="img/hat_profile200.png"){
				echo "<textarea id='imgurl' name='imgurl' rows='6' cols='38' style='display: none;'></textarea>";
				echo "<img id='img' src='img/hat_profile200.png' onclick='editImgSrc(this)' height='100' width='100' /><br>";
			}
			else {
				echo "<textarea id='imgurl' name='imgurl' rows='6' cols='38' style='display: none;'>".$picture."</textarea>";
				//echo "<div id='imgurl_div' style='display: none'><input type='text' id='imgurl' name='imgurl' value='".$picture."'/></div>";
				echo "<img id='img' src='".$picture."' onclick='editImgSrc(img)' height='100' width='100' /><br>";
			}
			echo "<input type='submit' name='settingEdit' onmousedown='imgurl.value=img.src' onfocus='imgurl.value=img.src' value='Save Changes'>";	
			echo "</form>";
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
		$mysql->close();

	}
?>
	<a class="but" id="changePass" onclick="changePassword()">Change Password</a>
	<div id="temp_div" style="display: none"><textarea id="temp" rows="6" cols="38" onkeypress="console.log(event.which)"></textarea></div>

</div>
</body>
</html>
<?php include 'footer.php';?>