<?php

	require_once( __DIR__ . '/src/LINEBotTiny.php');

	require_once( __DIR__ . '/conf/channel_key.php');
	require_once( __DIR__ . '/conf/db_connection.php');
	require_once( __DIR__ . '/conf/bot_setup.php');	

	require_once( __DIR__ . '/func/func_main.php');
	require_once( __DIR__ . '/func/func_display.php');
	require_once( __DIR__ . '/func/func_db.php');

	set_error_handler('exceptions_error_handler');
	
	$client = new LINEBotTiny($channelAccessToken, $channelSecret);
	$display = new display();
	$database = new database();

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

							$gobu_logic = new bot_logic ($client, $event, $display);

							switch ($exploded_Message[0]) {
								
								// Return Text Based Only To User

									// Disabled
								case '..find':
									$display->single_text_response($client, $event, "Currently in maintenance, use ..name instead\n\nExample :\n..name Spawn");
									break;

									// using New Search
										// Connecting to Bagoum
								
								case '..flair':
									$search_result = search_card_v2 (trim($join_input));
									$gobu_logic->logic_controller_for_bagoum($search_result, $exploded_Message[0]);
									break;

								case '..name':
									$search_result = search_card_v2 (trim($join_input));
									$gobu_logic->logic_controller_for_bagoum($search_result, $exploded_Message[0]);
									break;

										// Connecting to Database
								
								case '..ani':
									$search_result = search_card_v2 (trim($join_input));
									$gobu_logic->logic_controller_for_database($search_result, $exploded_Message[0], $database, $db);
									break;

								case '..anievo':
									$search_result = search_card_v2 (trim($join_input));
									$gobu_logic->logic_controller_for_database($search_result, $exploded_Message[0], $database, $db);
									break;

								// Return Either Text or Image

									// Using Old Search
								case '..img':
									$response = search_card (trim($join_input), $exploded_Message[0]);
									if (count($response) == 2) {
										$display->single_image_response($client, $event, $response);
									} else {
										$display->single_text_response($client, $event, $response);
									}
									break;

								case '..imgevo':
									$response = search_card (trim($join_input), $exploded_Message[0]);
									if (count($response) == 2) {
										$display->single_image_response($client, $event, $response);
									} else {
										$display->single_text_response($client, $event, $response);
									}
									break;

									// Using New Search
								case '..alt':
									$search_result = search_card_v2 (trim($join_input));
									if ($search_result['found'] > 1 && $search_result['found'] < 6) {
										$display->confirm_response($client, $event, $search_result, "..alt");
									} 
									if ($search_result['found'] == 1) {
										$response = get_specific_card_info_v2($search_result['name'], "..alt");
										if (count($response) == 2) {
											$display->single_image_response($client, $event, $response);
										} else {
											$display->single_text_response($client, $event, $response);
										}										
									}
									if ($search_result['found'] == 0) {
										$display->single_text_response($client, $event, "No card found with that criteria");
									}
									if ($search_result['found'] > 5 && $search_result['found'] <= 10) {
										$display->single_text_response($client, $event, "Found " . $search_result['found'] . " card with that criteria.\n\n" . $search_result['name']);
									} else {
										$display->single_text_response($client, $event, "Found " . $search_result['found'] . " card with that criteria. That's too many~");
									}
									break;

								case '..altevo':
									$search_result = search_card_v2 (trim($join_input));
									if ($search_result['found'] > 1 && $search_result['found'] < 6) {
										$display->confirm_response($client, $event, $search_result, "..altevo");
									} 
									if ($search_result['found'] == 1) {
										$response = get_specific_card_info_v2($search_result['name'], "..altevo");
										if (count($response) == 2) {
											$display->single_image_response($client, $event, $response);
										} else {
											$display->single_text_response($client, $event, $response);
										}	
									}
									if ($search_result['found'] == 0) {
										$display->single_text_response($client, $event, "No card found with that criteria");
									}
									if ($search_result['found'] > 5 && $search_result['found'] <= 10) {
										$display->single_text_response($client, $event, "Found " . $search_result['found'] . " card with that criteria.\n\n" . $search_result['name']);
									} else {
										$display->single_text_response($client, $event, "Found " . $search_result['found'] . " card with that criteria. That's too many~");
									}
									break;

								// Admin Function
								case '..set':
									if ($event['source']['userId'] == 'Uc7871461db4f5476b1d83f71ee559bf0') {
										switch ($exploded_Message[1]) {
											case 'funclog':
												$function_log = $exploded_Message[2];
												break;

											case 'unilog':
												$universal_log = $exploded_Message[2];
												break;
										}
										$result = $database->update_log_setting (trim($function_log), trim($universal_log));
										$display->single_text_response($client, $event, $result);
									} else {
										$display->single_text_response($client, $event, "Sorry, you don't have permission to do that~");
									}
									break;

								case '..status':
									$display->single_text_response($client, $event, "Gobu Status\n\nFunction Log : " . $function_log . "Universal Log : " . $universal_log);
									break;

								// Debug
								case '..bagoum':
									$search_result = search_card_v2 (trim($join_input));
									$gobu_logic->logic_controller_for_bagoum($search_result, "..flair");
									break;

								case '..database':
									$search_result = search_card_v2 (trim($join_input));
									$gobu_logic->logic_controller_for_database($search_result, "..ani", $database, $db);
									break;

							}

							///////////////////
							// Log Function //
							/////////////////
							if ($function_log == 1) {
								$database->create_function_log_data($event['source'], $message['text'], $db);
							}

							if ($universal_log == 1) {
								$database->create_universal_log_data($event['source'], $message['text'], $db);		
							}
							
							// Closing Database Connection
							if (is_resource($db) && get_resource_type($db) === 'mysql link') {
								mysqli_close($db);
							}

						} catch (Exception $e) {
	                		$response = "Error Occured\n\n- Details -" . PHP_EOL . "File Location : " . $e->getFile() . PHP_EOL . "Line Number : " . $e->getLine() . PHP_EOL . "Type : " . $e->getMessage();
	                		$display->single_text_response($client, $event, $response);	
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