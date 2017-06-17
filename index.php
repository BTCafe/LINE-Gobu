<?php

	require_once( __DIR__ . '/src/LINEBotTiny.php');

	require_once( __DIR__ . '/conf/channel_key.php');

	require_once( __DIR__ . '/func/func_main.php');
	
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
						
						try {

							/////////////////////////////////////////	
							// Works On Personal and Group Account//
							///////////////////////////////////////

							switch ($exploded_Message[0]) {
								
								case '..flair':
									$response = get_similar_card ($exploded_Message[1], $exploded_Message[0]);
									$client->replyMessage(array(
					                        'replyToken' => $event['replyToken'],
					                        'messages' => array(
					                            array(
					                                'type' => 'text',
					                                'text' => $response
					                            )
					                        )
					                ));
									break;

								case '..find':
									$response = get_similar_card ($exploded_Message[1], $exploded_Message[0]);
									$client->replyMessage(array(
					                        'replyToken' => $event['replyToken'],
					                        'messages' => array(
					                            array(
					                                'type' => 'text',
					                                'text' => $response
					                            )
					                        )
					                ));
									break;

							}

							///////////////////
							// Log Function //
							/////////////////
							
							// if (substr($message['text'], 0, 2) === "..") {
							// 	fm_create_log_data($event['source'], $message['text']);		
							// }

							// if (substr($message['text'], 0, 1) === "@") {
							// 	fm_create_log_data($event['source'], $message['text']);		
							// }

							// Double Check For Closing Database Connection
							if (is_resource($db) && get_resource_type($db) === 'mysql link') {
								mysqli_close($db);
							}

						} catch (Exception $e) {
	                		$text_response = "Sorry, An Error Just Occured" . PHP_EOL . $e->getMessage();	
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