<?php
	require_once( __DIR__ . '/src/LINEBotTiny.php');
	require_once( __DIR__ . '/src/twitter-api-php-master/TwitterAPIExchange.php');

	require_once( __DIR__ . '/conf/channel_key.php');
	require_once( __DIR__ . '/conf/db_connection.php');
	require_once( __DIR__ . '/conf/bot_setup.php');	
	require_once( __DIR__ . '/conf/twitter_setup.php');	

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

	               		$command = $exploded_Message[0];

	               		$counter = 1 ;
	               		$criteria = "";
	               		while ($counter < count($exploded_Message)) {
	               			$criteria .= $exploded_Message[$counter] . " ";
	               			$counter ++ ;
	               		}
						
						$gobu_logic = new bot_logic ($client, $event, $display);

						// DO SPECIAL EVENT !
						$gobu_logic->do_special_event($command, $database, $db, $display, $event, $client);
						try {

							/////////////////////////	
							// Social Media Router //
							////////////////////////

							if (filter_var($message['text'], FILTER_VALIDATE_URL)) {
								$host = parse_url($message['text'], PHP_URL_HOST) ;
								if ($host == "mobile.twitter.com") {
									$host = "twitter.com" ;
								}
								switch ($host) {
									case 'twitter.com':
										$splitted_url = explode("/" , parse_url($message['text'], PHP_URL_PATH));
										$id_to_search = $splitted_url[3];

										$url = 'https://api.twitter.com/1.1/statuses/show.json'; // API to use
										$getfield = '?id=' . $id_to_search; // Query
										$requestMethod = 'GET';

										$twitter = new TwitterAPIExchange($twitter_settings);
										$json = $twitter->setGetfield($getfield)
										    ->buildOauth($url, $requestMethod)
										    ->performRequest();

										$data = json_decode($json);

										if (isset($data->extended_entities)) {
											if (isset($data->extended_entities->media)) {
												$tes = $data->extended_entities->media ;
												foreach ($tes as $images) {
													$media_stack[] = $images->media_url_https;
												}
												carousel_response_for_twitter_with_picture($client, $event, $data, $media_stack);
											}
										} else {
											$message = twitter_text_builder($data);
											$display->single_text_response($client, $event, $message);
										} 
										
										break;
								}
							}

							/////////////////////////	
							// Shadowverse Router //
							////////////////////////

							switch ($command) {
								case 'happyxthought':
									if (isset($event['source']['groupId']) || isset($event['source']['roomId'])) {
										$text_response = "This is not the place to talk about that ... \nNeed to talk about that in private" ;
									} else {
										$text_response = "Give me my master id !" ;
										file_put_contents('./func/temp/' . $event['source']['userId'] . '.txt', 'test' . PHP_EOL , LOCK_EX);
									}
									
									$display->single_text_response($client, $event, $text_response);
									break;
								
								// Return Text Based Only To User //
								case '..find':
									if (trim($criteria) === "waifu") {
										$search_result = search_card_v2 (trim("Silva")); 
										$gobu_logic->logic_controller_for_bagoum($search_result, '..raw', "image");
										if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									} else {
										$search_result = find_card (explode(" ", trim($criteria))); // Explode the criteria to make it into array
										$gobu_logic->logic_controller_for_bagoum($search_result, '..name', "text");
										if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									}
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

								case '..alt2':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..altevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..altevo2':
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

								case '..rawalt2':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawaltevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawaltevo2':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								// Return Sound and Text or Only Text //
								case '..voice':
									$gobu_logic->logic_controller_for_bagoum($exploded_Message, $command, "sound");
									if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..mywaifu':
									$gobu_logic->logic_controller_for_bagoum_game('waifu_game');
									break;

								case '..daily':
									$gobu_logic->logic_controller_for_general($command, $database, $db, $event['source']);
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
								case 'test':
									$gobu_logic->logic_controller_for_bagoum_game('guess_flair');
									break;

								case '..debug':
									$display->single_sticker_response($client, $event);
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