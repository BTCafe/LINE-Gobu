<?php
	
	// Error Handling Mainly In Case No Term Found
	function exceptions_error_handler($severity, $message, $filename, $lineno) {
	  if (error_reporting() == 0) {
	    return;
	  }
	  if (error_reporting() & $severity) {
	    throw new ErrorException($message, 0, $severity, $filename, $lineno);
	  }
	}

	// Replacing <br> into \n
	function replacer ($words){
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

	function get_specific_card_info ($card_array_name, $card_array_list, $parameter){
		$specific_card_info = $card_array_list[$card_array_name];

		switch ($parameter) {
			case '..find':
				$name = $specific_card_info["name"];
				$faction = $specific_card_info["faction"];
				if ($specific_card_info["race"] != "") {
					$faction .= " (" . $specific_card_info["race"] . ")" ;
				}
				$rarity = $specific_card_info["rarity"];
				$expansion_origin = $specific_card_info["expansion"];
				$base_stats = "Base : " . $specific_card_info["manaCost"] . " PP " . $specific_card_info["baseData"]["attack"] . "/" . $specific_card_info["baseData"]["defense"] ;
				$base_description = replacer($specific_card_info["baseData"]["description"]) ;
				$evo_stats = "Evolved : " . $specific_card_info["manaCost"] . " PP " . $specific_card_info["evoData"]["attack"] . "/" . $specific_card_info["evoData"]["defense"] ;
				$evo_description = replacer($specific_card_info["evoData"]["description"]) ;

				$result = $name . "\n" . $faction . "\n" . $expansion_origin . " -- " . $rarity . "\n\n" . $base_stats . "\n" . $base_description . "\n\n" . $evo_stats . "\n" . $evo_description ;
				break;
			
			case '..flair':
				$name = $specific_card_info["name"];
				$base_flair = $specific_card_info["baseData"]["flair"];
				$result = $name . "\n\n" . $base_flair . "\n\n" ;
				if ($specific_card_info["evoData"]["flair"] != "") {
					$result .= $specific_card_info["evoData"]["flair"] . "\n" ;
				}
				break;

			case '..img':
				$image_url = $specific_card_info["baseData"]["img"] ;
				$image_resized = "https://images1-focus-opensocial.googleusercontent.com/gadgets/proxy?url=$image_url&container=focus&resize_w=184&resize_h=240&refresh=2592000";
				$result = array ($image_url, $image_resized) ;
				break;

			case '..imgevo':
				$image_url = $specific_card_info["evoData"]["img"] ;
				$image_resized = "https://images1-focus-opensocial.googleusercontent.com/gadgets/proxy?url=$image_url&container=focus&resize_w=184&resize_h=240&refresh=2592000";
				$result = array ($image_url, $image_resized) ;
				break;
		}

		return $result ; 
	}

	function get_similar_card ($desc, $parameter){
		$card_list = fetch_all_card();
		$card_name = array_keys($card_list);

		$counter = 0 ;
		$found = 0 ;
		$name_stack = "" ;
		while (count($card_name) > $counter) {
			$compare = stripos($card_name[$counter], $desc) ; 
			if ($compare !== false) {
				$found++ ;
				$name_stack = $name_stack . $card_name[$counter] . " , " ;
				$found_counter = $counter ;
			}
			$counter++ ;
		}

		if ($found > 10) {
			$result = "Found " . $found .  " cards with " . $desc . " in it. That's too many~";	
		} elseif ($found > 1 && $found <= 9) {
			$result = "Found " . $found . " cards with " . $desc . " in it.\n\n" . $name_stack;
			$result = rtrim($result, " , ");
		} elseif ($found == 1) {
			$result = get_specific_card_info($card_name[$found_counter], $card_list, $parameter);
		} elseif ($found == 0) {
			$result = "No card found with that description" ;
		} 

		return $result ;
	}

	function create_log_data ($source, $command) {
		if (!isset($source['userId'])) {
			$choosenID = 'groupId' ;
			if (!isset($source['groupId'])) {
				$choosenID = 'roomId' ;
			} 
		} else {
			$choosenID = 'userId' ;
		}

		$log = 	
			date('Y-m-d h:i:s e') . PHP_EOL . 	                    		
    		"User ID: " . $source[$choosenID] . PHP_EOL . 
    		"Command: " . $command . PHP_EOL . 
    		"-----------------------------" . PHP_EOL; 

    	file_put_contents('./logs/' . date('Y-m-d') . '.txt', $log, FILE_APPEND | LOCK_EX);
	}
?>