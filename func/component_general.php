<?php 
	// Error Handling 
	function exceptions_error_handler($severity, $message, $filename, $lineno) {
	  if (error_reporting() == 0) {
	    return;
	  }
	  if (error_reporting() & $severity) {
	    throw new ErrorException($message, 0, $severity, $filename, $lineno);
	  }
	}

	// Replacing <br> into \n
	function replace_br ($words){
	    $replaced_string = str_ireplace("<br>", "\n", $words);
	    return $replaced_string ;
	}
	
	// Resizing image to 184x240 for LINE thumbnail using free resizing API from Google
	function resize_image ($image_url) {
		$image_resized = "https://images1-focus-opensocial.googleusercontent.com/gadgets/proxy?url=$image_url&container=focus&resize_h=240&refresh=2592000";
		$image_array = array ($image_url, $image_resized) ;
		return $image_array ;
	} 
	
	// Created to help bypass HTTPS requirement from LINE API
	function https_image_container ($image_url) { 
		$image = "https://images1-focus-opensocial.googleusercontent.com/gadgets/proxy?url=$image_url&container=focus&refresh=2592000";
		return $image ;
	} 

	// For the new database format
	function log_wrapper ($event, $command, $criteria, $db, $search_result, $database){
		if ($search_result['found'] == 1) {
			$database->create_log_data_specific($event['source'], $command, $criteria, $db, $search_result['name'], $search_result['expansion']);
		} else {
			$database->create_log_data($event['source'], $command, $criteria, $db);
		}
	}

	function hunt_games ($source, $database, $db){

		if ($database->check_economy_availabilty($source, $db) == 0) {
			$database->create_new_user_economy($source, $db) ;
		}

		$current_datetime = date('Y-m-d H:i:s');
		$last_hunt_time = $database->get_last_hunt($source, $db);
		$difference = compare_datetime($current_datetime, $last_hunt_time);

		if ($difference['minutes'] >= 5) {
			$result = rand(1,100);
			if ($result > 0 && $result <= 60) { 
				// Normal Item : 0 - 50 point (60% chances)
				$rarity_code = 1 ;
			} elseif ($result > 60 && $result <= 85) { 
				// Rare : 100 - 500 point (25% chances)
				$rarity_code = 2 ;
			} elseif ($result > 85 && $result <= 95) { 
				// Super Rare : 1000 - 2000 point (10% chances)
				$rarity_code = 3 ;
			} elseif ($result > 95 && $result <= 99) {
				// Super Super Rare : 5000 point (4% chances)
				$rarity_code = 4 ;
			} elseif ($result == 100) { 
				// Legendary : 10000 point (1% chances)
				$rarity_code = 5 ;
			}
			
			$item_list = $database->get_item($source, $db, $rarity_code);
			$item_pick = rand(0,count($item_list)-1);
			
			$database->update_last_hunt($source, $db);
			$database->modify_points($source, $db, $item_list[$item_pick]["ITEM_VALUES"], 1);
			$database->modify_supply_points($event['source'], $db, 1, 0);

			switch ($item_list[$item_pick]["RARITY"]) {
				case 1:
					$rarity = "(Normal Item)" ;
					break;
				
				case 2:
					$rarity = "(Rare Item)" ;
					break;

				case 3:
					$rarity = "(Super Rare Item)" ;
					break;

				case 4:
					$rarity = "(Super Super Rare Item)" ;
					break;

				case 5:
					$rarity = "(Legendary Item)" ;
					break;
			}

			$hunt_response = sprintf("We found %s !\n%s\n\n%s\n\n-- Sold and got %s points --",
				$item_list[$item_pick]["NAME"], $rarity, $item_list[$item_pick]["ITEM_DESC"], $item_list[$item_pick]["ITEM_VALUES"]
			); 
			
			return $hunt_response ;
		} else {
			$seconds_left = 300 - $difference['seconds'];
			return "Can hunt again in " . $seconds_left . " seconds" ;
		}


	}

	function compare_datetime($first_date, $second_date){

		$date1 = new DateTime($first_date);
		$date2 = new DateTime($second_date);
		$interval = $date1->diff($date2);
		$difference['seconds'] = $interval->s ;
		$difference['minutes'] = $interval->i ;
		$difference['hours'] = $interval->h ;
		$difference['days'] = $interval->d ;
		return $difference ;

	}

	function get_top_points ($client, $source, $db_conf){

		$query = "SELECT `ID_USER`, `POINTS` FROM `USER_ECONOMY` ORDER BY `POINTS` DESC LIMIT 5";
		$query_result = mysqli_query($db_conf, $query);

		$result = "TOP 5 USER\n\n" ;
		$counter = 0 ;

		while ($current_row = mysqli_fetch_array($query_result)){
			$user_info = $client->getProfile($current_row['ID_USER']);
			$user_info_decoded = json_decode($user_info, true);
			
			$name = $user_info_decoded['displayName'];
			$points = $current_row['POINTS'];
			$result .= sprintf("%d. %s - %s points\n", ++$counter, $name, $points);
		}

		return $result ;

	}

?>