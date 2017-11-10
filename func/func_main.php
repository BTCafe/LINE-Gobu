<?php	
	require_once( __DIR__ . '/component_general.php');
	require_once( __DIR__ . '/component_bagoum.php');
	require_once( __DIR__ . '/component_urbandictionary.php');

	/**
	* The main class that form the BOT logic
	*/
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

		function multiple_result_handler ($search_result, $inputted_command)
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
			// Formating voice command to be correct 
			if ($inputted_command == "..voice") {
				$input_correct = 0 ;
				
				if (count($search_result) > 3) {
					$lang = $search_result[1] ;
					$voice_type = $search_result[2] ;

					if ($lang == "eng" || $lang == "jpn" || $lang == "kor") {
						$input_correct++ ;
					}

					if ($voice_type == "atk" || $voice_type == "play" || $voice_type == "evo" || $voice_type == "die") {
						$input_correct++ ;	
					}
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
						// Rearrange criteria to only holds name for search purpose
						while (count($search_result) > $index) {
							$new_criteria .= $search_result[$index] . " " ;
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
						// If an image found the result will be 2 because it will return an array by resize function
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
						$this->display->single_text_response($this->client, $this->event, $voice_description . "\n" . $sound_url);
						break;

				}
			} else {
				if ($inputted_command == "..voice") {
					$this->display->single_text_response($this->client, $this->event, "Multiple result found and this function doesn't support carousel yet. Please input the full name : " . "\n\n" . $search_result['name']);
				} else {
					$this->multiple_result_handler($search_result, $inputted_command);
				}
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
				if (strpos($original_url, 'gifv') !== false) {
					$converted_url = str_ireplace('http:', 'https:', $original_url);
					$converted_url = str_ireplace('.gifv', '.mp4', $converted_url);

					$image_result_status = get_specific_card_info_v2($search_result['name'], $image_type);
				
					$formatted_video_response = array($converted_url, $image_result_status[1]);
					$this->display->single_video_response($this->client, $this->event, $formatted_video_response);
				} elseif (strpos($original_url, 'mp4') !== false) {
					$image_result_status = get_specific_card_info_v2($search_result['name'], $image_type);

					$formatted_video_response = array($original_url, $image_result_status[1]);					
					$this->display->single_video_response($this->client, $this->event, $formatted_video_response);
				} else {
					$this->display->single_text_response($this->client, $this->event, $original_url);
				}
			} else {
				$this->multiple_result_handler($search_result, $inputted_command);
			}
		}

		function logic_controller_for_info ($inputted_command)
		{
			switch ($inputted_command) {
				case '..help':
					$message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/texts/cmds_response.txt');
					break;
				case '..contact':
					$this->display->show_contact_menu($this->client, $this->event);
					break;
				case '..about':
					$message = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/texts/about_response.txt');
					break;
			}
			if (isset($message)) {
				$this->display->single_text_response($this->client, $this->event, $message);
			}
		}

		function logic_controller_for_urbandictionary ($inputted_command, $criteria)
		{
			switch ($inputted_command) {
				case '..ud':
					$message = exact_term($criteria);
					break;

				case '..explain':
					$message = exact_term($criteria);					
					break;

				case '..random':
					$message = random_term();
					break;
			}
			if (isset($message)) {
				$this->display->single_text_response($this->client, $this->event, $message);
			}
		}

		// You know, getting my mind examined by you is really scary ...
		function do_special_event ($command, $database, $db){
			// Special Function for Just Aggro Event - will be deleted on 1st Dec
			if (file_exists('./func/temp/' . $this->event['source']['userId'] . '.txt')) {
				unlink('./func/temp/' . $this->event['source']['userId'] . '.txt');
				if ('minerva28' == strtolower($command)) {
					$result = $this->client->getProfile($this->event['source']['userId']);
					$result = json_decode($result, true);
					$user_display_name = $result['displayName'] ;

					$eligible = $database->check_arg_participation($this->event['source'], $db);
					if ($eligible) {
						$current_participant = $database->get_number_of_participant($db) ; 
						$text_response = 
						"You're late " . $user_display_name . ", there's already " . $current_participant . " other people here !" . PHP_EOL . PHP_EOL . 
						"Thank you for coming in though ^^" . PHP_EOL . PHP_EOL . 
						"- Yours Truly, Happy Happy BTC <3" ;

						// Here's your prize
						$this->display->congrats($this->client, $this->event, $text_response);
						
						$database->create_log_data_for_arg($this->event['source'], $current_participant + 1, $db);
					} else {
						$text_response = 
						"I'm sorry " . $user_display_name . ", but you already participated in this game ^^" . PHP_EOL . PHP_EOL . 
						"Please contact me on Twitter, YouTube, or email if you're interested in another one ~" . PHP_EOL . PHP_EOL . 
						"- Regards, BTC" ;
						$this->display->single_text_response($this->client, $this->event, $text_response);
					}
				} else {
					$text_response = "That's not my master ID !\nInput my thought again to progress ~" ;
					$this->display->single_text_response($this->client, $this->event, $text_response);
				}
			}
		}
	}
?>