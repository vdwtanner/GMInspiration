<?php
        session_start();
        require dirname(__FILE__)."/scripts/parser.php";

		require_once dirname(__FILE__).'/HTMLPurifier/library/HTMLPurifier.auto.php';
		$purifier = new HTMLPurifier();

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
		$json=$purifier->purify($_POST["json"]);
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