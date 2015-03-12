<?php

	
	function getResultArray($stmt, $byref_array_for_fields){
		$meta = $stmt->result_metadata();

		while($field = $meta->fetch_field()){
			$params[] = &$row[$field->name];
		}

		call_user_func_array(array($stmt, "bind_result"), $params);

		$copy = create_function("$a", "return $a;");

		$results = array();
		while($stmt->fetch()){
			$results[] = array_map($copy, $params);
		}

	}

?>
