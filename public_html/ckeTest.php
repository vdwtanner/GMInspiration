<?php 
	session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>A Simple Page with CKEditor</title>
        <!-- Make sure the path to CKEditor is correct. -->
        <script src="scripts/ckeditor/ckeditor.js"></script>
		<link href="scripts/ckeditor/samples/sample.css" rel="stylesheet">
		<style>
	
			/* Style the CKEditor element to look like a textfield */
			.cke_textarea_inline
			{
				padding: 3px;
				height: 50px;
				overflow: auto;
				border: 1px solid gray;
				-webkit-appearance: textfield;
			}
			
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
		
		var extras=0;
		function addField(){
			var table = document.getElementById("fields");
			var tr = document.createElement("tr");
			extras++;
			tr.id=extras;
			tr.innerHTML = "<td><input id='label "+extras+"' name='label "+extras+"' type='text' placeholder='Enter label here' /></td><td><div id='text "+extras+"' name='text "+extras+"' contenteditable='true' class='textarea'>Enter Extra info here</div></td><td><a class='button' onclick='removeField(this.parentNode.parentNode)'>Delete</a></td></br>";
			//tr.innerHTML='<tr><td><label for="desc" >Description:</label></td><td width="300px"><div id="desc'+extras+'" name="desc" class="textarea" contenteditable="true" placeholder="do not die"></div></td></tr>';
			table.appendChild(tr);
			CKEDITOR.inline("text "+extras);
		}
		
		function removeField(element){
			//alert("removing: " + element.id);
			while(element.firstChild){
				element.removeChild(element.firstChild);
			}
			//var form = document.getElementById("contribute");
			//form.remove(element);
		}
		
	</script>
    </head>
    <body>
		<?php
			if(!$_SESSION["username"]){
				die("You must be logged in in order to access this part of the site.");
			}
		?>
		<form id="contribute" method="GET">
		<table id="fields">
			<tr><td>Name</td><td><input id="name" name="name" type="text" pattern=".{1,100}" title="1-100 characters required" maxlength=100" placeholder="Enter kick-ass name here" required/></td></tr>
			<tr><td>Intended Version</td><td><select id="game" name="game" required oninput="editionCheck(this)">
				<?php 
					$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
					if ($mysql->connect_error) {
						die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
					}
					try{
						$mysql->query("START TRANSACTION");
						$result=$mysql->query("SELECT DISTINCT(game) FROM contributions ORDER BY game ASC");
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
				</select><input id="other" name="other" type="text" maxlength="75" style="display: none" placeholder="Enter Game name: version" maxlength="75" title="Example: &quot;Dungeons and Dragons: 5th edition&quot;" onblur="other_option.value=this.value"/></td></tr>
			<tr><td>Contribution Type</td><td><select id="type" name="type" required >
				<option value="Weapon">Weapon</option>
				<option value="Spell">Spell</option>
				<option value="Consumable">Consumable</option>
				<option value="Crafting">Crafting</option>
				<option value="Feat">Feat</option>
				<option value="Artifact">Artifact</option>
				<option value="Tool">Tool</option>
				</select></td></tr>
			<tr><td>Sub type</td><td><input id="Sub_type" name="Sub_type" type="text" placeholder="Enter sub type(s) here" maxlength="75" /></td><td><a class="button" onclick="removeField(this.parentNode.parentNode)">Delete</a></td></tr>
			<tr><td>Link to image</td><td><input id="img" name="img" type="text" placeholder="Paste image URL here" /></td><td><a class="button" onclick="removeField(this.parentNode.parentNode)">Delete</a></td></tr>
			<tr><td><label for="desc" >Description:</label></td><td width="300px"><div id="desc" name="desc" class="textarea" contenteditable="true">Enter a bad ass description here</div></td></tr>
			<tr><td><label for="Lore" >Lore:</label></td><td width="300px"><div class="textarea" id="Lore" name="Lore" contenteditable="true">Enter a some mind blowing lore here</div></td><td><a class="button" onclick="removeField(this.parentNode.parentNode)">Delete</a></td></tr>
			<tr><td><label for="How to use">How to use:</label></td><td width="300px"><div class="textarea" id="How to use" name="How to use" contenteditable="true">Tell us how to use this</div></td><td><a class="button" onclick="removeField(this.parentNode.parentNode)">Delete</a></td></tr>
			<tr><td>Effect</td><td width="300px"><div class="textarea" id="Effect" name="Effect" contenteditable="true">What effect(s) does this have?</div></td><td><a class="button" onclick="removeField(this.parentNode.parentNode)">Delete</a></td></tr>
			<tr><td>Attack</td><td width="300px"><input id="Attack" name="Attack" type="text" maxlength="60" placeholder="How much damage does this do?" size="26" title="Examples: 3d6, 5, (half your level)+strength modifier, etc"/></td><td><a class="button" onclick="removeField(this.parentNode.parentNode)">Delete</a></td></tr>
		</table>		
		</br>
	</form>
		<button id="add_field" style="border-radius: 10px;" onclick="addField()">Add Field</button>
		<div id="submit_button"><button id="submit_contribution" style="border-radius: 10px;" onclick="submitForm(contribute)">Submit</button></div>
	</br>
    </body>
</html>