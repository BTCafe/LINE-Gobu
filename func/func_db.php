<?php	

	class database 
	{
		
		function __construct()
		{
			
		}

		// Log function to store any transaction data to the database
		static function create_universal_log_data ($source, $command, $db_conf) {
			if (!isset($source['userId'])) {
				$choosenID = 'groupId' ;
				if (!isset($source['groupId'])) {
					$choosenID = 'roomId' ;
				} 
			} else {
				$choosenID = 'userId' ;
			}

	    	$query = "INSERT INTO `GOBU_DIARY` (`DATE`, `USER_ID`, `COMMAND`) VALUES ('" .
				date('Y-m-d h:i:s e') . "','" . $source[$choosenID] . "','" . $command . "')"; 

			mysqli_query($db_conf, $query);
		}

		// Log function to store any transaction data to the database
		static function create_function_log_data ($source, $command, $db_conf) {
			if (!isset($source['userId'])) {
				$choosenID = 'groupId' ;
				if (!isset($source['groupId'])) {
					$choosenID = 'roomId' ;
				} 
			} else {
				$choosenID = 'userId' ;
			}

	    	$query = "INSERT INTO `USED_FUNCTION` (`DATE`, `USER_ID`, `COMMAND`) VALUES ('" .
				date('Y-m-d h:i:s e') . "','" . $source[$choosenID] . "','" . $command . "')";  

			mysqli_query($db_conf, $query);
		}

		// Log function to store executed data to the database
		static function create_log_data ($source, $command, $criteria, $db_conf) {
			if (!isset($source['roomId'])) {
				$choosenID = 'groupId' ;
				if (!isset($source['groupId'])) {
					$choosenID = 'userId' ;
				} 
			} else {
				$choosenID = 'roomId' ;
			}

	    	$query = "INSERT INTO `SUCCESS_LOG` (`DATE`, `USER_ID` , `COMMAND`, `CRITERIA`) VALUES ('" .
				date('Y-m-d h:i:s e') . "','" . $source[$choosenID] . "','"  . $command .  "','" . $criteria . "')";  

			mysqli_query($db_conf, $query);
		}

		// New log function to store Urban Dictionary Command
		static function create_log_data_ud ($source, $command, $criteria, $db_conf) {
			if (!isset($source['roomId'])) {
				$choosenID = 'groupId' ;
				if (!isset($source['groupId'])) {
					$choosenID = 'userId' ;
				} 
			} else {
				$choosenID = 'roomId' ;
			}

	    	$query = "INSERT INTO `URBAN_DICTIONARY` (`DATE`, `USER_ID` , `COMMAND`, `CRITERIA`) VALUES ('" .
				date('Y-m-d h:i:s e') . "','" . $source[$choosenID] . "','"  . $command .  "','" . $criteria . "')";  

			mysqli_query($db_conf, $query);
		}

		// New log function to store executed data to the database
		static function create_log_data_specific ($source, $command, $criteria, $db_conf, $card_name, $expansion_origin) {
			if (!isset($source['roomId'])) {
				$choosenID = 'groupId' ;
				if (!isset($source['groupId'])) {
					$choosenID = 'userId' ;
				} 
			} else {
				$choosenID = 'roomId' ;
			}

			$id = $source[$choosenID];

	    	$query = "INSERT INTO `SUCCESS_LOG` (`DATE`, `USER_ID` , `COMMAND`, `CRITERIA`, `SPECIFIC_CARD`, `EXPANSION_ORIGINS`) VALUES ('" .
				date('Y-m-d h:i:s e') . "','$id','$command','$criteria', '$card_name', '$expansion_origin')";  

			mysqli_query($db_conf, $query);
		}

		static function update_log_setting ($function_log, $universal_log){
			if ($function_log == " " || $universal_log == " ") {
				return "An error occured, setting unchanged" ;
			} else {
				$new_setting = "function_log=" . $function_log . "\nuniversal_log=" . $universal_log ;
				file_put_contents('./conf/admin_setup.txt', $new_setting, LOCK_EX);
				return "Setting changed" ;
			}
		}

		static function get_animated_url ($name, $is_evo, $db_conf){
			$name = trim($name);
			$query = "SELECT * FROM `ANIMATED_TABLE` WHERE NAME='" . $name . "' AND EVOLVE='" . $is_evo . "'" ;
			$query_result = mysqli_query($db_conf, $query);
			if ($query_result) {
				if ( mysqli_num_rows($query_result) == 0 ) {
					return "Not found / available yet, sorry~" ;
				} else {
					$query_fetch = mysqli_fetch_array($query_result);
					if ($query_fetch['GOOGLE_URL'] != "") {
						$animated_url = $query_fetch['GOOGLE_URL'] ;
					} else {
						$animated_url = $query_fetch['ANI_URL'] ;
					}
					return $animated_url ;
				}
			} else {
				return "Not found / available yet, sorry~";
			}
		}

		static function check_arg_participation ($source, $db_conf) {
			$id = $source['userId'] ;

			$query = "SELECT COUNT(USER_ID) AS PARTICIPANT_EXIST FROM `JUST_AGGRO` WHERE USER_ID = '" . $id . "'" ;  

			$search_result = mysqli_query($db_conf, $query);
			$row = mysqli_fetch_assoc($search_result);

			if ($row['PARTICIPANT_EXIST'] == 0) {
				return TRUE ;
			} else {
				return FALSE ;
			}
		}

		static function get_number_of_participant ($db_conf) {
			$query = "SELECT COUNT(USER_ID) AS PARTICIPANT_SIZE FROM `JUST_AGGRO`" ;  

			$search_result = mysqli_query($db_conf, $query);
			$row = mysqli_fetch_assoc($search_result);
			return $row['PARTICIPANT_SIZE'] ;
		}

		static function create_log_data_for_arg ($source, $placement, $db_conf) {
			$choosenID = 'userId' ;
	    	$query = "INSERT INTO `JUST_AGGRO` (`DATE`, `USER_ID`, `PLACEMENT`) VALUES ('" .
				date('Y-m-d h:i:s e') . "','" . $source[$choosenID] . "' , '$placement')";  

			mysqli_query($db_conf, $query);
		}

		static function do_daily ($source, $db_conf) {
			$id_user = $source['userId'] ;

			$query = "SELECT * FROM `USER_ECONOMY` WHERE ID_USER = '" . $id_user . "'";
			$query_result = mysqli_query($db_conf, $query);

			if ( mysqli_num_rows($query_result) == 0 ) {
				$query = "INSERT INTO `USER_ECONOMY` (`ID_USER`, `LAST_DAILY` , `POINTS`) VALUES ('" .
						$id_user . "','" .
						date('Y-m-d H:i:s') . "','" .
						1000 . "')";  

				mysqli_query($db_conf, $query);
				return "here's your daily salary - 1000 points given" ;
			} else {
				$query_fetch = mysqli_fetch_array($query_result);
				$current_datetime = date('Y-m-d H:i:s');
				$last_daily_time = $query_fetch['LAST_DAILY'] ;

				$date1 = new DateTime($current_datetime);
				$date2 = new DateTime($last_daily_time);
				$interval = $date1->diff($date2);
				$hours_difference = $interval->h ;
				$days_difference = $interval->d ;

				if ($days_difference >= 1) {
					$new_points = $query_fetch['POINTS'] + 1000;
			    	$query = "UPDATE `USER_ECONOMY` SET `POINTS`=" . $new_points . ", `LAST_DAILY`='" . $current_datetime . "' WHERE ID_USER='" . $id_user . "'" ;

					mysqli_query($db_conf, $query);
					return "here's your daily salary - 1000 points given" ;
				} else {
					$grace_period = 24 - $hours_difference ;
					return "you can't do daily now, please come back again in " . $grace_period . " hours" ;
				}
			}

		}

		static function get_points ($source, $db_conf) {

			$query = "SELECT POINTS FROM `USER_ECONOMY` WHERE ID_USER = '" . $source['userId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			$current_points = $query_fetch['POINTS'] ;
			return $current_points ;
			
		}

		static function manage_points ($source, $db_conf, $value, $type){
			$query = "SELECT * FROM `USER_ECONOMY` WHERE ID_USER = '" . $source['userId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			$current_points = (int)$query_fetch['POINTS'] ;
			$new_points = 0 ;
			$response = "" ;

			if ($current_points < $value) {
				return "You don't have enough points!";
			}

			switch ($type) {
				case 1:
					$new_points = $current_points + $value;
					$response = "You won " . $value . " points!";
					break;
				
				case 2:
					$new_points = $current_points - $value;
					$response = "You got scammed and lose " . $value . " points!";
					break;
			}

	    	$query = "UPDATE `USER_ECONOMY` SET `POINTS`=" . $new_points . " WHERE ID_USER='" . $source['userId'] . "'" ;
			mysqli_query($db_conf, $query);

			return $response ;
		}

	}
?>