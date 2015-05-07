<?php
        session_start();
        require dirname(__FILE__)."/scripts/parser.php";

		require_once dirname(__FILE__).'/HTMLPurifier/library/HTMLPurifier.auto.php';

		$allowedEle = "b,i,u,li,ol,ul,table,tr,td,th,br,p";

		$allowedAttri = "p.style, p.class, table.style, table.class, table.width,
				table.cellpadding, table.cellspacing, table.border, table.id
				td.abbr, td.align, td.class, td.id, td.colspan, td.rowspan, td.style,
				td.valign, tr.align, tr.class, tr.id, tr.style, tr.valign, th.abbr,
				th.align, th.class, th.id, th.colspan, th.rowspan, th.style,
				th.valign, ul.style";

		$config = HTMLPurifier_Config::createDefault();
		$config->set("Core.Encoding", "UTF-8");
		$config->set("HTML.AllowedElements", $allowedEle);
		$config->set("HTML.AllowedAttributes", $allowedAttri);
		$purifier = new HTMLPurifier($config);

                if(!$_SESSION["username"]){
                        header("HTTP/1.1 401 You are not logged in");
                        die("You must be logged in in order to access this part of the site.");
                }
                if(!$_POST["id"]){
                        print_r($_POST);
                        //header("HTTP/1.1 412 Contribution ID not found");
                        die("No ID found, cannot complete update.");
                }
                //echo "Welcome to the contribution screen, ".$_SESSION["username"];
                //echo "</br>";
                $parser = new parser;
                $id=$_POST["id"];
		$privacy=$_POST["privacy"];
		$name=$_POST["name"];	
		$game=$_POST["game"];	
		$type=$_POST["type"];	
		$subtype=$_POST["subtype"];	

		$desc=$purifier->purify($_POST["desc"]);	
		$img=$purifier->purify($_POST["img"]);
		$json=json_decode($_POST["json"], true);
		foreach($json as $key => $value){
			$value["label"] = $purifier->purify($value["label"]);
			$value["text"] = $purifier->purify($value["text"]);
		}
		$json=json_encode($json);


		//print_r(htmlspecialchars($_POST["json"], ENT_QUOTES, "UTF-8"));
		//print_r(htmlspecialchars($json, ENT_QUOTES, "UTF-8"));
		//print_r(htmlspecialchars($desc, ENT_QUOTES, "UTF-8"));
		
		if ($img==""){
			if ($type==Armor){
				$img = "img/Armor200.png";
			}
			if ($type==Classes){
				$img = "img/Classes200.png";
			}
			if ($type==Feat){
				$img = "img/Feats200.png";
			}
			if ($type==Item){
				$img = "img/Items200.png";
			}
			if ($type==Monster){
				$img = "img/Monsters200.png";
			}
			if ($type==Race){
				$img = "img/Races200.png";
			}
			if ($type==Spell){
				$img = "img/Spells200.png";
			}
			if ($type==Weapon){
				$img = "img/Weapons200.png";
			}
		}
                if($game=="other"){
                        $game=$_POST["other"];
                }
                //echo $json;
                //print_r($array);
                //echo "</br>";
                $mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
                if ($mysql->connect_error) {
                        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
                }
                //$json = $mysql->real_escape_string(json_encode($array));
                //print($json);
                try{
                        $mysql->query("START TRANSACTION");
						$stmt=$mysql->prepare("SELECT username FROM contributions WHERE id=?");
						$stmt->bind_param("i", $id);
						if(!$stmt->execute()){
                            header("HTTP/1.1 500 Failed to check identity.</br> (".$stmt->errno.") ".$stmt->error);
							die;
                        }
						$username=null;
						$stmt->bind_result($username);
						$stmt->fetch();
						$stmt->close();
						if($_SESSION["username"]!=$username){
							header("HTTP/1.1 401 NO HACKING ALLOWED DAMMIT");
							die;
						}
                        if($img){
                                //$stmt=$mysql->prepare("UPDATE contributions SET name=?");
                                $stmt=$mysql->prepare("UPDATE contributions SET name=?, `type`=?, sub_type=?, game=?, `desc`=?, json=?, img=?, privacy=? WHERE id=?");
                                //$stmt->bind_param("s", $name);
                                $stmt->bind_param("sssssssii", $name, $type, $subtype, $game, $desc, $json, $img, $privacy, $id);
                                if(!$stmt->execute()){
                                        header("HTTP/1.1 500 Failed to execute update command1.</br> (".$stmt->errno.") ".$stmt->error);
                                }
                                
                                //$mysql->query("UPDATE contributions SET name='".$name."', `type`='".$type."', sub_type='".$subtype."', game='".$game."', `desc`='".$desc."', json='".$json."', img='".$img."' WHERE id=".$id."");
                        }else{
                                $stmt=$mysql->prepare("UPDATE contributions SET name=?, `type`=?, sub_type=?, game=?, `desc`=?, json=?, privacy=? WHERE id=?");
                                $stmt->bind_param("ssssssii", $name, $type, $sub_type, $game, $desc, $json, $privacy, $id);
                                if(!$stmt->execute()){
                                        header("HTTP/1.1 500 Failed to execute update command2.</br> (".$stmt->errno.") ".$stmt->error);
                                }
                                //$mysql->query("UPDATE contributions SET name='".$name."', `type`='".$type."', sub_type='".$subtype."', game='".$game."', `desc`='".$desc."', json='".$json."' WHERE id=".$id."");
                        }
                        echo $_SESSION["username"].", Your contribution was successfully updated.";
                        $mysql->commit();
                        $stmt->close();
                }catch(Exception $e){
                        $mysql->rollback();
                        header("HTTP/1.1 500 Unexpected Error occurred.");
                        die($e);
                }
                $mysql->close();
        ?>
