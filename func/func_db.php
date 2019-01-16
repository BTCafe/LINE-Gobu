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

			$query = "SELECT * FROM `WAIFU_LIST` WHERE CURRENT_CLAIMER = '" . $source['userId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$waifu_count = mysqli_num_rows($query_result); 

			$query = "SELECT * FROM `USER_ECONOMY` WHERE ID_USER = '" . $id_user . "'";
			$query_result = mysqli_query($db_conf, $query);
			$base_daily_rewards = 2500 * (1 + $waifu_count);

			if ( mysqli_num_rows($query_result) == 0 ) {

				$query = "INSERT INTO `USER_ECONOMY` (`ID_USER`, `LAST_DAILY` , `POINTS`) VALUES ('" .
						$id_user . "','" .
						date('Y-m-d H:i:s') . "','" .
						$base_daily_rewards . "')";  

				mysqli_query($db_conf, $query);
				return sprintf("Here's your daily salary \n-- %d points given --", $base_daily_rewards) ;
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
					$new_points = $query_fetch['POINTS'] + $base_daily_rewards;
					if ($new_points > 250000) {
						$new_points = 250000 ;
					}
			    	$query = "UPDATE `USER_ECONOMY` SET `POINTS`=" . $new_points . ", `LAST_DAILY`='" . $current_datetime . "' WHERE ID_USER='" . $id_user . "'" ;

					mysqli_query($db_conf, $query);
					return sprintf("Here's your daily salary \n-- %d points given --", $base_daily_rewards) ;
				} else {
					$grace_period = 24 - $hours_difference ;
					return "You can't do daily now, please come back again in " . $grace_period . " hours" ;
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

		static function get_waifu_count ($source, $db_conf) {

			$query = "SELECT * FROM `WAIFU_LIST` WHERE CURRENT_CLAIMER = '" . $source['userId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$waifu_count = mysqli_num_rows($query_result); 
			return $waifu_count ;
			
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

			if ($value <= 0) {
				return "You can't bet with that number!" ;
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

		static function get_item ($source, $db_conf, $rarity) {

			$query = "SELECT * FROM `ITEM_LIST` WHERE RARITY = '" . $rarity . "'";
			$query_result = mysqli_query($db_conf, $query);
			
			while ($current_row = mysqli_fetch_assoc($query_result)) {
				$item_list[] = $current_row ;
			}

			return $item_list ;
			
		}

		static function get_last_hunt ($source, $db_conf){

			$query = "SELECT LAST_HUNT FROM `USER_ECONOMY` WHERE ID_USER = '" . $source['userId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			return $query_fetch['LAST_HUNT'] ;

		}

		static function update_last_hunt ($source, $db_conf){

			$new_datetime = date('Y-m-d H:i:s');
			$query = "UPDATE `USER_ECONOMY` SET `LAST_HUNT`='" . $new_datetime . "' WHERE ID_USER='" . $source['userId'] . "'" ;
			mysqli_query($db_conf, $query);

		}

		static function modify_points ($source, $db_conf, $value, $type){
			$query = "SELECT * FROM `USER_ECONOMY` WHERE ID_USER = '" . $source['userId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			$current_points = (int)$query_fetch['POINTS'] ;
			$new_points = 0 ;
			$response = "" ;

			switch ($type) {
				case 0:
					$new_points = $current_points - $value ;
					break;
				
				case 1:
					$new_points = $current_points + $value ;
					break;
			}
			
			$query = "UPDATE `USER_ECONOMY` SET `POINTS`=" . $new_points . " WHERE ID_USER='" . $source['userId'] . "'" ;
			mysqli_query($db_conf, $query);

		}

		static function check_economy_availabilty ($source, $db_conf){

			$id_user = $source['userId'] ;
			$query = "SELECT * FROM `USER_ECONOMY` WHERE ID_USER = '" . $id_user . "'";
			$query_result = mysqli_query($db_conf, $query);
			$is_created = mysqli_num_rows($query_result); 
			return $is_created ;

		}

		static function create_new_user_economy ($source, $db_conf){
			$id_user = $source['userId'] ;
			$query = "INSERT INTO `USER_ECONOMY` (`ID_USER`, `LAST_DAILY`, `POINTS`) VALUES ('" .
						$id_user . "','" .
						date('Y-m-d H:i:s') . "','" .
						1000 . "')";  
			mysqli_query($db_conf, $query);
		}

		static function get_supply_points ($source, $db_conf){

			$id_user = $source['userId'] ;
			$query = "SELECT * FROM `USER_ECONOMY` WHERE ID_USER = '" . $id_user . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			$current_supply = (int)$query_fetch['SUPPLY_POINTS']; 
			return $current_supply ;

		}

		static function modify_supply_points ($source, $db_conf, $value, $type){
			$query = "SELECT * FROM `USER_ECONOMY` WHERE ID_USER = '" . $source['userId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			$current_supply = (int)$query_fetch['SUPPLY_POINTS'] ;
			$new_value = 0 ;
			switch ($type) {
				case 0:
					$new_value = $current_supply - $value ;
					break;
				
				case 1:
					$new_value = $current_supply + $value ;
					break;
			}
			
			$query = sprintf("UPDATE `USER_ECONOMY` SET `SUPPLY_POINTS`= %d WHERE ID_USER = '%s'",
				$new_value, $source['userId']);
			mysqli_query($db_conf, $query);
			// return $query ;

		}

		static function get_plant_counts ($source, $db_conf){

			$id_group = $source['groupId'] ;
			$query = "SELECT * FROM `AREA_LIST` WHERE ID_GROUP = '" . $id_group . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			return (int)$query_fetch['PLANTS_COUNT']; 
			
		}

		static function create_area ($source, $db_conf){

			if (isset($source['groupId'])) {				
				$id_group = $source['groupId'] ;
				$query = "SELECT * FROM `AREA_LIST` WHERE ID_GROUP = '" . $id_group . "'";
				$query_result = mysqli_query($db_conf, $query);
				$is_created = mysqli_num_rows($query_result);
				if ($is_created == 0) {
					$query = sprintf("INSERT INTO `AREA_LIST` (`ID_GROUP`, `PLANTS_COUNT`) VALUES ('%s', 0)", 
						$id_group);
					mysqli_query($db_conf, $query);
					return "Garden created" ;
				} else {
					return "There's already a garden here" ;
				}
			} else {
				return "Can't create garden here (only in group room)" ;
			}


		}

		static function modify_plants ($source, $db_conf, $value, $type){
			$id_group = $source['groupId'] ;
			$query = "SELECT * FROM `AREA_LIST` WHERE ID_GROUP = '" . $id_group . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			$current_value = $query_fetch['PLANTS_COUNT'];
			$new_value = 0 ;
			$response = "" ;

			switch ($type) {
				case 0:
					$new_value = $current_value - $value ;
					break;
				
				case 1:
					$new_value = $current_value + $value ;
					break;
			}
			
			$query = sprintf("UPDATE `AREA_LIST` SET `PLANTS_COUNT` = %d WHERE ID_GROUP = '%s'",
				$new_value, $source['groupId']);
			mysqli_query($db_conf, $query);

		}

		static function get_waifu_status ($name_to_search, $db_conf) {
			$name_to_search = str_ireplace('\'', '', $name_to_search) ;
			$query = sprintf("SELECT * FROM `WAIFU_LIST` WHERE `CARD_NAME` = '%s'", 
				$name_to_search);
			$query_result = mysqli_query($db_conf, $query);
			$is_claimed = mysqli_num_rows($query_result); 
			
			return $is_claimed ;
		}

		static function get_last_claimer ($name_to_search, $db_conf) {
			$name_to_search = str_ireplace('\'', '', $name_to_search) ;
			$query = sprintf("SELECT * FROM `WAIFU_LIST` WHERE `CARD_NAME` = '%s'", 
				$name_to_search);
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);
			$current_claimer = $query_fetch['CURRENT_CLAIMER'];

			return $current_claimer ;
		}

		static function register_claim ($source, $db_conf, $card_name){
			$card_name = str_ireplace('\'', '', $card_name) ;
			$id_user = $source['userId'] ;
			$query = sprintf("INSERT INTO `WAIFU_LIST` 
				(`CARD_NAME`, `CURRENT_CLAIMER`) 
				VALUES ('%s', '%s')",
				$card_name, $id_user);

			mysqli_query($db_conf, $query);
		}

		static function check_claim_status ($name_to_search, $db_conf){

			$name_to_search = str_ireplace('\'', '', $name_to_search) ;
			$query = sprintf("SELECT * FROM `WAIFU_LIST` WHERE `CARD_NAME` = '%s'",
				$name_to_search);
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);

			$current_time = date('Y-m-d H:i:s');
			$last_gifted = $query_fetch['LAST_GIFTED'];

			$difference = compare_datetime($current_time, $last_gifted);

			if ($difference['days'] >= 1) {
				return 1 ;
			} else {
				return 0 ;
			}

		}

		static function get_claimer_id ($name_to_search, $db_conf){

			$name_to_search = str_ireplace('\'', '', $name_to_search) ;
			$query = sprintf("SELECT * FROM `WAIFU_LIST` WHERE `CARD_NAME` = '%s'",
				$name_to_search);
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);

			return $query_fetch['CURRENT_CLAIMER'];
		}

		static function get_old_claimer_id ($name_to_search, $db_conf){

			$name_to_search = str_ireplace('\'', '', $name_to_search) ;
			$query = sprintf("SELECT * FROM `WAIFU_LIST` WHERE `CARD_NAME` = '%s'",
				$name_to_search);
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);

			return $query_fetch['OLD_CLAIMER'];
		}

		static function update_claim ($source, $db_conf, $card_name, $database){
			$id_user = $source['userId'] ;
			$old_claimer = $database->get_last_claimer($card_name, $db_conf);
			$current_time = date('Y-m-d H:i:s');

			$query = sprintf("UPDATE `WAIFU_LIST` SET
				`CURRENT_CLAIMER` = '%s' , 
				`OLD_CLAIMER` = '%s' , 
				`LAST_CLAIM` = '%s', 
				`LAST_GIFTED` = '%s'
				WHERE `CARD_NAME` = '%s'",
				$id_user, $old_claimer, $current_time, $current_time, $card_name);

			mysqli_query($db_conf, $query);
		}

		static function update_gift ($db_conf, $card_name){

			$current_time = date('Y-m-d H:i:s');

			$query = sprintf("UPDATE `WAIFU_LIST` SET
				`LAST_GIFTED` = '%s'
				WHERE `CARD_NAME` = '%s'",
				$current_time, $card_name);

			mysqli_query($db_conf, $query);
		}

		static function update_gift_all ($source, $db_conf){

			$current_time = date('Y-m-d H:i:s');

			$query = sprintf("UPDATE `WAIFU_LIST` SET
				`LAST_GIFTED` = '%s'
				WHERE `CURRENT_CLAIMER` = '%s'",
				$current_time, $source['userId']);

			mysqli_query($db_conf, $query);
		}

		static function get_claim ($source, $db_conf){

			$query = sprintf("SELECT * FROM `WAIFU_LIST` WHERE `CURRENT_CLAIMER` = '%s'",
				$source['userId']);
			$query_result = mysqli_query($db_conf, $query);
			$has_claim = mysqli_num_rows($query_result); 

			if ($has_claim > 0) {
				$counter = 0 ;
				$result = "== YOUR WAIFU LIST ==\n\n";
				
				while ($current_row = mysqli_fetch_array($query_result)){

					$name = $current_row['CARD_NAME'];
					$result .= sprintf("%d. %s\n", ++$counter, $name);
				}

				return $result ;
			} else {
				return "You don't have any claim yet, so lonely ....";
			}
		}

		static function delete_claim ($source, $db_conf, $card_name, $database){
			$card_name = str_ireplace('\'', '', $card_name) ;
			$id_user = $source['userId'] ;
			$old_claimer = $database->get_last_claimer($card_name, $db_conf);

			if ($id_user == $old_claimer) {				
				$query = sprintf("DELETE FROM `WAIFU_LIST` 
					WHERE `CARD_NAME` = '%s'",
					$card_name);
				mysqli_query($db_conf, $query);
				return "You broke up with " . $card_name ;
			} else {
				return $card_name . " doesn't even like you" ;
			}

		}

		static function get_area_mod ($source, $db_conf){

			$id_group = $source['groupId'];
			$query = sprintf("SELECT * FROM `AREA_LIST` WHERE `ID_GROUP` = '%s'",
				$id_group);
			$query_result = mysqli_query($db_conf, $query);

			if (mysqli_num_rows($query_result) == 1 ) {
				$query_fetch = mysqli_fetch_array($query_result);
				return $query_fetch;
			} else {
				$default_rate = array(
					'MOD_N' => 90, 
					'MOD_R' => 10,
					'MOD_SR' => 0,
					'MOD_SSR' => 0,
					'MOD_LEGEND' => 0
				); 
				return $default_rate;
			}

		}

		static function create_coupon ($coupon_code, $coupon_value, $db_conf){
			$query = sprintf("INSERT INTO `COUPON_LIST` 
				(`CODE`, `VALUE`) 
				VALUES ('%s', '%d')",
				$coupon_code, $coupon_value);

			mysqli_query($db_conf, $query);
		}

		static function redeem_coupon ($event, $coupon_code, $db_conf, $database){

			$query = sprintf("SELECT * FROM `COUPON_LIST` WHERE `CODE` = '%s'",
					$coupon_code);
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result);

			$database->modify_points($event['source'], $db_conf, $query_fetch['VALUE'], 1);

			$query = sprintf("DELETE FROM `COUPON_LIST` WHERE `CODE` = '%s'",
				$coupon_code);
			mysqli_query($db_conf, $query);
			return $query_fetch['VALUE'];

		}

		static function modify_rate ($db_conf, $groupId, $type){

			$query = "";
			if ($type == 1) {
				$query = sprintf("UPDATE `AREA_LIST` SET
					`MOD_N` = '20', 
					`MOD_R` = '50', 
					`MOD_SR` = '25' 
					WHERE `AREA_LIST`.`ID_GROUP` = '%s' ",
					$groupId);
			} elseif($type == 0) {
				$query = sprintf("UPDATE `AREA_LIST` SET
					`MOD_N` = '50', 
					`MOD_R` = '30', 
					`MOD_SR` = '15' 
					WHERE `AREA_LIST`.`ID_GROUP` = '%s' ",
					$groupId);
			}

			mysqli_query($db_conf, $query);
		}

		static function get_casino_info ($source, $db_conf){

			$query = "SELECT * FROM `BUILDING_LIST` WHERE ID_GROUP = '" . $source['groupId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result); 
			return $query_fetch ;

		}

		static function update_casino ($source, $db_conf){

			$query = "SELECT * FROM `BUILDING_LIST` WHERE ID_GROUP = '" . $source['groupId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result); 

			$current_datetime = date('Y-m-d H:i:s');
			$last_daily_time = $query_fetch['LAST_REFRESH_CASINO'] ;

			$date1 = new DateTime($current_datetime);
			$date2 = new DateTime($last_daily_time);
			$interval = $date1->diff($date2);
			$hours_difference = $interval->h ;
			$days_difference = $interval->d ;

			if ($days_difference >= 1) {
				$new_points = ($query_fetch['LV_CASINO'] * 25000) + 100000 ;
		    	$query = sprintf("UPDATE `BUILDING_LIST` 
					SET CURRENT_VALUE_CASINO = '%d', LAST_REFRESH_CASINO = '%s'
					WHERE ID_GROUP = '%s'",
					$new_points, $current_datetime , $source['groupId']);

				mysqli_query($db_conf, $query);
			} 

		}

		static function timer_casino ($source, $db_conf){

			$query = "SELECT * FROM `BUILDING_LIST` WHERE ID_GROUP = '" . $source['groupId'] . "'";
			$query_result = mysqli_query($db_conf, $query);
			$query_fetch = mysqli_fetch_array($query_result); 

			$current_datetime = date('Y-m-d H:i:s');
			$last_daily_time = $query_fetch['LAST_REFRESH_CASINO'] ;

			$date1 = new DateTime($current_datetime);
			$date2 = new DateTime($last_daily_time);
			$interval = $date1->diff($date2);
			$hours_difference = $interval->h ;
			$reset_in = 24 - $hours_difference ;

			return "Casino will reset in " . $reset_in . " hours"; 

		}

		static function modify_casino_points ($source, $used_points, $current_casino, $type, $db_conf){

			switch ($type) {
				case 1:
					$new_points = $current_casino['CURRENT_VALUE_CASINO'] - $used_points ;
					break;
				
				case 2:
					$new_points = $current_casino['CURRENT_VALUE_CASINO'] + $used_points ;
					// $value_max = ($current_casino['LV_CASINO'] * 25000) + 100000 ;
					// if ($value_max < $new_points) {
					// 	$new_points = $value_max;
					// }					
					break;
			}

			$query = sprintf("UPDATE `BUILDING_LIST` 
				SET CURRENT_VALUE_CASINO = '%d'
				WHERE ID_GROUP = '%s'",
				$new_points, $source['groupId']);

			mysqli_query($db_conf, $query);
		}

		static function upgrade_casino ($source, $points_used, $current_casino, $db_conf){

			$level_up_needed = $current_casino['LV_CASINO'] * 1000000 ;
			$total_exp = $points_used + $current_casino['EXP_CASINO'];

			if ($total_exp >= $level_up_needed) {
				$new_level = $current_casino['LV_CASINO'] + 1;
				$new_wealth = ($new_level * 25000) + 100000 ;
				$query = sprintf("UPDATE `BUILDING_LIST` 
					SET LV_CASINO = '%d', CURRENT_VALUE_CASINO = '%d'
					WHERE ID_GROUP = '%s'",
					$new_level, $new_wealth, $source['groupId']);

				mysqli_query($db_conf, $query);
				return "<Casino Upgraded To Level " . $new_level . " >\n\n- Daily wealth increases by 25k\n-Total Wealth is now " . $new_wealth . "\n- Current cash refreshed";

			} else {
				$old_exp = $current_casino['EXP_CASINO'];
				$new_exp = $old_exp + $points_used;
				$query = sprintf("UPDATE `BUILDING_LIST` 
					SET EXP_CASINO = '%d'
					WHERE ID_GROUP = '%s'",
					$new_exp, $source['groupId']);

				mysqli_query($db_conf, $query);
				$text_response = sprintf("Casino EXP Increases by %d\nNeed %d more to level up",
					$points_used, ($level_up_needed - $new_exp) );

				return $text_response;
			}

		}

	}
?>