<?php

	require_once( __DIR__ . '/src/LINEBotTiny.php');

	require_once( __DIR__ . '/conf/channel_key.php');
	require_once( __DIR__ . '/conf/db_connection.php');

	require_once( __DIR__ . '/func/func_main.php');

	function single_text_response ($client, $event, $response){
		$client->replyMessage(array(
	        'replyToken' => $event['replyToken'],
	        'messages' => array(
	            array(
	                'type' => 'text',
	                'text' => $response
	            )
	        )
        ));
	}

	function single_image_response ($client, $event, $response) {
		$ori = $response[0] ;
		$ori_preview = $response[1] ;
		$client->replyMessage(array(
                'replyToken' => $event['replyToken'],
                'messages' => array(
                    array(
                        'type' => 'image',
                        'originalContentUrl' => $ori,
                        'previewImageUrl' => $ori_preview
                    )
                )
        ));
	}
	
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