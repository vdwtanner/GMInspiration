<?php
	session_start();
	mb_internal_encoding("UTF-8");
?>
<html>
<head>
	<title><?php echo $pageTitle; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="<?php echo $pageDescription; ?>">
	<?php// If meta robots content is specified, include robots meta tag
	/*if($pageRobots)
	{
		echo '<meta name="robots" content="' . $pageRobots . '">';
	}*/?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="scripts/js/notify.min.js"></script>
	
	<script type="application/ld+json">
    {  "@context" : "http://schema.org",
       "@type" : "WebSite",
       "name" : "The GM's Inspiration",
       "alternateName" : "GM's Inspiration",
       "url" : "http://www.gminspiration.com"
    }
    </script>
	<style>
		.ui-autocomplete-category {
			font-weight: bold;
			padding: .2em .4em;
			margin: .8em 0 .2em;
			line-height: 1.5;
		}
		.ui-autocomplete {
			max-height: 10em;
			overflow-y: auto;
			overflow-x: hidden;
		}
  </style>
  <script>
  $.widget( "custom.catcomplete", $.ui.autocomplete, {
    _create: function() {
      this._super();
      this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
    },
    _renderMenu: function( ul, items ) {
      var that = this,
        currentCategory = "";
      $.each( items, function( index, item ) {
        var li;
        if ( item.category != currentCategory ) {
          ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
          currentCategory = item.category;
        }
        li = that._renderItemData( ul, item );
        if ( item.category ) {
          li.attr( "aria-label", item.category + " : " + item.label );
        }
      });
    }
  });
  </script>
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
				title: "Login",
				height: 270,
				width: 430,
				modal: true,
				position: {my: "center top", at: "center top", of: window},
				buttons: {
					"Forgot Password": function(){
						$("#modal_area").html("<b>Please enter the email linked to your account</b><br><input id='email' type='email' placeholder='email' tooltip='Must be a valid email' />");
						$("#modal_area").dialog({
							title: "Recover Password",
							height: 270,
							width: 430,
							modal: true,
							buttons: {
								"Reset Password": function(){
									console.log("reset pass");
									$.ajax({
										url: "scripts/profileScripts.php",
										type: "POST",
										data: {
											funct: "resetPassword",
											email: $("#email").val(),
										},
										success: function(html){
											$("#modal_area").html(html);
											$("#modal_area").dialog({
												title: "Success!",
												buttons: {
													"Close": function(){
														$(this).dialog("close");
													}
												}
											});
										},
										error: function(xhr, status, error){
											$("#modal_area").html(error);
											$("#modal_area").dialog({
												title: "ERROR",
												buttons: {
													"Close": function(){
														$(this).dialog("close");
													}
												}
											});
										}
									});
								},
								"Cancel": function(){
									login();
								}
							}
						});
					},
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
						//var buttons = $("#modal_area").dialog("option", "buttons");
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
				url: "sign_up_completion.php",
				type: "POST",
				data: ({
					username: user,
					email: email,
					pass: pass,
				}),
				success: function(html){
					//$("#modal_area").html("<b>Successful signup. Check your inbox and spam filter for an activation email</b>");
					$("#modal_area").html(html);
					$("#modal_area").dialog({
						title: "Successful sign up!",
						buttons: {
							"Close": function(){
								$("#modal_area").dialog("close");
							}
						}
					});
				},
				error: function(xhr, status, error){
					$("#modal_area").html(error);
					$("#modal_area").dialog({
						title: "ERROR",
						buttons: {
							"Back": function(){
								signUp();
							},
							"Close": function(){
								$("#modal_area").dialog("close");
							}
						}
					});
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
				title: "Sign Up",
				modal: true,
				position: {my: "center top", at: "center top", of: window},
				buttons: {
					"Sign Up": function(){
						//$("#signup_form").validate();
						if(($("#signup_pass").val()==$("#signup_confpass").val())){
							submitSignUp();
						}else{
							//$("#signup_form").submit();
							alert("Password mismatch");
						}
					},
					"Cancel": function(){
						$(this).dialog("close");
					}
				}
			});
		}
		
		var numMessages=<?php echo ($_SESSION["numMessages"]>0)?$_SESSION["numMessages"]:"0" ?>;
		function updateInboxNotification(){
			console.log("updateInboxNotification()");
			$.ajax({
				url: "scripts/getNumberUnreadMessages.php",
				type: "POST",
				success: function(html){
					$("#inbox_notification").html(html);
					var msg=$("#inbox_notification").text();
					msg=msg.replace(/\D/g,'');
					console.log(numMessages +" "+ msg);
					if(numMessages<msg){
						$.notify("You have "+(msg-numMessages)+" new message"+(((msg-numMessages)>1)?"s":"")+"!", "info");
					}
					numMessages=msg;
				}
			});
			window.setTimeout(function(){updateInboxNotification()},10000);//update every 10 seconds
		}
		
		function updateInboxNotificationSingle(){
			console.log("updateInboxNotification()");
			$.ajax({
				url: "scripts/getNumberUnreadMessages.php",
				type: "POST",
				success: function(html){
					$("#inbox_notification").html(html);
				}
			});
		}
		
		$(function(){
			updateInboxNotification();
			//setTimeout(function(){updateInboxNotification()},50);//update after 50 milliseconds
		});
		
		function autosearch(input){
			var text=input.value;
			$.ajax({
				url: "scripts/autoCompleter.php",
				type: "POST",
				data: {
					type: "userNames",
					input: text
				},
				success: function(json){
					console.log(json);
					var source=JSON.parse(json);
					$(input).autocomplete({source: source});
				},
				error: function(xhr, status, error){
					console.log(error);
				}
			});
		}
	</script>
	<?php 
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}
		$stmt=$mysql->prepare("SELECT username FROM `users` ORDER BY username ASC");
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
		
		?>
  <script>
  $(function() {
    var data = <?php echo "'".json_encode($json)."'";?>;
	data=JSON.parse(data);
    $( "#search" ).catcomplete({
      delay: 0,
      source: data
    });
  });
  </script>
</head>
<body>

<?php
	echo "<div id='headcontainer'>";
	
	echo "<div id='headerlinks'>";
	if($_SESSION["username"]){

		echo "<b>Welcome, <a href='profile.php?user=".htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8")."'>".htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8")."</a> &nbsp; <a onclick='logoff()'>logout</a></b>";
	}else{
		echo "<b><a onclick='(login())'>Login</a> &nbsp; <a onclick='signUp()'>Sign up</a></b>";
	}
	
	if($_SESSION["username"]){
		echo "<a href='inbox.php' style='float: right;' ><b id='inbox_notification'>Inbox</b></a>";
	}
	
	echo "</div>";
	//header img
	echo "<div style='clear: both;'>";
	echo "<a  id='homelink' href='index.html'><img class='title' src='img/title2.png'></a>";
	
	echo "</div>";
	
	echo "<div style='clear: both;'>";
	echo "<hr>";
	echo "<b><a class='hlink' href='home.php'>Home</a>&nbsp;</b>";
	
	if($_SESSION["username"]){
	echo "<b><a class='hlink' href='profile.php?user=".htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8")."'>Profile</a>&nbsp;</b>";
	echo "<b><a class='hlink' href='contribute.php'>Contribute</a>&nbsp;</b>";
	echo "<b><a class='hlink' href='collections.php'>Collections</a>&nbsp;</b>";
	
	}
	echo "<b><a class='hlink' href='about.php'>About</a>&nbsp;</b>";
	echo "<b><a class='hlink' href='contact.php'>Contact Us</a>&nbsp;</b>";
	//search bar
	echo "<form  method='GET' style='display: inline; float: right; margin: 0px;' action='search-results.php'>";
	if(!$_GET["keywords"])
		echo "<input id='search' type='text' name='keywords' style='width: 20em' placeholder='Enter keywords here' title='Search for users, contributions, types, subtypes, and game versions' autocomplete='false'>";
	else
		echo "<input id='search' type='text' name='keywords' style='width: 20em' value='".htmlspecialchars($_GET["keywords"], ENT_QUOTES, "UTF-8")."' title='Search for users, contributions, types, subtypes, and game versions' autocomplete='false'>";
	echo "<input type='hidden' name='csort' value='relevance'>";
	echo "<input class='but' type='submit' value='Search'>";
	echo "</form>";
	//echo "<hr>";
	echo "</div>";
	echo "</div>";

?>
	<div id="test" title="test" style="display: none;"><p>This is a modal test</p></div>
	<div id="modal_area" style="display: none" title="Login"></div>
</body>
</html>

