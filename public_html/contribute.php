<?php
	include "header.php";
	session_start();
?>
<DOCTYPE html>
<html>
<head>

	<title>Contribute - The GM's Inspiration</title></title>
	<meta name="viewport" content="initial-scale=1">
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/jquery.validate.min.js"></script>
	<script src="scripts/ckeditor/ckeditor.js"></script>
	<!--<script src="scripts/js/utils.js"></script>-->
	<script src="scripts/js/jquery-validation/dist/jquery.validate.min.js"></script>
	<link href="scripts/ckeditor/samples/sample.css" rel="stylesheet">
	<style>
		a {
			cursor: pointer;
		}
		
		p {
			margin-top: .1em;
		}
		
		.bordered {
			border: 2px solid blue;
			border-radius: 5px;
		}
		
		.fadeout {
			opacity: 30%;
		}
		
		div.textarea {
			background-color: #FFFFFF;
			box-shadow: 1px 1px 3px #888888;
			line-height: 1.5;
			border-radius: 4px;
			padding: 0px;
			margin: 0px;
		}
		
	</style>
	<script type="text/javascript" language="javascript">
		function editionCheck(version){
			if(version.value=="other"){
				//document.getElementById("other").style.display="inline-block";
				$("#other").show(300, false);
				$("#other_option").val($("#other").val());
			}else{
				//document.getElementById("other").style.display="none";
				$("#other").hide(300, false);
				$("#other_option").val("other");
			}
		}
		
		function typeCheck(type){
			if(type.value=="Weapon"){
				document.getElementById("wdiv").style.display="block";
			}else{
				document.getElementById("wdiv").style.display="none";
			}
		}
		
		function submitForm(form){
			//('<input type="submit">').hide().appendTo(form).click().remove();
			var newdiv = document.createElement("div");
			
		}
		
		function submit(){
			//Do validation things first to save time
			$("#cont-form").validate();
			if(!$("#cont-form").valid()){
				alert("Please fix the errors noted in red");
				return;
			}
			if($("#game option:selected").text()=="Other"){
				if($("#other").val().length==0){
					alert("Please enter the game and version on the line provided.");
					return;
				}
				if($("#other").val().length>75){
					alert("Max length for game name and version is 75 characters.");
					return;
				}
				if($("#other").val().length<10){
					alert("Game name and version should be at least 10 characters");
					return;
				}
			}
			//Valid, so now we can submit to server
			//Get values
			var privacy=$("#privacy").val();
			var name=$("#name").val();
			var type=$("#type").val();
			var subtype=$("#subtype").val();
			var game=$("#game").val();
			var desc=$("#desc").html();
			var img=$("#img").attr("src");
			var labels=[];
			$("[name*='label ']").each(function() {labels.push($(this).html())});
			var texts=[];
			$("[name*='text ']").each(function() {texts.push($(this).html())});
			var json=new Array();
			for(var x=0; x < labels.length;x++){
				//store elements as objects within the JSON.
				var element = new Object();
				element.label=labels[x];
				element.text=texts[x];
				json[x]=element;
			}
			json=JSON.stringify(json);
			//log to console for debugging
			console.log("Privacy: "+privacy);
			console.log(name);
			console.log(game);
			console.log(type);
			console.log(subtype);
			console.log(desc);
			console.log(img);
			console.log(labels);
			console.log(texts);
			console.log("JSON: "+json);
			//Submit to server
			$.ajax({
				url: "save_contribution.php",
				type: "POST",
				data: {
					privacy: privacy,
					name: name,
					game: game,
					type: type,
					subtype: subtype,
					desc: desc,
					img: img,
					json: json
				},
				success: function(html){
					var div=document.createElement("div");
					$(div).html(html);
					$(div).dialog({
						height: 250,
						width: 400,
						modal: true,
						position: {my: "center top", at: "center top", of: window},
						buttons: {
							"Awwww yeeeah, nat 20!": function(){$(this).dialog("close");}
						}
					});
				},
				error: function(xhr, status, html){
					var div=document.createElement("div");
					$(div).html(html);
					$(div).dialog({
						height: 250,
						width: 350,
						modal: true,
						position: {my: "center top", at: "center top", of: window},
						buttons: {
							"Crap... looks like a crit fail...": function(){$(this).dialog("close");}
						}
					});
				}
			});
		}
		
		function removeField(element){
			//alert("removing: " + element.id);
			$(element).slideUp();
			while(element.firstChild){
				element.removeChild(element.firstChild);
			}
			//var form = document.getElementById("contribute");
			//form.remove(element);
		}
		
		var num = 4;
		function addField(){
			var label=document.createElement("label");
			var div=document.createElement("div");
			$(div).html('<div><h4 id="label '+num+'" name="label '+num+'" style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">New Title</h4>' +
				'<div id="text '+num+'" name="text '+num+'" class="textarea" style="margin-top: .1em" contenteditable="true">Enter yee some words of wonder</div></div>' +
				'<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(\':first-child\').fadeTo(400, .2)" onmouseout="$(this).parent().children(\':first-child\').fadeTo(400, 1)">Delete</a>');
			$(div).hide();
			$("#body").append(div);
			$(div).show("slow");
			CKEDITOR.inline("label "+num);
			CKEDITOR.inline("text "+num);
			num++;
		}
		
		function showBorder(e){
			e.border='2px solid blue';
		}
		
		function editImgSrc(img){
			//var div = document.createElement("div");
			//$(div).html('<label for="src">URL: </label><input type="text" id="src" placeholder="'+img.src+'" />');
			$("#temp_div").dialog({
				height: 300,
				width: 460,
				position: {my: "center top", at: "center top", of: window},
				buttons: ({
					"Accept": function(){
						//console.log($("#temp_div").html());
						img.src=$("#temp").val();
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
<div id="container" class="cf">
	<?php
		if(!$_SESSION["username"]){
			die("You must be logged in in order to access this part of the site.");
		}
	?>
	<div id="contribution"><form id="cont-form">
		<div id="privacy_settings">
			<select id="privacy" title="Select a privacy option" required>
				<option value="0">Public</option>
				<option value="1">Private</option>
				<option value="2">Protected</option>
			</select>
		</div>
		<div class="profile_img"><img id="img" onclick="editImgSrc(this)" width="175" height="175" /></div>
		<div class="name_user_game">
			<h2><input type="text" id="name" name="name" placeholder="Contribution Name" minlength="3" required/> - <select id="type" name="type" required title="Select a type">
				<option value="" disabled selected>Choose Type</option>
				<option value="Armor">Armor</option>
				<option value="Classes">Class</option>
				<option value="Feat">Feat</option>
				<option value="Item">Item</option>
				<option value="Monster">Monster</option>
				<option value="Race">Race</option>
				<option value="Spell">Spell</option>
				<option value="Weapon">Weapon</option>
			</select> (<input id="subtype" name="subtype" type="text" minlength="3" placeholder="Subtype(s)" />)</h2>
			<h3>submitted by <span style="color: blue;"><u><?php echo $_SESSION["username"]; ?></u></span></h3>
			<select id="game" name="game" required oninput="editionCheck(this)">
				<?php 
					$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
					if ($mysql->connect_error) {
						die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
					}
					try{
						$mysql->query("START TRANSACTION");
						$result=$mysql->query("SELECT DISTINCT(game) FROM contributions ORDER BY game ASC");	// This SHOULD be secure.
						while($row=$result->fetch_array(MYSQLI_BOTH)){
							echo "<option value='".htmlspecialchars($row["game"], ENT_QUOTES, "UTF-8")."'>".htmlspecialchars($row["game"], ENT_QUOTES, "UTF-8")."</option>";
						}
						$mysql->commit();
					}catch(Exception $e){
						echo "We appear to have rolled a natural 1... *sigh* Copy the following error message and submit it to us <a href=''>here</a>:</br>".$e;
					}
					$mysql->close();
				?>
				<option value="other" id="other_option">Other</option>
			</select><input id="other" name="other" type="text" minlength="10" maxlength="75" style="display: none" placeholder="Enter Game name: version" title="Example: &quot;Dungeons and Dragons: 5th edition&quot;" onblur="other_option.value=this.value"/></br>
		</div>
		<b>Not yet rated</b>
		<div id="body" style="display: block; clear: both;">
			<h4 style="margin-bottom: .1em; padding-bottom: 0em">Description</h4>
			<div id="desc" class="textarea" style="margin-top: .1em;" contenteditable="true" minlength="25" required>Click here to edit the description</div>
			<div>
				<div class="fade">
					<span><h4 id="label 0" name="label 0" style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">Lore</h4></span>
					<div class="textarea" id="text 0" name="text 0" contenteditable="true">Enter a some mind blowing lore here</div>
				</div>
				<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(':first-child').fadeTo(400, .2)" onmouseout="$(this).parent().children(':first-child').fadeTo(400, 1)">Delete</a>
			</div>
			<div>
				<div class="fade">
					<h4 id="label 1" name="label 1" style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">How to Use</h4>
					<div class="textarea" id="text 1" name="text 1" contenteditable="true">Tell us how to use this</div>
				</div>
				<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(':first-child').fadeTo(400, .2)" onmouseout="$(this).parent().children(':first-child').fadeTo(400, 1)">Delete</a>
			</div>
			<div>
				<div class="fade">
					<h4 id="label 2" name="label 2" style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">Effect</h4>
					<div class="textarea" id="text 2" name="text 2" contenteditable="true">What effect(s) does this have?</div>
				</div>
				<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(':first-child').fadeTo(400, .2)" onmouseout="$(this).parent().children(':first-child').fadeTo(400, 1)">Delete</a>
			</div>
			<div>
				<div class="fade">
					<h4 id="label 3" name="label 3" style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">Attack</h4>
					<div class="textarea" id="text 3" name="text 3" contenteditable="true">How much damage does this do?</div>
					<!--<input id="Attack" name="Attack" type="text" maxlength="60" placeholder="How much damage does this do?" size="26" title="Examples: 3d6, 5, (half your level)+strength modifier, etc"/> -->
				</div>
				<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(':first-child').fadeTo(400, .2)" onmouseout="$(this).parent().children(':first-child').fadeTo(400, 1)">Delete</a>
			</div>
		</div>
	</form></div>
	<script type="text/javascript" language="javascript">
		$("#cont-form").validate({
			debug:true,
			ignore: ":hidden",
			ignoreTitle: true,
			rules: {
				other: {
					minlength: 0,
				}
			}
		});
	</script>
	</br>
		<button class="but" id="add_field" style="border-radius: 10px;" onclick="addField()">Add Field</button>
		<div id="submit_button" style="display: inline-block;"><button class="but" id="submit_contribution"  onclick="submit()">Submit</button></div>
	</br>
</div>
<div id="temp_div" style="display: none"><textarea id="temp" rows="6" cols="38" onkeypress="console.log(event.which)"></textarea></div>
</body>
</html>
<?php include 'footer.php';?>