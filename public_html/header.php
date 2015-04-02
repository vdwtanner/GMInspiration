<?php
	session_start();
?>
<html>
<head>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script type="text/javascript" language="javascript">
		function submitLogin(dialog){
			
			var user=$("#login_username").val();
			var pass=$("#login_pass").val();
			$.ajax({
				url: "login.php",
				type: "POST",
				data: ({
					username: user,
					pass: pass,
				}),
				success: function(html){
					$("#login_container").html(html);
					$("#modal_area").dialog("option", "buttons", [{
						text: "Close",
						click: function(){
							$(this).dialog("close");
							location.reload();
						}
					}]);
					setTimeout(function(){location.reload()},1500);
				},
				error: function(xhr, status, error){
					$("#login_container").html(error);
					$("#modal_area").dialog("option", "buttons", [{
						text: "Close",
						click: function(){
							$(this).dialog("close");
						}
					}]);
				}
			});
		}
		
		$("#login_pass").keypress(function(e){
			console.log("Key pressed: "+ e);
			if(e.keyCode == $.ui.keyCode.ENTER) {
				console.log("ENTER pressed");
				var buttons = $("#modal_area").dialog("option", "buttons");
				buttons["Login"].click.apply(dialog);
			}
		});
	
		function login(){
			var dialog, form;
			$("#modal_area").load("login.html");
			form = $("#modal_area").innerHTML;
			//alert(form);
			$("#modal_area").dialog({
				height: 300,
				width: 350,
				modal: true,
				position: {my: "center top", at: "center top", of: window},
				buttons: {
					"Login": function(){
						submitLogin(this);
					},
					"Cancel": function(){
						$(this).dialog("close");
					}
				},
				open: function() {
					$("#modal_area").keypress(function(e) {
					//console.log("Key pressed: " +e.keyCode);
					if (e.keyCode == 13) {
						var buttons = $("#modal_area").dialog("option", "buttons");
						//console.log(buttons);
						submitLogin(dialog);
					}
					});
				}
			});
		}
		
		function submitLogoff(){
			$.ajax({
				url: "logoff.php",
				success: function(html){
					location.reload();
				},
				error: function(){
					alert("An error has occured");
				}
			});
		}
		
		function logoff(){
			$("<b>Do you really want to logout?</b>").dialog({
				height: 200,
				width: 350,
				modal: true,
				position: {my: "center top", at: "center top", of: window},
				buttons: {
					"Yes": function(){submitLogoff();},
					"No": function(){$(this).dialog("close");}
				}
			});
		}
		
		function submitSignUp(){
			var user=$("#signup_username").val();
			var pass=$("#signup_pass").val();
			var email=$("#signup_email").val();
			$.ajax({
				url: "sign_up.php",
				type: "POST",
				data: ({
					username: user,
					email: email,
					pass: pass,
				}),
				success: function(html){
					$("#modal_area").html("<b>Successful signup. Check your inbox and spam filter for an activation email</b>");
				},
				error: function(xhr, status, error){
					$("#modal_area").html("<b>An error occurred.</b>");
				}
			});
		}
		
		function signUp(){
			var dialog, form;
			$("#modal_area").load("sign_up.html");
			form = $("#modal_area").innerHTML;
			//alert(form);
			$("#modal_area").dialog({
				height: 450,
				width: 450,
				modal: true,
				position: {my: "center top", at: "center top", of: window},
				buttons: {
					"Sign Up": function(){
						$("#signup_form").validate();
						if($("#signup_form").valid() && ($("#signup_pass").val()==$("#signup_confirm_pass").val())){
							submitSignUp();
						}else{
							$("#signup_form").submit();
						}
					},
					"Cancel": function(){
						$(this).dialog("close");
					}
				}
			});
		}
		
		function updateInboxNotification(){
			console.log("updateInboxNotification()");
			$.ajax({
				url: "scripts/getNumberUnreadMessages.php",
				type: "POST",
				success: function(html){
					$("#inbox_notification").html(html);
				}
			});
			window.setTimeout(function(){updateInboxNotification()},30000);//update every 30 seconds
		}
		
		$(function(){
			updateInboxNotification();
			//setTimeout(function(){updateInboxNotification()},50);//update after 50 milliseconds
		});
		
	</script>
</head>
<body>

<?php



	echo "<div id='headcontainer'>";
	
	echo "<div id='headerlinks'>";
	if($_SESSION["username"]){

		echo "<b>Welcome, <a href='profile.php?user=".$_SESSION["username"]."'>".$_SESSION["username"]."</a> &nbsp; <a onclick='logoff()'>logout</a></b>";
	}else{
		echo "<b><a onclick='(login())'>Login</a> &nbsp; <a href='sign_up.php'>Sign up</a></b>";
	}
	
	if($_SESSION["username"]){
		echo "<a href='inbox.php' style='float: right;' ><b id='inbox_notification'>Inbox</b></a>";
	}
	
	echo "</div>";

	echo "<div style='clear: both;'>";
	
	echo "<a id='homelink' href='index.html'>The Game Master's Inspiration</a>";
	
	echo "</div>";
	
	echo "<div style='clear: both;'>";
	echo "<hr>";
	echo "<form method='GET' action='search_results.php' style='display: inline;'>";
	echo "<input type='text' name='keywords' style='width: 20em' placeholder='Enter keywords here' title='Search for users, contributions, types, subtypes, and game versions'>";
	echo "<input type='hidden' name='usort' value='relevance'>";
	echo "<input type='hidden' name='csort' value='relevance'>";
	echo "<input type='submit' name='searchSubmit' value='Search'>";
	echo "</form>";
	
	echo "<hr>";
	echo "</div>";
	echo "</div>";

?>
	<div id="test" title="test" style="display: none;"><p>This is a modal test</p></div>
	<div id="modal_area" style="display: none" title="Login"></div>
</body>
</html>

