<?php

	require_once( __DIR__ . '/src/LINEBotTiny.php');

	require_once( __DIR__ . '/conf/channel_key.php');
	require_once( __DIR__ . '/conf/db_connection.php');

	require_once( __DIR__ . '/func/func_main.php');
	require_once( __DIR__ . '/func/func_display.php');

	set_error_handler('exceptions_error_handler');
	
	$client = new LINEBotTiny($channelAccessToken, $channelSecret);

	foreach ($client->parseEvents() as $event) {

	    switch ($event['type']) {

	    	// Standard Message Event 
	        case 'message':
	            $message = $event['message'];

	            switch ($message['type']) {
	                case 'text':

	                	// Explode The Message So We Can Get The First Words
	               		$exploded_Message = explode(" ", trim($message['text']));

	               		$counter = 1 ;
	               		$join_input = "";
	               		while ($counter < count($exploded_Message)) {
	               			$join_input .= $exploded_Message[$counter] . " ";
	               			$counter ++ ;
	               		}
						
						try {

							/////////////////////////////////////////	
							// Works On Personal and Group Account//
							///////////////////////////////////////

							switch ($exploded_Message[0]) {
								
								case '..flair':
									$response = search_card (trim($join_input), $exploded_Message[0]);
									single_text_response($client, $event, $response);
									break;

								case '..find':
									$response = search_card (trim($join_input), $exploded_Message[0]);
									single_text_response($client, $event, $response);
									break;

								case '..img':
									$response = search_card (trim($join_input), $exploded_Message[0]);
									if (count($response) == 2) {
										single_image_response($client, $event, $response);
									} else {
										single_text_response($client, $event, $response);
									}
									break;

								case '..imgevo':
									$response = search_card (trim($join_input), $exploded_Message[0]);
									if (count($response) == 2) {
										single_image_response($client, $event, $response);
									} else {
										single_text_response($client, $event, $response);
									}
									break;

								// Experimental
								case '..finds':
									$search_array = explode (" ",trim($join_input)) ;
									$response = find_card ($search_array);
									single_text_response($client, $event, $response);
									break;

								// For Debug
								case '..name':
									$search_result = search_card_v2 (trim($join_input), "..find");
									if ($search_result['found'] > 1 && $search_result['found'] < 6) {
										confirm_response($client, $event, $search_result, "..find");
									} 
									if ($search_result['found'] == 1) {
										single_text_response($client, $event, get_specific_card_info_v2($search_result['name'], "..find"));
									}
									if ($search_result['found'] == 0) {
										single_text_response($client, $event, "No card found with that criteria");
									}
									if ($search_result['found'] > 5 && $search_result['found'] <= 10) {
										single_text_response($client, $event, "Found " . $search_result['found'] . " card with that criteria.\n\n" . $search_result['name']);
									} else {
										single_text_response($client, $event, "Found " . $search_result['found'] . " card with that criteria. That's too many~");
									}
									break;

							}

							///////////////////
							// Log Function //
							/////////////////
							
							// create_log_data($event['source'], $message['text'], $db);		
							
							// Closing Database Connection
							if (is_resource($db) && get_resource_type($db) === 'mysql link') {
								mysqli_close($db);
							}

						} catch (Exception $e) {
	                		$response = "Sorry, An Error Just Occured" . PHP_EOL . $e->getMessage();
	                		single_text_response($client, $event, $response);	
						}
	                    break;
	           
	                default:
	                    error_log("Unsupporeted message type: " . $message['type']);
	                    break;
	            }
	            break;
	
	        default:
	            error_log("Unsupporeted event type: " . $event['type']);
	            break;
	    }
	};
	
?>