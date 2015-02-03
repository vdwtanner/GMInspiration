<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>
	<title>Contribute</title>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	
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
			var div = document.getElementById("extra");
			var newdiv = document.createElement("div");
			extras++;
			newdiv.id=extras;
			newdiv.innerHTML = "<input id='label "+extras+"' name='label "+extras+"' type='text' style='vertical-align: top' placeholder='Enter label here' /><textarea id='text "+extras+"' name='text "+extras+"' placeholder='Enter extra info here' rows='5' cols='50'></textarea></br>";
			div.appendChild(newdiv);
		}
		
		function removeField(element){
			var form = document.getElementById("contribute");
			form.remove
		}
		
	</script>
</head>
<body>
	<h2>Dungeon Crawlers - Contribute</h2>
	<?php
		if(!$_SESSION["username"]){
			die("You must be logged in in order to access this part of the site.");
		}
	?>
	<form id="contribute" method="POST" action="save_contribution.php">
		<label for="name">Name:</label><input id="name" name="name" type="text" pattern=".{1,100}" title="1-100 characters required" maxlength=100" placeholder="Enter kick-ass name here" required/></br>
		<label for="game">Intended Version:</label><select id="game" name="game" required oninput="editionCheck(this)">
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
		</select><input id="other" name="other" type="text" maxlength="75" style="display: none" placeholder="Enter Game name: version" maxlength="75" title="Example: &quot;Dungeons and Dragons: 5th edition&quot;" onblur="other_option.value=this.value"/></br>
		<?php echo $e;?>
		<label for="type">Contribution Type:</label><select id="type" name="type" required oninput="typeCheck(this)">
			<option value="Weapon">Weapon</option>
			<option value="Spell">Spell</option>
			<option value="Consumable">Consumable</option>
			<option value="Crafting">Crafting</option>
			<option value="Feat">Feat</option>
			<option value="Artifact">Artifact</option>
			<option value="Tool">Tool</option>
		</select></br>
		<div id="wdiv" style="display: none"><label for="wtype">Weapon type:</label><input id="wtype" name="wtype" type="text" placeholder="Enter weapon type here" /></div>
		<label for="desc" style="vertical-align: top">Description:</label><textarea id="desc" name="desc" required placeholder="Enter a bad ass description here" rows="5" cols="50"></textarea></br>
		<label for="Lore" style="vertical-align: top">Lore:</label><textarea id="Lore" name="Lore" placeholder="Enter a some mind blowing lore here" rows="5" cols="50"></textarea></br>
		<label for="How to use" style="vertical-align: top">How to use:</label><textarea id="How to use" name="How to use" placeholder="Tell us how to use this" rows="5" cols="50"></textarea></br>
		<label for="Effect" style="vertical-align: top">Effect:</label><textarea id="Effect" name="Effect" placeholder="What effect(s) does this have?" rows="5" cols="50"></textarea></br>
		<label for="Attack">Attack:</label><input id="Attack" name="Attack" type="text" maxlength="60" placeholder="How much damage does this do?" size="26" title="Examples: 3d6, 5, (half your level)+strength modifier, etc"/></br>
		<div id="extra"></div>
		</br>
	</form>
	<button style="border-radius: 10px;" onclick="addField()">Add Field</button>
	<button style="border-radius: 10px;" onclick="submitForm(contribute)">Submit</button>
	</br>
	
	<a href="index.html"><button style="border-radius: 10px;">Home</button></a>
</body>
</html>