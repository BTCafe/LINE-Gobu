<?php	
	// Log function to store any transaction data to the database
	function create_universal_log_data ($source, $command, $db_conf) {
		if (!isset($source['userId'])) {
			$choosenID = 'groupId' ;
			if (!isset($source['groupId'])) {
				$choosenID = 'roomId' ;
			} 
		} else {
			$choosenID = 'userId' ;
		}

    	$query = "INSERT INTO `GOBU_DIARY` (`DATE`, `USER_ID`, `COMMAND`) VALUES ('" .
			date('Y-m-d h:i:s e') . "','" . $source[$choosenID] . "','" . $command . "')"; ;  

		mysqli_query($db_conf, $query);
	}

	// Log function to store any transaction data to the database
	function create_function_log_data ($source, $command, $db_conf) {
		if (!isset($source['userId'])) {
			$choosenID = 'groupId' ;
			if (!isset($source['groupId'])) {
				$choosenID = 'roomId' ;
			} 
		} else {
			$choosenID = 'userId' ;
		}

    	$query = "INSERT INTO `USED_FUNCTION` (`DATE`, `USER_ID`, `COMMAND`) VALUES ('" .
			date('Y-m-d h:i:s e') . "','" . $source[$choosenID] . "','" . $command . "')"; ;  

		mysqli_query($db_conf, $query);
	}

	function update_log_setting ($function_log, $universal_log){
		if ($function_log == " " || $universal_log == " ") {
			return "An error occured, setting unchanged" ;
		} else {
			$new_setting = "function_log=" . $function_log . "\nuniversal_log=" . $universal_log ;
			file_put_contents('./conf/admin_setup.txt', $new_setting, LOCK_EX);
			return "Setting changed" ;
		}
	}

	function get_animated_url ($name, $is_evo, $db_conf){
		$name = trim($name);
		$query = "SELECT `ANI_URL` FROM `ANIMATED_TABLE` WHERE NAME='" . $name . "' AND EVOLVE='" . $is_evo . "'" ;
		$query_result = mysqli_query($db_conf, $query);
		if ($query_result) {
			if ( mysqli_num_rows($query_result) == 0 ) {
				return "Not found / available yet, sorry~" ;
			} else {
				$query_fetch = mysqli_fetch_array($query_result);
				$animated_url = $query_fetch['ANI_URL'] ;
				return $animated_url ;
			}
		} else {
			return "Not found / available yet, sorry~";
		}


	}
?>