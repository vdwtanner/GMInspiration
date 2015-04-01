<?php
	include "header.php";
	session_start();
?>
<DOCTYPE html>
<html>
<head>
	<title>Contribute</title>
	<meta name="viewport" content="initial-scale=1">
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/jquery.validate.min.js"></script>
	<script src="scripts/ckeditor/ckeditor.js"></script>
	<script src="scripts/js/utils.js"></script>
	<link href="scripts/ckeditor/samples/sample.css" rel="stylesheet">
	<style>
		a {
			cursor: pointer;
		}
		a.button {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #FFAD33;
			border-radius: 10px;
		}
		a.button:hover {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #EC9C2E;
			border-radius: 10px;
		}
		a.button:active {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #E8A643;
			border-radius: 10px;
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
				document.getElementById("other").style.display="inline-block";
			}else{
				document.getElementById("other").style.display="none";
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
			newdiv.innerHTML = "<input id='hidden_sub' type='submit' style='display: none'>";
			form.appendChild(newdiv);
			document.getElementById("hidden_sub").click();
			form.remove(newDiv);
			if(form.checkValidity()){
				form.submit();
			}
		}
		
		function submit(){
			alert("still working on that...");
		}
		
		/*var extras=0;
		function addField(){
			var div = document.getElementById("extra");
			var newdiv = document.createElement("div");
			extras++;
			newdiv.id=extras;
			newdiv.innerHTML = "<input id='label "+extras+"' name='label "+extras+"' type='text' style='vertical-align: top' placeholder='Enter label here' /><textarea id='text "+extras+"' name='text "+extras+"' placeholder='Enter extra info here' rows='5' cols='50'></textarea><a class='button' onclick='removeField(this.parentNode)'>Delete</a></br>";
			div.appendChild(newdiv);
		}*/
		
		function removeField(element){
			//alert("removing: " + element.id);
			$(element).slideUp();
			while(element.firstChild){
				element.removeChild(element.firstChild);
			}
			//var form = document.getElementById("contribute");
			//form.remove(element);
		}
		
		var num = 0;
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
		
	</script>
</head>
<body>
<div id="container" class="cf">
	<h2 id="page_title">Dungeon Crawlers - Contribute</h2>
	<?php
		if(!$_SESSION["username"]){
			die("You must be logged in in order to access this part of the site.");
		}
	?>
	<div id="contribution">
		<div class="profile_img"><img id="img" onclick="editImgSrc(this)" width="175" height="175" /></div>
		<div class="name_user_game">
			<h2><input type="text" id="name" placeholder="Contribution Name" /> - <select id="type" name="type" required title="Select a type">
				<option value="Weapon">Weapon</option>
				<option value="Spell">Spell</option>
				<option value="Consumable">Consumable</option>
				<option value="Crafting">Crafting</option>
				<option value="Feat">Feat</option>
				<option value="Artifact">Artifact</option>
				<option value="Tool">Tool</option>
			</select> (<input id="subtype" type="text" placeholder="Subtype(s)" />)</h2>
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
							echo "<option value='".$row["game"]."'>".$row["game"]."</option>";
						}
						$mysql->commit();
					}catch(Exception $e){
						echo "We appear to have rolled a natural 1... *sigh* Copy the following error message and submit it to us <a href=''>here</a>:</br>".$e;
					}
					$mysql->close();
				?>
				<option value="other" id="other_option">Other</option>
			</select><input id="other" name="other" type="text" maxlength="75" style="display: none" placeholder="Enter Game name: version" maxlength="75" title="Example: &quot;Dungeons and Dragons: 5th edition&quot;" onblur="other_option.value=this.value"/></br>
		</div>
		<b>Not yet rated</b>
		<div id="body" style="display: block; clear: both;">
			<h4 style="margin-bottom: .1em; padding-bottom: 0em">Description</h4>
			<div id="desc" class="textarea" style="margin-top: .1em;" contenteditable="true">Click here to edit the description</div>
			<div>
				<div class="fade">
					<span><h4 style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">Lore</h4></span>
					<div class="textarea" id="Lore" name="Lore" contenteditable="true">Enter a some mind blowing lore here</div>
				</div>
				<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(':first-child').fadeTo(400, .2)" onmouseout="$(this).parent().children(':first-child').fadeTo(400, 1)">Delete</a>
			</div>
			<div>
				<div class="fade">
					<h4 style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">How to Use</h4>
					<div class="textarea" id="How to use" name="How to use" contenteditable="true">Tell us how to use this</div>
				</div>
				<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(':first-child').fadeTo(400, .2)" onmouseout="$(this).parent().children(':first-child').fadeTo(400, 1)">Delete</a>
			</div>
			<div>
				<div class="fade">
					<h4 style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">Effect</h4>
					<div class="textarea" id="Effect" name="Effect" contenteditable="true">What effect(s) does this have?</div>
				</div>
				<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(':first-child').fadeTo(400, .2)" onmouseout="$(this).parent().children(':first-child').fadeTo(400, 1)">Delete</a>
			</div>
			<div>
				<div class="fade">
					<h4 style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">Effect</h4>
					<input id="Attack" name="Attack" type="text" maxlength="60" placeholder="How much damage does this do?" size="26" title="Examples: 3d6, 5, (half your level)+strength modifier, etc"/>
				</div>
				<a class="button" onclick="removeField(this.parentNode)" onmouseover="$(this).parent().children(':first-child').fadeTo(400, .2)" onmouseout="$(this).parent().children(':first-child').fadeTo(400, 1)">Delete</a>
			</div>
		</div>
	</div>
	</br>
		<button id="add_field" style="border-radius: 10px;" onclick="addField()">Add Field</button>
		<div id="submit_button"><button id="submit_contribution" style="border-radius: 10px;" onclick="submit()">Submit</button></div>
	</br>
	
	<a href="index.html"><button style="border-radius: 10px;">Home</button></a>
</div>
</body>
</html>
