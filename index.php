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
						// $gobu_logic->do_special_event($command, $database, $db, $display, $event, $client);
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
										// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									} else {
										$search_result = find_card (explode(" ", trim($criteria))); // Explode the criteria to make it into array
										$gobu_logic->logic_controller_for_bagoum($search_result, '..name', "text");
										// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									}
									break;

								case '..flair':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "text");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..name':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "text");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

									// Connecting to Database
								// case '..ani':
								// 	$search_result = search_card_v2 (trim($criteria));
								// 	$gobu_logic->logic_controller_for_database($search_result, $command, $database, $db);
								// 	// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
								// 	break;

								// case '..anievo':
								// 	$search_result = search_card_v2 (trim($criteria));
								// 	$gobu_logic->logic_controller_for_database($search_result, $command, $database, $db);
								// 	// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
								// 	break;

								// Return Either Text or Image //
								case '..img':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..imgevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..alt':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..alt2':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..altevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..altevo2':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..raw':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawalt':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawalt2':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawaltevo':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								case '..rawaltevo2':
									$search_result = search_card_v2 (trim($criteria));
									$gobu_logic->logic_controller_for_bagoum($search_result, $command, "image");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;

								// Return Sound and Text or Only Text //
								case '..voice':
									$gobu_logic->logic_controller_for_bagoum($exploded_Message, $command, "sound");
									// if ($function_log == 1){ log_wrapper($event, $command, $criteria, $db, $search_result, $database); }
									break;
									
							}

							////////////////////////	
							// Mini Games Router //
							///////////////////////

							switch ($command) {

								case '..mywaifu':
									$gobu_logic->logic_controller_for_bagoum_game('waifu_game');
									break;

								case '..daily':
									$gobu_logic->logic_controller_for_general($command, $database, $db, $event['source']);
									break;

								case '..points':
									$gobu_logic->logic_controller_for_general($command, $database, $db, $event['source']);
									break;

								case '..slots':
									$database->update_casino($event['source'], $db);
									$current_casino = $database->get_casino_info($event['source'], $db);
									$used_points = (int)$exploded_Message[1];
									if ($used_points < $current_casino['CURRENT_VALUE_CASINO']) {
										$result = rand(1,2);

										$database->modify_casino_points($event['source'], $used_points, $current_casino, $result, $db);

										$gobu_logic->logic_controller_for_general_2($command, $database, $db, $event['source'], $used_points, $result);
									} else {
										$text_response = "Casino doesn't have enough points\nYou can upgrade Casino for more daily points";
										$display->single_text_response($client, $event, $text_response);
									} 
									break;

								case '..casino':
									$current_casino = $database->get_casino_info($event['source'], $db);
									$max_cash = ($current_casino['LV_CASINO'] * 25000) + 100000 ;
									$text_response = sprintf("<CASINO STATUS>\n(%d / %d)\nLevel : %d\nCash left : %d\nMax Cash : %d", $current_casino['EXP_CASINO'], ($current_casino['LV_CASINO'] * 1000000), $current_casino['LV_CASINO'], $current_casino['CURRENT_VALUE_CASINO'], $max_cash);
									$display->single_text_response($client, $event, $text_response);
									break;

								case '..up':
									$current_casino = $database->get_casino_info($event['source'], $db);
									$used_points = (int)$exploded_Message[1];
									if ($used_points > 0) {
										$current_points = $database->get_points($event['source'], $db);
										if ($current_points < $used_points) {
											$text_response = "You don't have enough points to upgrade";
										} else {
											$database->modify_points($event['source'], $db, $used_points, 0);
											$text_response = $database->upgrade_casino($event['source'], $used_points, $current_casino, $db);	
										}

									} else {
										$text_response = "You must use positive number";
									}
									$display->single_text_response($client, $event, $text_response);
									break;

								case '..hunt':

									$current_supply = $database->get_supply_points($event['source'], $db);

									if ($current_supply == 0) {
										$text_response = "We don't have any supplies. Let's buy some from the shops!\nExample : ..resupply 10";
										$display->single_text_response($client, $event, $text_response);
									} else {
										$text_response = hunt_games($event['source'], $database, $db);
										$display->single_text_response($client, $event, $text_response);
									}

									break;

								case '..rank':
									$text_response = get_top_points($client, $event['source'], $db);
									$display->single_text_response($client, $event, $text_response);
									break;

								case '..supply':
									$current_supply = $database->get_supply_points($event['source'], $db);
									$text_response = sprintf("You currently have %d supplies", 
										$current_supply);
									$display->single_text_response($client, $event, $text_response);
									break;

								case '..resupply':
									if (isset($exploded_Message[1])) {
										$value = (int)$exploded_Message[1] ;
										$base_supply_cost = 25 ;

										$total_cost = $value * $base_supply_cost ;
										$current_points = $database->get_points($event['source'], $db);

										if ($current_points <= $total_cost) {
											$difference = $total_cost - $current_points ;
											$text_response = sprintf("You don't have enough points to buy that many supplies\n(Need %d more)", $difference) ;
											$display->single_text_response($client, $event, $text_response);
										} else {
											if ($value < 0) {
												$text_response = sprintf("You must use positive number");
											} else {
												$database->modify_points($event['source'], $db, $total_cost, 0);
												$database->modify_supply_points($event['source'], $db, $value, 1);

												$current_supply = $database->get_supply_points($event['source'], $db);
												$text_response = sprintf("Bought %d supplies\n(Current Supply : %d)", $value, $current_supply);
												
											}
											$display->single_text_response($client, $event, $text_response);
										}

									} 
									break;

								case '..plant':
									if (isset($exploded_Message[1])) {
										$total_cost = (int)$exploded_Message[1] ;

										if ($total_cost < 0) {
											$text_response = sprintf("You can't plant that many flower") ;
											$display->single_text_response($client, $event, $text_response);
										} else {
											$current_points = $database->get_points($event['source'], $db);
											if ($current_points <= $total_cost) {
												$difference = $total_cost - $current_points ;
												$text_response = sprintf("You don't have enough points to plant that many flower\n(Need %d more)", $difference) ;
												$display->single_text_response($client, $event, $text_response);
											} else {
												$database->modify_points($event['source'], $db, $total_cost, 0);
												$database->modify_plants($event['source'], $db, $total_cost, 1);
												$current_flower = $database->get_plant_counts($event['source'], $db);
												$text_response = sprintf("Planted %d flower\n(Current Flower : %d)",
													$total_cost, $current_flower);
												$display->single_text_response($client, $event, $text_response);
											}
										}

									} else {
										$text_response = sprintf("There's %d flower at the moments", 
											$database->get_plant_counts($event['source'], $db));
										$display->single_text_response($client, $event, $text_response);
									} 
									break;

								case '..garden':
									$result = $database->create_area($event['source'], $db);
									$display->single_text_response($client, $event, $result);
									break;

								case '..sell':
									$current_flower = $database->get_plant_counts($event['source'], $db);
									$database->modify_points($event['source'], $db, $current_flower, 1);
									$database->modify_plants($event['source'], $db, $current_flower, 0);

									$text_response = sprintf("Sold all flower for %d points", 
										$current_flower);

									$display->single_text_response($client, $event, $text_response);
									break;

								case '..who':
									$search_result = search_card_v2 (trim($criteria));
									if ($search_result["found"] > 1 || $search_result["found"] == 0) {
										$gobu_logic->logic_controller_for_bagoum($search_result, $command, "text");
									} else {
										$name = trim($search_result['name']);
										$is_claimed = $database->get_waifu_status($name, $db);
										switch ($is_claimed) {
											case 0:
												$text_response = sprintf("Nobody claimed %s yet...", $name);
												break;											
											case 1:
												$claimer_id = $database->get_claimer_id($name, $db);
												$result = $client->getProfile($claimer_id);
												$result = json_decode($result, true);
												$current_claimer = $result['displayName'] ;
												$text_response = sprintf("[%s] is already claimed by [%s] !", $name, $current_claimer);
												break;
										}
										$display->single_text_response($client, $event, $text_response);
									}
									break;

								case '..claims':
									$current_points = $database->get_points($event['source'], $db);
									$search_result = search_card_v2 (trim($criteria));

									if ($search_result["found"] > 1 || $search_result["found"] == 0) {
										$gobu_logic->logic_controller_for_bagoum($search_result, $command, "text");
									} else {

										$name = trim($search_result['name']);
										$is_claimed = $database->get_waifu_status($name, $db);
										if ($is_claimed == 0 && $current_points >= 10000) {
											$database->register_claim($event['source'], $db, $name);
											$database->modify_points($event['source'], $db, 10000, 0);
											
											$text_response = sprintf("You have claimed %s !\nDon't forget to treat them ~", $name);
											$image_data = get_specific_card_info_v2($name, '..imgevo');

											$display->double_text_and_image_response($client, $event, $text_response, $image_data);
										} else {
											$can_claim = $database->check_claim_status($name, $db);

											if ($can_claim == 1 && $current_points >= 10000) {
												$database->update_claim($event['source'], $db, $name, $database);
												$database->modify_points($event['source'], $db, 10000, 0);

												$claimer_id = $database->get_claimer_id($name, $db);
												$result = $client->getProfile($claimer_id);
												$result = json_decode($result, true);
												$current_claimer = $result['displayName'] ;

												$old_claimer_id = $database->get_old_claimer_id($name, $db);
												$result = $client->getProfile($old_claimer_id);
												$result = json_decode($result, true);
												$old_claimer = $result['displayName'] ;

												$text_response = sprintf("%s is now yours !\nNice steal there ~\n[New Claimer : %s]\n[Old Claimer : %s]", $name, $current_claimer, $old_claimer);
												$display->single_text_response($client, $event, $text_response);
											} elseif ($current_points < 10000) {
												$text_response = sprintf("You don't even have enough points to claim anybody (10k needed)");
												$display->single_text_response($client, $event, $text_response);
											} elseif ($can_claim == 0) {
												
												$claimer_id = $database->get_claimer_id($name, $db);
												$result = $client->getProfile($claimer_id);
												$result = json_decode($result, true);
												$user_display_name = $result['displayName'] ;

												$text_response = sprintf("Somebody already claimed %s\nThey treated them well so you can't claim them yet\n(Current Claimer : %s)", 
													$name, $user_display_name);
												$display->single_text_response($client, $event, $text_response);
											}

										}										
									}
									break;

								case '..gift':
									$search_result = search_card_v2 (trim($criteria));
									if ($search_result["found"] > 1 || $search_result["found"] == 0) {
										$gobu_logic->logic_controller_for_bagoum($search_result, $command, "text");
									} else {

										$name = trim($search_result['name']);
										$is_claimed = $database->get_waifu_status($name, $db);
										if ($is_claimed == 0) {
											$text_response = sprintf("Nobody claimed %s yet !", $name);
											$display->single_text_response($client, $event, $text_response);
										} else {
											$current_points = $database->get_points($event['source'], $db);
											$current_waifu = $database->get_waifu_count($event['source'], $db);
											$gift_price = 150 * (1 + $current_waifu);
											if ($gift_price > $current_points) {
												$text_response = sprintf("You don't have enough points to gift them (need %s pt) !", $gift_price);
											} else {
												$database->modify_points($event['source'], $db, $gift_price, 0);
												$database->update_gift($db, $name);
												$text_response = sprintf("Gifted %s\n-- Used %s pt --", $name, $gift_price);
											}
											
											$display->single_text_response($client, $event, $text_response);
										}

									}
									break;

								case '..giftall':
									
									$current_waifu = $database->get_waifu_count($event['source'], $db);
									$current_points = $database->get_points($event['source'], $db);
									$gift_price = 150 * (1 + $current_waifu);

									$total_cost = $gift_price * $current_waifu ;
									if ($total_cost > $current_points) {
										$text_response = sprintf("You don't have enough point to gift everyone\n%s Points Needed", $total_cost);
										$display->single_text_response($client, $event, $text_response);
									} else {
										$database->modify_points($event['source'], $db, $total_cost, 0);
										$database->update_gift_all($event['source'], $db);
										$text_response = sprintf("Gifted all your waifus!\n%s Points Used", $total_cost);
										$display->single_text_response($client, $event, $text_response);
									}

									break;

								case '..myclaims':
									$text_response = $database->get_claim($event['source'], $db);
									$display->single_text_response($client, $event, $text_response);
									break;

								case '..huntrate':
									$item_rates = $database->get_area_mod($event['source'], $db);
									$text_response = sprintf("== HUNT CHANCES IN THIS GROUP ==\n\nNormal \t: %d %%\nRare \t: %d %%\nSR \t: %d %%\nSSR \t: %d %%\nLegend \t: %d %%\n",
										$item_rates['MOD_N'], $item_rates['MOD_R'], $item_rates['MOD_SR'],
										$item_rates['MOD_SSR'], $item_rates['MOD_LEGEND']);
									$display->single_text_response($client, $event, $text_response);
									break;

								case '..redeem':
									$code = $exploded_Message[1];
									$points_gained = $database->redeem_coupon ($event, $code, $db, $database);
									$display->single_text_response($client, $event, "Redeemed " . $points_gained . " points");
									break;

								case '..unclaims':
									$search_result = search_card_v2 (trim($criteria));
									if ($search_result["found"] > 1 || $search_result["found"] == 0) {
										$gobu_logic->logic_controller_for_bagoum($search_result, $command, "text");
									} else {
										$name = trim($search_result['name']);
										$is_claimed = $database->get_waifu_status($name, $db);
										if ($is_claimed == 1) {
											$text_response = $database->delete_claim($event['source'], $db, $name, $database);
											// $database->modify_points($event['source'], $db, 5000, 1);
											
											// $display->single_text_response($client, $event, $text_response . "\n-- 5000 Given Back --");
											$display->single_text_response($client, $event, $text_response);
										} else {
											$text_response = sprintf("Nobody claimed %s yet !", $name);
											$display->single_text_response($client, $event, $text_response);
										}
									}
									break;

							}

							//////////////////////////////	
							// Urban Dictionary Router //
							/////////////////////////////

							switch ($command) {
								// Urban Dictionary Function
								case '..ud':
									$gobu_logic->logic_controller_for_urbandictionary($command, $criteria);
									// if ($function_log == 1) {
									// 	$database->create_log_data_ud($event['source'], $command, $criteria, $db);
									// }
									break;

								case '..explain':
									$gobu_logic->logic_controller_for_urbandictionary($command, $criteria);
									// if ($function_log == 1) {
									// 	$database->create_log_data_ud($event['source'], $command, $criteria, $db);
									// }
									break;

								case '..random':
									$gobu_logic->logic_controller_for_urbandictionary($command, "");
									// if ($function_log == 1) {
									// 	$database->create_log_data_ud($event['source'], $command, "Random Stuff", $db);
									// }
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

								case '..simg':
									switch ($exploded_Message[1]) {
										case 'laksek':
											$ori = 'https://i.imgur.com/YlUDgWh.jpg' ;
											$ori_preview = 'https://i.imgur.com/YlUDgWht.jpg' ;
											$response = array($ori, $ori_preview); 
											break;
									}

									if (isset($response)) {
										$display->single_image_response($client, $event, $response);
									}
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
										// $result = $database->update_log_setting (trim($function_log), trim($universal_log));
										$display->single_text_response($client, $event, $result);
									} else {
										$display->show_no_permission($client, $event);
									}
									break;

								case '..gen':
									if ($event['source']['userId'] == 'Uc7871461db4f5476b1d83f71ee559bf0') {
										$database->create_coupon($exploded_Message[1], $exploded_Message[2], $db);
										$display->single_text_response($client, $event, "Coupon Created");
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
									// Testing Sticker
									// $display->single_sticker_response($client, $event);

									// Fixing the Moon
									$search_result = search_card_v2 (trim($criteria));
									$name = trim($search_result['name']);
									$result = $database->get_waifu_status($name, $db);
									$display->single_text_response($client, $event, $result);
									break;

								case '..welcome':
									$display->welcome_sticker_response($client, $event);
									break;

								case '..database':
									$search_result = search_card_v2 (trim($criteria));
									// $gobu_logic->logic_controller_for_database($search_result, "..debugdb", $database, $db);
									break;

								case '..myid':
									$display->single_text_response($client, $event, "Your ID is : " . $event['source']['userId']);
									break;

								case '..rup':
									if ($event['source']['userId'] == 'Uc7871461db4f5476b1d83f71ee559bf0' || 
										$event['source']['userId'] == 'U8fc363c732604bcbb2ffd2fb256b3bd8') {
										$database->modify_rate($db, $event['source']['groupId'], 1);
										$display->single_text_response($client, $event, "Item Rate UP !");
									} else {
										$display->show_no_permission($client, $event);
									}
									break;							

								case '..rdown':
									if ($event['source']['userId'] == 'Uc7871461db4f5476b1d83f71ee559bf0' || 
										$event['source']['userId'] == 'U8fc363c732604bcbb2ffd2fb256b3bd8') {
										$database->modify_rate($db, $event['source']['groupId'], 0);
										$display->single_text_response($client, $event, "Item rate DOWN !");
									} else {
										$display->show_no_permission($client, $event);
									}
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
	                		$response = "Error Occured\n\n- Details -" . PHP_EOL . "File Location : " . $e->getFile() . PHP_EOL . "Line Number : " . $e->getLine() . PHP_EOL . "Type : " . $e->getMessage() . "\n\nMake sure you have added Gobu as friend to avoid future error";
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

<!-- UPDATE `AREA_LIST` SET `MOD_N` = '10', `MOD_R` = '60', `MOD_SR` = '25' WHERE `AREA_LIST`.`ID_GROUP` = 'Cbe11f05ca744703274a284626f9212ac'; -->