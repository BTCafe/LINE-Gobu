<?php

	class display 
	{
		function __construct()
		{
			
		}

		static function single_text_response ($client, $event, $response){
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

		static function single_image_response ($client, $event, $response) {
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

		static function single_video_response ($client, $event, $response) {
			$video_url = $response[0] ;
			$video_preview = $response[1] ;
			$client->replyMessage(array(
	                'replyToken' => $event['replyToken'],
	                'messages' => array(
	                    array(
	                        'type' => 'video',
	                        'originalContentUrl' => $video_url,
	                        'previewImageUrl' => $video_preview
	                    )
	                )
	        ));
		}

		static function confirm_response ($client, $event, $search_result, $command){
			$splitted_name_stack = explode ("\n", $search_result['name']);
			$formatted_stack = array();
			for ($i=0; $i < $search_result['found']; $i++) {
				$formatted_stack[$i] = array('name'=> $splitted_name_stack[$i], 'command'=> $command . " " . $splitted_name_stack[$i]);
			}

			switch (count($formatted_stack)) {
				case 2:
					carousel_response_two($client, $event, $search_result, $formatted_stack);
					break;

				case 3:
					carousel_response_three($client, $event, $search_result, $formatted_stack);
					break;

				case 4:
					carousel_response_four($client, $event, $search_result, $formatted_stack);
					break;

				case 5:
					carousel_response_five($client, $event, $search_result, $formatted_stack);
					break;
			}
		}

		static function show_maintenance_message ($client, $event){
			$client->replyMessage(array(
		        'replyToken' => $event['replyToken'],
		        'messages' => array(
		            array(
		                'type' => 'text',
		                'text' => "This command is currently disabled"
		            )
		        )
	        ));
		}

		static function show_too_many_result ($client, $event, $total_result_found){
			$client->replyMessage(array(
		        'replyToken' => $event['replyToken'],
		        'messages' => array(
		            array(
		                'type' => 'text',
		                'text' => "Found " . $total_result_found . " card with that criteria. That's too many for me~"
		            )
		        )
	        ));
		}		

		static function show_result_more_than_5 ($client, $event, $total_result_found, $list_card_name){
			$client->replyMessage(array(
		        'replyToken' => $event['replyToken'],
		        'messages' => array(
		            array(
		                'type' => 'text',
		                'text' => "Found " . $total_result_found . " card with that criteria.\n\n" . $list_card_name
		            )
		        )
	        ));
		}	

		static function show_no_result ($client, $event){
			$client->replyMessage(array(
		        'replyToken' => $event['replyToken'],
		        'messages' => array(
		            array(
		                'type' => 'text',
		                'text' => "No card found with that criteria"
		            )
		        )
	        ));
		}

	}

	function carousel_response_two ($client, $event, $search_result, $formatted_stack){
		$client->replyMessage(array(
	        'replyToken' => $event['replyToken'],
	        'messages' => array(
	        	// First Message
	        	array(
	                'type' => 'text',
	                'text' => "Found " . $search_result['found'] . " with that criteria :\n\n" . $search_result['name']
	            ),

	        	// Second Message
	            array(
	                'type' => 'template',
	                'altText' => "Only viewable on LINE Mobile",

	                // Carousel Header
	                'template' => array(
	                	'type' => "carousel",

	                	// Carousel Object
	                	'columns' => array(
	                		
	                		// Carousel First Object
	                		array(
	                			'title' => "Result (1 of 2)",
	                			'text' => $formatted_stack[0]['name'],

	                			// Action inside of carousel 1
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[0]['command']	
	                        		)
	                        	)
	                		),
	                		
	                		// Carousel Second Object
	                		array(
	                			'title' => "Result (2 of 2)",
	                			'text' => $formatted_stack[1]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[1]['command']
	                        		)
	                        	)
	                		)

	                	)
	                )
	            )
	        )
	    ));
	}

	function carousel_response_three ($client, $event, $search_result, $formatted_stack){
		$client->replyMessage(array(
	        'replyToken' => $event['replyToken'],
	        'messages' => array(
	        	// First Message
	        	array(
	                'type' => 'text',
	                'text' => "Found " . $search_result['found'] . " with that criteria :\n\n" . $search_result['name']
	            ),

	        	// Second Message
	            array(
	                'type' => 'template',
	                'altText' => "Only viewable on LINE Mobile",

	                // Carousel Header
	                'template' => array(
	                	'type' => "carousel",

	                	// Carousel Object
	                	'columns' => array(
	                		
	                		// 1
	                		array(
	                			'title' => "Result (1 of 3)",
	                			'text' => $formatted_stack[0]['name'],

	                			// Action inside of carousel 1
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[0]['command']	
	                        		)
	                        	)
	                		),
	                		
	                		// 2
	                		array(
	                			'title' => "Result (2 of 3)",
	                			'text' => $formatted_stack[1]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[1]['command']
	                        		)
	                        	)
	                		),

	                		// 3
	                		array(
	                			'title' => "Result (3 of 3)",
	                			'text' => $formatted_stack[2]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[2]['command']
	                        		)
	                        	)
	                		)

	                	)
	                )
	            )
	        )
	    ));
	}

	function carousel_response_four ($client, $event, $search_result, $formatted_stack){
		$client->replyMessage(array(
	        'replyToken' => $event['replyToken'],
	        'messages' => array(
	        	// First Message
	        	array(
	                'type' => 'text',
	                'text' => "Found " . $search_result['found'] . " with that criteria :\n\n" . $search_result['name']
	            ),

	        	// Second Message
	            array(
	                'type' => 'template',
	                'altText' => "Only viewable on LINE Mobile",

	                // Carousel Header
	                'template' => array(
	                	'type' => "carousel",

	                	// Carousel Object
	                	'columns' => array(
	                		
	                		// 1
	                		array(
	                			'title' => "Result (1 of 4)",
	                			'text' => $formatted_stack[0]['name'],

	                			// Action inside of carousel 1
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[0]['command']	
	                        		)
	                        	)
	                		),
	                		
	                		// 2
	                		array(
	                			'title' => "Result (2 of 4)",
	                			'text' => $formatted_stack[1]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[1]['command']
	                        		)
	                        	)
	                		),

	                		// 3
	                		array(
	                			'title' => "Result (3 of 4)",
	                			'text' => $formatted_stack[2]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[2]['command']
	                        		)
	                        	)
	                		),

	                		// 4
	                		array(
	                			'title' => "Result (4 of 4)",
	                			'text' => $formatted_stack[3]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[3]['command']
	                        		)
	                        	)
	                		)

	                	)
	                )
	            )
	        )
	    ));
	}

	function carousel_response_five ($client, $event, $search_result, $formatted_stack){
		$client->replyMessage(array(
	        'replyToken' => $event['replyToken'],
	        'messages' => array(
	        	// First Message
	        	array(
	                'type' => 'text',
	                'text' => "Found " . $search_result['found'] . " with that criteria :\n\n" . $search_result['name']
	            ),

	        	// Second Message
	            array(
	                'type' => 'template',
	                'altText' => "Only viewable on LINE Mobile",

	                // Carousel Header
	                'template' => array(
	                	'type' => "carousel",

	                	// Carousel Object
	                	'columns' => array(
	                		
	                		// 1
	                		array(
	                			'title' => "Result (1 of 5)",
	                			'text' => $formatted_stack[0]['name'],

	                			// Action inside of carousel 1
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[0]['command']	
	                        		)
	                        	)
	                		),
	                		
	                		// 2
	                		array(
	                			'title' => "Result (2 of 5)",
	                			'text' => $formatted_stack[1]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[1]['command']
	                        		)
	                        	)
	                		),

	                		// 3
	                		array(
	                			'title' => "Result (3 of 5)",
	                			'text' => $formatted_stack[2]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[2]['command']
	                        		)
	                        	)
	                		),

	                		// 4
	                		array(
	                			'title' => "Result (4 of 5)",
	                			'text' => $formatted_stack[3]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[3]['command']
	                        		)
	                        	)
	                		),

	                		// 5
	                		array(
	                			'title' => "Result (5 of 5)",
	                			'text' => $formatted_stack[4]['name'],

	                			// Action inside of carousel 2
	                        	'actions' => array(
	                        		array(
	                        			'type' => 'message',
	                        			'label' => 'Confirm',
	                        			'text' => $formatted_stack[4]['command']
	                        		)
	                        	)
	                		)

	                	)
	                )
	            )
	        )
	    ));
	}
	
?>