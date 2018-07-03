<?php 
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
		$card_id = $card_data['id'];
		$image_url = "https://shadowverse-portal.com/image/card/en/C_$card_id.png" ;
		$result = resize_image($image_url) ;
		return $result ;
	}

	function get_alt_image ($card_data){
		if ($card_data["hasAlt"] == true) {
			$card_id = $card_data['altid'];
			$image_url = "https://shadowverse-portal.com/image/card/en/C_$card_id.png" ;
			$result = resize_image($image_url) ;
		} else {
			$result = "No alternate image found" ;
		}
		return $result ;
	}

	function get_alt_image_2 ($card_data){
		if ($card_data["hasAlt2"] == true) {
			$card_id = $card_data['altid2'];
			$image_url = "https://shadowverse-portal.com/image/card/en/C_$card_id.png" ;
			$result = resize_image($image_url) ;
		} else {
			$result = "No alternate image found" ;
		}
		return $result ;
	}

	function get_evolved_image ($card_data){
		if ($card_data['hasEvo']) {
			$card_id = $card_data['id'];
			$image_url = "https://shadowverse-portal.com/image/card/en/E_$card_id.png" ;
			$result = resize_image($image_url) ;
		} else {
			$result = "No evolve image found / available" ;
		}
		return $result ;
	}

	function get_alt_evolved_image ($card_data){
		if ($card_data["hasAlt"] == true) {
			$card_id = $card_data['altid'];
			$image_url = "https://shadowverse-portal.com/image/card/en/E_$card_id.png" ;
			$result = resize_image($image_url) ;
		} else {
			$result = "No alternate evolve image found" ;
		}
		return $result ;
	}

	function get_alt_evolved_image_2 ($card_data){
		if ($card_data["hasAlt2"] == true) {
			$card_id = $card_data['altid2'];
			$image_url = "https://shadowverse-portal.com/image/card/en/E_$card_id.png" ;
			$result = resize_image($image_url) ;
		} else {
			$result = "No alternate evolve image found" ;
		}
		return $result ;
	}

	function get_id ($card_data){
		$card_id = $card_data['id'] ;
		return $card_id ;
	}

	function get_raw_image ($card_data, $evo, $alt){
		$to_replace = array(',', ' ', '\'', '.', '-');
		$card_name = str_ireplace($to_replace, '', $card_data['_name']) ;
		$image_url = "http://sv.bagoum.com/getRawImage/$evo/$alt/$card_name" ;
		$temp_file = file_get_contents($image_url);
		if ($temp_file) {
			$file_size = strlen($temp_file);
			if ($file_size > 10000) {
				$result = resize_image($image_url) ;
				$result[0] = https_image_container($image_url) ;
			} else {
				$result = "No raw image found / available yet" ;	
			} 	
		}
		else {
			$result = "No raw image found / available yet" ;
		}
		return $result ;
	}

	function get_expansion ($card_data) {
		$expansion_origin = $card_data["expansion"];
		$result = trim($expansion_origin);
		return $result ;
	}

	// This is the main hub for connecting all the other function here, kinda like a controller
	function get_specific_card_info_v2 ($card_name, $parameter){
		$card_array_list = fetch_all_card();
		// Selecting the specific array that contains the required data 
		$specific_card_info = $card_array_list[trim($card_name)];

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

			case '..alt':
				$result = get_alt_image($specific_card_info);				
				break;

			case '..alt2':
				$result = get_alt_image_2($specific_card_info);				
				break;

			case '..altevo':
				$result = get_alt_evolved_image($specific_card_info);				
				break;

			case '..altevo2':
				$result = get_alt_evolved_image_2($specific_card_info);				
				break;

			case '..name':
				$result = get_stats_based_on_name($specific_card_info);
				break;

			case 'id':
				$result = get_id($specific_card_info);
				break;

			case '..raw':
				$result = get_raw_image($specific_card_info, 0, 0);
				break;

			case '..rawevo':
				$result = get_raw_image($specific_card_info, 1, 0);
				break;

			case '..rawalt':
				$result = get_raw_image($specific_card_info, 0, 1);
				break;

			case '..rawaltevo':
				$result = get_raw_image($specific_card_info, 1, 1);
				break;

			case '..rawalt2':
				$result = get_raw_image($specific_card_info, 0, 2);
				break;

			case '..rawaltevo2':
				$result = get_raw_image($specific_card_info, 1, 2);
				break;

			case 'expansion':
				$result = get_expansion($specific_card_info);
				break;

		}

		return $result ; 
	}

	/**
	* Look for a card that has specific name
	*/
	function search_card_v2 ($criteria){
		// Get all the card
		$card_list = fetch_all_card();
		// Create a new array from $card_list using their keys (the card name) for easier search
		$card_name = array_keys($card_list);

		$counter = 0 ;
		$found = 0 ;
		$name_stack = "" ;
		// Looping to find any card that match $criteria on their name
		while (count($card_name) > $counter) {
			$compare = stripos($card_name[$counter], $criteria) ; 
			if ($compare !== false) {
				$found++ ;
				$name_stack = $name_stack . $card_name[$counter] . "\n" ;
				$found_counter = $counter ;
				// In case an exact name found, immediately pick that one by resetting the name stack and found number
				if (strlen($criteria) == strlen($card_name[$counter])) {
					$found = 1 ;
					$name_stack = $card_name[$counter];
					$counter = count($card_name);
				}
			}
			$counter++ ;
		}

		$search_result = array('found'=>$found, 'name'=>$name_stack);
		return $search_result;
	}

	/**
	* Look for a card that has specific criteria associated with it
	*/
	function find_card ($search_array){
		$card_list = fetch_all_card();
		$card_list = array_values($card_list);

		$number_of_word_to_search = count($search_array); // How many criteria inputted
		$terminate_early_status = false ; // For faster comparing when any criteria doesn't meet
		$word_search_counter = 0 ; // Counter for switching criteria
		$found_word_status = 0 ; // Indicator to monitor how many criteria meet
		$match_counter = 0 ; // Indicator on how many card found
		$card_counter = 0 ; // Counter for switching card to analyze
		$name_stack = "" ; // Holds all card name that match criteria

		// Begin search until all cards has been compared
		while ($card_counter < count($card_list)) {
			// Begin comparassion on individual card with the inputted criteria. Ends prematurely when any criteria doesn't meet
			while ($word_search_counter < $number_of_word_to_search && $terminate_early_status == false) {
				$compare = stripos(replace_br($card_list[$card_counter]["searchableText"]), $search_array[$word_search_counter]);
				if ($compare !== false) {
					$word_search_counter++ ;
					$found_word_status++ ;
				} else {
					$terminate_early_status = true ;
				}

				// Only happen when all criteria meet on the card. Add it to the stack
				if ($found_word_status == $number_of_word_to_search) {
					$match_counter++ ;
					$name_stack = $name_stack . $card_list[$card_counter]['name'] . "\n" ;
				}

			}

			// Reset all condition before doing another search
			$terminate_early_status = false ;
			$found_word_status = $word_search_counter = 0 ;
			// Increase counter to switch to other card
			$card_counter++ ;

		}
		// Return comparassion result with their name and the number of card that match  
		$find_result = array('found'=>$match_counter, 'name'=>$name_stack);
		return $find_result ;

	}

	function get_random_card (){
		$card_list = array_values( fetch_all_card() );
		$random_counter = rand(0, count($card_list) - 1 );
		$answer = array() ;
		$answer['name'] = $card_list[$random_counter]["name"] ;

		if ($card_list[$random_counter]["hasEvo"]) {
			$use_evo_flair = rand(0, 1);
			switch ($use_evo_flair) {
				case 0:		
					$answer['flair'] = $card_list[$random_counter]["baseData"]["flair"] ;			
					break;
				
				case 1:
					$answer['flair'] = $card_list[$random_counter]["evoData"]["flair"] ;
					break;
			}
		} else {
			$answer['flair'] = $card_list[$random_counter]["baseData"]["flair"] ;	
		}

		$taken_counter = array() ;
		$taken_counter[0] = $random_counter ;

		$filler = array() ;

		for ($i=0; $i < 3; $i++) { 			
			$check_counter = 0 ;
			$random_counter = rand(0, count($card_list) - 1 );
			do {
				if ($taken_counter[$check_counter] == $random_counter) {
					$check_counter = 0 ;
					$random_counter = rand(0, count($card_list) - 1 );	
				} else {
					$check_counter ++ ;
				}
			} while ($check_counter < count($taken_counter) );

			$filler[$i] = $card_list[$random_counter]["name"];
		}

		$result = array(
			'name' => $answer["name"], 
			'flair' => $answer["flair"],
			'filler1' => $filler[0],
			'filler2' => $filler[1],
			'filler3' => $filler[2],
		);

		return $result;
	}

	function get_one_random_follower_name (){
		$card_list = array_values( fetch_all_card() );

		do {
			$random_counter = rand(0, count($card_list) - 1 );
		} while ($card_list[$random_counter]["type"] !== "Follower") ;

		$result = $card_list[$random_counter]["name"];

		return $result;
	}

?>