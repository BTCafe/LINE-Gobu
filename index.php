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

	                	// Separating Word To Get The Command (First Words)
	               		$exploded_Message = explode(" ", trim($message['text']));
	               		$command = $exploded_Message[0];

	               		// Remake the exploded word so it will contain criteria only
	               		$counter = 1 ;
	               		$criteria = "";
	               		while ($counter < count($exploded_Message)) {
	               			$criteria .= $exploded_Message[$counter] . " ";
	               			$counter ++ ;
	               		}
						
						$gobu_logic = new bot_logic ($client, $event, $display);

						// EXPERIMENT ROOM // 
						if ($event['source']['userId'] == 'Uc7871461db4f5476b1d83f71ee559bf0') {
							try {
								// PATIENTS GOES HERE
								if ($command == "test") {
									$gobu_logic->logic_controller_for_bagoum_game();
								}
							} catch (Exception $e) {
								// EVALUATION REPORT
								$response = "Error Occured\n\n- Details -" . PHP_EOL . "File Location : " . $e->getFile() . PHP_EOL . "Line Number : " . $e->getLine() . PHP_EOL . "Type : " . $e->getMessage();
	                		$display->single_text_response($client, $event, $response);	
							}
						}

						// DO SPECIAL EVENT !
						$gobu_logic->do_special_event($command, $database, $db);
						
						try {

							/////////////////////////	
							// Shadowverse Router //
							////////////////////////

							switch ($command) {
								case 'happyxthought':
									if (isset($event['source']['groupId']) || isset($event['source']['roomId'])) {
										$text_response = "This is not the place to talk about that ..." ;
									} else {
										$text_response = "Give me my master id !" ;
										file_put_contents('./func/temp/' . $event['source']['userId'] . '.txt', 'test' . PHP_EOL , LOCK_EX);
									}									
									$display->single_text_response($client, $event, $text_response);
									break;
								
								// Return Text Based Only To User //
								case '..find':
									$search_result = find_card (explode(" ", trim($criteria))); // Explode the criteria to make it into array
									$gobu_logic->logic_controller_for_bagoum($search_result, '..name', "text");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..flair':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "text");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..name':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "text");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

									// Connecting to Database
								case '..ani':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_database($search_result, $command, $database, $db);
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..anievo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_database($search_result, $command, $database, $db);
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								// Return Either Text or Image //
								case '..img':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..imgevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..alt':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..altevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..raw':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawalt':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawaltevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								// Return Sound and Text or Only Text //
								case '..voice':
									$gobu_logic->logic_controller_for_bagoum($exploded_Message, $command, "sound");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;
							}

							//////////////////////////////	
							// Urban Dictionary Router //
							/////////////////////////////

							switch ($command) {
								// Urban Dictionary Function
								case '..ud':
									$gobu_logic->logic_controller_for_urbandictionary($command, $criteria);
									if ($function_log == 1) {
										$database->create_log_data_ud($event['source'], $command, $criteria, $db);
									}
									break;

								case '..explain':
									$gobu_logic->logic_controller_for_urbandictionary($command, $criteria);
									if ($function_log == 1) {
										$database->create_log_data_ud($event['source'], $command, $criteria, $db);
									}
									break;

								case '..random':
									$gobu_logic->logic_controller_for_urbandictionary($command, "");
									if ($function_log == 1) {
										$database->create_log_data_ud($event['source'], $command, "Random Stuff", $db);
									}
									break;
							}

							///////////////////////	
							// Utilities Router //
							//////////////////////

							switch ($command) {
								// Utility Function
								case '..help':
									$gobu_logic->logic_controller_for_info($command);
									break;

								case '..contact':
									$gobu_logic->logic_controller_for_info($command);
									break;

								case '..about':
									$gobu_logic->logic_controller_for_info($command);
									break;

								// Admin Function //
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
										$display->show_no_permission($client, $event);
									}
									break;

								case '..status':
									$display->single_text_response($client, $event, "Gobu Status\n\nFunction Log : " . $function_log);
									break;

								// Debug
								case '..debug':
									file_put_contents('./temp/' . $event['source']['userId'] . '.txt', 'test' . PHP_EOL , LOCK_EX);
									break;

								case '..database':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_database($search_result, "..debugdb", $database, $db);
									break;

							}

							///////////////////
							// Log Function //
							/////////////////
							
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