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

	// Fetching Card Data From sv.bagoum.com
	function fetch_all_card (){
		$bagoum_url = 'http://sv.bagoum.com/cardsFullJSON' ;
		$card_data = file_get_contents($bagoum_url);
		$card_array = json_decode($card_data, true);
		return $card_array ; 
	}

	function get_stats_based_on_name ($card_data){
		$name = $card_data["name"];
		$type = $card_data["type"];
		$rarity = $card_data["rarity"];
		$expansion_origin = $card_data["expansion"];
		$faction = $card_data["faction"];
		if ($card_data["race"] != "") {
			$faction .= " (" . $card_data["race"] . ")" ;
		}

		if ($type == "Follower") {
			$base_stats = "Base : " . $card_data["manaCost"] . "PP " . $card_data["baseData"]["attack"] . "/" . $card_data["baseData"]["defense"] ;
			$base_description = replace_br($card_data["baseData"]["description"]) ;
			$evo_stats = "Evolved : " . $card_data["manaCost"] . " PP " . $card_data["evoData"]["attack"] . "/" . $card_data["evoData"]["defense"] ;
			$evo_description = replace_br($card_data["evoData"]["description"]) ;
			$result = $name . "\n>" . $rarity . " " . $faction . " " . $type . "\n>" . $expansion_origin . "\n\n" . $base_stats . "\n" . $base_description . "\n\n" . $evo_stats . "\n" . $evo_description ;
		} else {
			$base_stats = "Base : " . $card_data["manaCost"] . "PP";
			$base_description = replace_br($card_data["baseData"]["description"]) ;
			$result = $name . "\n>" . $rarity . " " . $faction . " " . $type . "\n>" . $expansion_origin . "\n\n" . $base_stats . "\n" . $base_description ;	
		}
		$result = trim($result);
		return $result ;
	}

	function get_flair ($card_data) {
		$name = $card_data["name"];
		$base_flair = $card_data["baseData"]["flair"];
		$result = $name . "\n\n" . $base_flair . "\n\n" ;
		if ($card_data["evoData"]["flair"] != "") {
			$result .= $card_data["evoData"]["flair"] . "\n" ;
		}
		$result = trim($result);
		return $result ;
	}

	function get_image ($card_data){
		$image_url = $card_data["baseData"]["img"] ;
		if ($image_url != "") {
			$result = resize_image($image_url) ;
		} else {
			$result = "No image found / available" ;
		}
		return $result ;
	}

	function get_evolved_image ($card_data){
		$image_url = $card_data["evoData"]["img"] ;
		if ($image_url != "") {
			$result = resize_image($image_url) ;
		} else {
			$result = "No image found / available" ;
		}
		return $result ;
	}

	// Resizing image to 184x240 for LINE thumbnail using free resizing API from Google
	function resize_image ($image_url) {
		$image_resized = "https://images1-focus-opensocial.googleusercontent.com/gadgets/proxy?url=$image_url&container=focus&resize_w=184&resize_h=240&refresh=2592000";
		$image_array = array ($image_url, $image_resized) ;
		return $image_array ;
	} 

	// The Controller In Deciding Response
	function get_specific_card_info ($card_array_name, $card_array_list, $parameter){
		// Selecting the specific array that contains the required data 
		$specific_card_info = $card_array_list[$card_array_name];

		switch ($parameter) {
			case '..find':
				$result = get_stats_based_on_name($specific_card_info);
				break;
			
			case '..flair':
				$result = get_flair($specific_card_info);
				break;

			case '..img':
				$result = get_image($specific_card_info);
				break;

			case '..imgevo':
				$result = get_evolved_image($specific_card_info);
				break;
		}

		return $result ; 
	}

	// Searching the word, return the result to the caller
	function search_card ($desc, $parameter){
		$card_list = fetch_all_card();
		// Create a new array from $card_list using their keys (the card name) for easier search
		$card_name = array_keys($card_list);

		$counter = 0 ;
		$found = 0 ;
		$name_stack = "" ;
		// Looping to find any card that match $desc on their name
		while (count($card_name) > $counter) {
			$compare = stripos($card_name[$counter], $desc) ; 
			if ($compare !== false) {
				$found++ ;
				$name_stack = $name_stack . $card_name[$counter] . ",\n" ;
				$found_counter = $counter ;
			}
			$counter++ ;
		}

		// Finding too many result, doesn't return their name to avoid spamming the room
		if ($found > 8) {
			$result = "Found " . $found .  " cards with " . $desc . " in it. That's too many~";	
		} 
		// Finding 2 to 8 similar card, shows the name of each card
		elseif ($found > 1 && $found <= 8) {
			$result = "Found " . $found . " cards with " . $desc . " in it.\n\n" . $name_stack;
			// Used to get rid of the last , on $name_stack 
			$result = rtrim($result, " , ");
		} 
		// Finding exactly 1 similar card, return the data about that card based on the parameter
		elseif ($found == 1) {
			$result = get_specific_card_info($card_name[$found_counter], $card_list, $parameter);
		} elseif ($found == 0) {
			$result = "No card found with that description" ;
		} 
		return $result ;
	}

	// Find the word that match anything from a card, return result to the caller - UNTESTED
	// function find_card ($search_array){
	// 	$card_list = fetch_all_card();

	// 	$number_of_word_to_search = count($search_array);
	// 	$terminate_early_status = false ;
	// 	$word_search_counter = 0 ;
	// 	$found_word_status = 0 ;

	// 	$found_array_number = 0 ;
	// 	$match_counter = 0 ;
	// 	$card_counter = 0 ;
	// 	$name_stack = "" ;

	// 	while ($card_counter < count($card_list)) {
	// 		while ($word_search_counter < $number_of_word_to_search && $terminate_early_status == false) {
	// 			$compare = stripos($card_list[$card_counter]["searchableText"], $search_array[$word_search_counter]);
	// 			if ($compare !== false) {
	// 				$word_search_counter++ ;
	// 				$found_word_status++ ;
	// 			} else {
	// 				$terminate_early_status = true ;
	// 			}

	// 			if ($found_word_status == $number_of_word_to_search) {
	// 				$match_counter++ ;
	// 				$found_array_number = $card_counter ;
	// 				$name_stack = $found . ". " . $name_stack . $card_list[$card_counter] . "\n" ;
	// 			}
	// 		}
	// 		$terminate_early_status = false ;
	// 		$found_word_status = $word_search_counter = 0 ;
	// 		$card_counter++ ;
	// 	}

	// 	// Finding too many result, doesn't return their name to avoid spamming the room
	// 	if ($found > 8) {
	// 		$result = "Found " . $found .  " cards with " . $desc . " in it. That's too many~";	
	// 	} 
	// 	// Finding 2 to 8 similar card, shows the name of each card
	// 	elseif ($found > 1 && $found <= 8) {
	// 		$result = "Found " . $found . " cards with " . $desc . " in it.\n\n" . $name_stack;
	// 		// Used to get rid of the last , on $name_stack 
	// 		$result = rtrim($result, " , ");
	// 	} 
	// 	// Finding exactly 1 similar card, return the data about that card based on the parameter
	// 	elseif ($found == 1) {
	// 		$result = get_stats_based_on_name($card_list[$found_array_number]);
	// 	} elseif ($found == 0) {
	// 		$result = "No card found with that description" ;
	// 	}
	// 	return $result ; 
	// }

	// Log function to store any transaction data to the database
	function create_log_data ($source, $command, $db_conf) {
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
?>