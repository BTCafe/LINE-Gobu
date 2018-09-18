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
		$result = rand(1,100);
		if ($result > 0 && $result <= 60) { // 60%
			// Normal / Worthless Item : 0 - 50 point
			$item_list = $database->get_item($source, $db, 1);
			$item_pick = rand(0,count($item_list)-1);
		} elseif ($result > 60 && $result <= 85) { // 25%
			// Rare : 100 - 500 point
			$item_list = $database->get_item($source, $db, 2);
			$item_pick = rand(0,count($item_list)-1);
		} elseif ($result > 85 && $result <= 95) { // 10%
			// Super Rare : 1000 - 2000 point
			$item_list = $database->get_item($source, $db, 3);
			$item_pick = rand(0,count($item_list)-1);
		} elseif ($result > 95 && $result <= 99) { // 4%
			// Super Super Rare : 5000 point
			$item_list = $database->get_item($source, $db, 4);
			$item_pick = rand(0,count($item_list)-1);
		} elseif ($result == 100) { // 1%
			// Legendary : 10000 point
			$item_list = $database->get_item($source, $db, 5);
			$item_pick = rand(0,count($item_list)-1);
		}
		return $item_list[$item_pick]["NAME"] ;

	}

?>