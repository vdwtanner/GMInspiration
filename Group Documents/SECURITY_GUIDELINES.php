----------------------------------------------------------------------

		SECURITY GUIDELINES FOR WEB PROGRAMMING

----------------------------------------------------------------------

	Please refer to this document when doing any of
	the following:
		- executing MySQL queries inside php
		- Retrieving input from the user
		- outputting ANYTHING that is user created
		  to the page

----------------------------------------------------------------------
		EXECUTING MYSQL QUERIES INSIDE PHP
----------------------------------------------------------------------

	- For the love of all that is holy, use prepared statements
	- Prepared statements give mysql an unambiguous case for
	  inputting user defined data. This prevents MySQL injections.
	- MySQL injections are scary.

	
	/*** EXAMPLE OF PREPARED STATEMENT VS. REGULAR QUERY FOR INSERTION ***/
	<?php
		// connect to mysql database, name the connection var $mysql

		// You still need this
		$mysql->query("START TRANSACTION");

		/*** REGULAR QUERY (DO NOT USE) ***/
		//$result = $mysql->prepare("INSERT INTO contribution_comments (contribution_id, username, comment) VALUES (".$cid.",".$user.",".$comment.")");

		/*** QUERY WITH PREPARED STATEMENTS ***/
		// First, tell the database to save a template statement. The question marks here are where our params will go.
		$stmt = $mysql->prepare("INSERT INTO contribution_comments (contribution_id, username, comment) VALUES (?,?,?)");

		// Tell the statement what data goes in place of those '?'. The first parameter is a string where you specify
		// the type of each parameter to come. This string tells the $stmt object that we are inputting an integer, string, and string.
		// also, this function binds the question marks in order, so our first ? has the value of $cid now, second ? has the value of $user etc...
		$stmt->bind_param("iss", $cid, $user, $comment);

		// Execute the command, if it returns false, it failed, print the error num and stuff.
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		// Very important here to close the $stmt after we are completely done using the object.
		$stmt->close();

	?>

	/*** EXAMPLE OF PREPARED STATEMENT VS. REGULAR QUERY FOR SELECTION ***/
	<?php
		// connect to mysql database, name the connection var $mysql

		$mysql->query("START TRANSACTION");

		/*** REGULAR QUERY (DO NOT USE) ***/
		$result = $mysql->query("SELECT picture FROM users WHERE username=".$row["username"]);
		// please note how this query above is vulnerable when the user's name is "1 OR 1=1"
		// then get data from result with fetch_array

		/*** QUERY WITH PREPARED STATEMENTS ***/
		// Tell the database to save a template statement.
		$stmt = $mysql->prepare("SELECT picture FROM users WHERE username=?");
		
		// Tell our statement to bind $row["username"], which is a string, to the question mark
		$stmt->bind_param("s", $row["username"]);
		
		// Execute the statement
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}

		// Create a var to hold our results
		$img=null;

		// Give stmt the variable, however the value of $img is unchanged until...
		$stmt->bind_result($img);

		// you call fetch. This modifies the contents of $img to contain picture
		$stmt->fetch();

		// Close the statement.
		$stmt->close();

		// NOTE: if you have multiple columns you wish to select from a table, you bind the results in
		//	 the same order that you listed the columns in. For example:
		//	 	if we SELECT picture, username, id FROM ...
		// 		we have to fetch($pic_holder, $user_holder, $id_holder)



	?> 

	

	/*** EXAMPLE OF FETCHING MULTIPLE ROWS WHEN SELECTING WITH PREPARED STATEMENTS ***/
	<?php
		// the statement object you get from calling mysql->prepared does not have a fetch array_function or
		// anything of the like, so we have to modify our code a bit to fetch all rows that match our select
		// statement

		// connect to mysql database, name the connection var $mysql

		// Tell the database to save a template statement
		$stmt = $mysql->prepare("SELECT username, timestamp, comment, fun, balance FROM ratings WHERE contribution_id=? ORDER BY timestamp DESC");

		// Tell our statement to bind $id to the question mark
		$stmt->bind_param("i", $id);

		// execute the statement
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}

		// create vars to hold each column of our result
		$u=null; $t=null; $c=null; $f=0; $b=0;

		// bind those vars the columns
		$stmt->bind_result($u, $t, $c, $f, $b);

		// create an associative array
		while($stmt->fetch()){
			$row["username"] = $u;
			$row["timestamp"] = $t;
			$row["comment"] = $c;
			$row["fun"] = $f;
			$row["balance"] = $b;
			$rowarr[] = $row;
		}

		// $rowarr now contains all of the rows whose contribution_id column was equal to $id
		// We can loop through the data to display it or do whatever with a foreach loop
		foreach($rowarr as $key => $row){
			// Do stuff
		}

		// Close the statement
		$stmt->close();
	

	?>


----------------------------------------------------------------------
		RETRIEVING INPUT FROM USER
----------------------------------------------------------------------









