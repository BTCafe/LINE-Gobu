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

	function get_alt_image ($card_data){
		if ($card_data["hasAlt"] == true) {
			$image_url = $card_data["baseData"]["altimg"] ;
			$result = resize_image($image_url) ;
		} else {
			$result = "No alternate image found" ;
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

	function get_alt_evolved_image ($card_data){
		if ($card_data["hasAlt"] == true) {
			$image_url = $card_data["evoData"]["altimg"] ;
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

	// Resizing image to 184x240 for LINE thumbnail using free resizing API from Google
	function resize_image ($image_url) {
		$image_resized = "https://images1-focus-opensocial.googleusercontent.com/gadgets/proxy?url=$image_url&container=focus&resize_w=184&resize_h=240&refresh=2592000";
		$image_array = array ($image_url, $image_resized) ;
		return $image_array ;
	} 

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

			case '..altevo':
				$result = get_alt_evolved_image($specific_card_info);				
				break;

			case '..name':
				$result = get_stats_based_on_name($specific_card_info);
				break;

			case 'id':
				$result = get_id($specific_card_info);
				break;

		}

		return $result ; 
	}

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

	class bot_logic 
	{
		protected $client ;
		protected $event ;
		protected $display ;
		
		function __construct($client_data, $event_data, $display)
		{
			$this->client = $client_data ;
			$this->event = $event_data ;
			$this->display = $display ;
		}

		function basic_logic ($search_result, $inputted_command)
		{
			if ($search_result['found'] > 1 && $search_result['found'] < 6) {
				$this->display->confirm_response($this->client, $this->event, $search_result, $inputted_command);
			} 
			if ($search_result['found'] == 0) {
				$this->display->show_no_result($this->client, $this->event);
			}
			if ($search_result['found'] > 5 && $search_result['found'] <= 10) {
				$this->display->show_result_more_than_5 ($this->client, $this->event, $search_result['found'], $search_result['name']);
			} 
			if ($search_result['found'] > 10) {
				$this->display->show_too_many_result ($this->client, $this->event, $search_result['found']);
			}
		}

		function logic_controller_for_bagoum ($search_result, $inputted_command, $preferred_return_type)
		{
			if ($inputted_command == "..voice") {
				$input_correct = 0 ;
				$lang = $search_result[1] ;
				$voice_type = $search_result[2] ;

				if ($lang == "eng" || $lang == "jpn" || $lang == "kor") {
					$input_correct++ ;
				}

				if ($voice_type == "atk" || $voice_type == "play" || $voice_type == "evo" || $voice_type == "die") {
					$input_correct++ ;	
				}

				switch ($input_correct) {
					case 0:
						$this->display->show_input_error($this->client, $this->event);
						break;
					
					case 1:
						$this->display->show_input_error($this->client, $this->event);
						break;

					case 2:
						$new_criteria = "" ;
						$index = 3 ;
						while ($index < count($search_result)) {
							$new_criteria .= " " . $search_result[$index] ;
							$index++ ;
						}
						unset($search_result);
						$search_result = search_card_v2 (trim($new_criteria));
						break;
				}
			}

			if ($search_result['found'] == 1) {
				switch ($preferred_return_type) {
					case 'text':
						$this->display->single_text_response($this->client, $this->event, get_specific_card_info_v2($search_result['name'], $inputted_command));
						break;
					
					case 'image':
						$image_result_status = get_specific_card_info_v2($search_result['name'], $inputted_command);
						if (count($image_result_status) == 2) {
							$this->display->single_image_response($this->client, $this->event, $image_result_status);
						} else {
							$this->display->single_text_response($this->client, $this->event, $image_result_status);
						}
						break;

					case 'sound':
						$card_id = get_specific_card_info_v2 ($search_result['name'], 'id');
						switch ($lang) {
							case 'eng':
								$lang_par = 'e' ; 
								break;
							case 'jpn':
								$lang_par = 'j' ;
								break;
							case 'kor':
								$lang_par = 'k' ;
								break;
						}
						switch ($voice_type) {
							case 'play':
								$sound_url = "http://sv.bagoum.com/voice/" . $lang_par . "/vo_" . $card_id . "_1.mp3" ;
								$voice_description = "Play voice for " . $search_result['name'] ;
								break;
							case 'atk':
								$sound_url = "http://sv.bagoum.com/voice/" . $lang_par . "/vo_" . $card_id . "_2.mp3" ;
								$voice_description = "Attack voice for " . $search_result['name'] ;
								break;
							case 'evo':
								$sound_url = "http://sv.bagoum.com/voice/" . $lang_par . "/vo_" . $card_id . "_3.mp3";
								$voice_description = "Evolve voice for " . $search_result['name'] ;
								break;
							case 'die':
								$sound_url = "http://sv.bagoum.com/voice/" . $lang_par . "/vo_" . $card_id . "_4.mp3";
								$voice_description = "Death voice for " . $search_result['name'] ;
								break;	
						}
						$this->display->single_text_response($this->client, $this->event, $voice_description . "\n\n" . $sound_url);
						break;

				}
			} else {
				$this->basic_logic($search_result, $inputted_command);
			}
		}

		function logic_controller_for_database ($search_result, $inputted_command, $database, $db)
		{
			if ($search_result['found'] == 1) {
				if ($inputted_command == "..ani") {
					$is_evo = 0 ;
					$image_type = "..img" ;
				}
				if ($inputted_command == "..anievo") {
					$is_evo = 1 ;
					$image_type = "..imgevo" ;
				}
				$original_url = $database->get_animated_url($search_result['name'], $is_evo, $db);
				$converted_url = str_ireplace('http:', 'https:', $original_url);
				$converted_url = str_ireplace('.gifv', '.mp4', $converted_url);

				$image_result_status = get_specific_card_info_v2($search_result['name'], $image_type);
			
				$formatted_video_response = array($converted_url, $image_result_status[1]);
				$this->display->single_video_response($this->client, $this->event, $formatted_video_response);
			} else {
				$this->basic_logic($search_result, $inputted_command);
			}
		}
	}
?>