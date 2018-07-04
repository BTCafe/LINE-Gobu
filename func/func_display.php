<?php



	class display 

	{

		function __construct()

		{

			

		}

		static function single_sticker_response ($client, $event){

			$client->replyMessage(array(

		        'replyToken' => $event['replyToken'],

		        'messages' => array(

		            array(

		                'type' => 'sticker',

		                'packageId' => 2, 

		                'stickerId' => 45

		            )

		        )

	        ));

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



		static function double_text_audio_response ($client, $event, $voice_description, $audio_url){

			$client->replyMessage(array(

		        'replyToken' => $event['replyToken'],

		        'messages' => array(

		            array(

		                'type' => 'text',

		                'text' => $voice_description

		            ),

		            array(

		                'type' => 'audio',

		                'originalContentUrl' => $audio_url,

		                'duration' => 30000

		            )

		        )

	        ));

		}



		static function double_text_response ($client, $event, $message1, $message2){

			$client->replyMessage(array(

		        'replyToken' => $event['replyToken'],

		        'messages' => array(

		            array(

		                'type' => 'text',

		                'text' => $message1

		            ),

		            array(

		                'type' => 'text',

		                'text' => $message2

		            )

		        )

	        ));

		}

		static function double_text_and_image_response ($client, $event, $text_data, $image_data){

			$ori = $image_data[0] ;

			$ori_preview = $image_data[1] ;

			$client->replyMessage(array(

		        'replyToken' => $event['replyToken'],

		        'messages' => array(

		            array(

		                'type' => 'text',

		                'text' => $text_data

		            ),

		            array(

		                'type' => 'image',

                        'originalContentUrl' => $ori,

                        'previewImageUrl' => $ori_preview

		            )

		        )

	        ));

		}

		static function congrats ($client, $event, $message){

			$client->replyMessage(array(

		        'replyToken' => $event['replyToken'],

		        'messages' => array(

		            array(
                        'type' => 'image',
                        'originalContentUrl' => "https://i.imgur.com/5K2k92B.jpg", // You might want to open this ^^
                        'previewImageUrl' => "https://i.imgur.com/5K2k92Bt.jpg"
                    ),

		            array(

		                'type' => 'text',

		                'text' => $message

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



		static function show_input_error ($client, $event){

			$client->replyMessage(array(

		        'replyToken' => $event['replyToken'],

		        'messages' => array(

		            array(

		                'type' => 'text',

		                'text' => "Something is missing ... try '..help' command to check"

		            )

		        )

		    ));

		}



		static function show_no_permission ($client, $event){

			$client->replyMessage(array(

		        'replyToken' => $event['replyToken'],

		        'messages' => array(

		            array(

		                'type' => 'text',

		                'text' => "Sorry, you don't have permission to do that~"

		            )

		        )

		    ));

		}



		static function show_contact_menu ($client, $event){

			$client->replyMessage(array(

	            'replyToken' => $event['replyToken'],

	            'messages' => array(

	                array(

	                    'type' => 'template',



	                    'altText' => "Only viewable in LINE Mobile",



	                    // The Button Content

	                    'template' => array(



	                    	'type' => "buttons",

	                    	'title' => "Contact Menu",

	                    	'text' => "Feel free to suggest a new feature or any bug you find !",



	                    	// Action to take between the three

	                    	'actions' => array(

	                    		array(

	                    			'type' => 'uri',

	                    			'label' => 'Feedbacks via Email',

	                    			'uri' => "mailto:minerva.bot.developer@gmail.com?subject=Feedback%20for%20Gobu"	

	                    		)

	                    	)

	                    )

	                )

	            )

	        ));

		}

		static function show_flair_game_choices ($client, $event, $answer, $filler){
			$filler[3] = $answer["name"];
			shuffle($filler);

			$client->replyMessage(array(

	            'replyToken' => $event['replyToken'],

	            'messages' => array(
	            	array(
	            		'type' => 'text',
	            		'text' => $answer["flair"]
	            	),

	                array(

	                    'type' => 'template',

	                    'altText' => "Only viewable in LINE Mobile",

	                    // The Button Content

	                    'template' => array(

	                    	'type' => "buttons",

	                    	'title' => "Guess The Flair !",

	                    	'text' => "Who has the above flair ?",

	                    	// Action to take

	                    	'actions' => array(

	                    		array(
	                    			'type' => 'message',
	                    			'label' => trim_text_for_label($filler[0]),
	                    			'text' => "..flair " . $filler[0]	
	                    		),
	                    		array(
	                    			'type' => 'message',
	                    			'label' => trim_text_for_label($filler[1]),
	                    			'text' => "..flair " . $filler[1]	
	                    		),
	                    		array(
	                    			'type' => 'message',
	                    			'label' => trim_text_for_label($filler[2]),
	                    			'text' => "..flair " . $filler[2]
	                    		),
	                    		array(
	                    			'type' => 'message',
	                    			'label' => trim_text_for_label($filler[3]),
	                    			'text' => "..flair " . $filler[3]	
	                    		)

	                    	)

	                    )

	                )

	            )

	        ));

		}


		static function carousel_for_social_media ($client, $event){

			$client->replyMessage(array(

	        'replyToken' => $event['replyToken'],

	        'messages' => array(

	        	// First Message

	        	array(

	                'type' => 'text',

	                'text' => "Found"

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

	                			'text' => "Result (1 of 2)",
                    			'thumbnailImageUrl' => 'https://pbs.twimg.com/media/DRD_i4GV4AAdqWt.jpg',

	                			// Action inside of carousel 1

	                        	'actions' => array(

	                        		array(

	                        			'type' => 'uri',

	                        			'label' => 'View Full',

	                        			'uri' => "https://pbs.twimg.com/media/DRD_i4GV4AAdqWt.jpg"	

	                        		)

	                        	)

	                		),
              		
	                		// Carousel Second Object

	                		array(

	                			'text' => "Result (2 of 2)",
                    			'thumbnailImageUrl' => 'https://pbs.twimg.com/media/DRA_ThnU8AAaZ-v.jpg',

	                			// Action inside of carousel 2

	                        	'actions' => array(

	                        		array(

	                        			'type' => 'uri',

	                        			'label' => 'View Full',

	                        			'uri' => "https://pbs.twimg.com/media/DRA_ThnU8AAaZ-v.jpg"

	                        		)

	                        	)

	                		)

	                	)

	                )

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

	function trim_text_for_label ($input){		
        $input = strip_tags($input);
  
	    //no need to trim, already shorter than trim length
	    if (strlen($input) <= 20) {
	        return $input;
	    }
	  
	    //find last space within length
	    $last_space = strrpos(substr($input, 0, 18), ' ');
	    $trimmed_text = substr($input, 0, $last_space);
	  
    	//add ellipses (..)
        $trimmed_text .= '..';
	  
	    return $trimmed_text;
	}

	function carousel_response_for_twitter_with_picture ($client, $event, $tweets_data, $media_stack){
		$text_builder = twitter_text_builder($tweets_data);
		$number_of_picture = count($media_stack);

		switch ($number_of_picture) {
			case 1:
				$client->replyMessage(array(

			        'replyToken' => $event['replyToken'],

			        'messages' => array(

			        	// First Message

			        	array(

			                'type' => 'text',

			                'text' => $text_builder

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

			                			'text' => "Image (1 of $number_of_picture)",
		                    			'thumbnailImageUrl' => $media_stack[0],

			                			// Action inside of carousel 1

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[0]	

			                        		)

			                        	)

			                		)

			                	)

			                )

			            )

			        )

			    ));
				break;
			case 2:
				$client->replyMessage(array(

			        'replyToken' => $event['replyToken'],

			        'messages' => array(

			        	// First Message

			        	array(

			                'type' => 'text',

			                'text' => $text_builder

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

			                			'text' => "Image (1 of $number_of_picture)",
		                    			'thumbnailImageUrl' => $media_stack[0],

			                			// Action inside of carousel 1

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[0]	

			                        		)

			                        	)

			                		),
		              		
			                		// Carousel Second Object

			                		array(

			                			'text' => "Image (2 of $number_of_picture)",
		                    			'thumbnailImageUrl' => $media_stack[1],

			                			// Action inside of carousel 2

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[1]

			                        		)

			                        	)

			                		)

			                	)

			                )

			            )

			        )

			    ));
				break;
			case 3:
				$client->replyMessage(array(

			        'replyToken' => $event['replyToken'],

			        'messages' => array(

			        	// First Message

			        	array(

			                'type' => 'text',

			                'text' => $text_builder

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

			                			'text' => "Image (1 of 3)",
		                    			'thumbnailImageUrl' => $media_stack[0],

			                			// Action inside of carousel 1

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[0]	

			                        		)

			                        	)

			                		),
		              		
			                		// Carousel Second Object

			                		array(

			                			'text' => "Image (2 of 3)",
		                    			'thumbnailImageUrl' => $media_stack[1],

			                			// Action inside of carousel 2

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[1]

			                        		)

			                        	)

			                		),
		              		
			                		// Carousel Third Object

			                		array(

			                			'text' => "Image (3 of 3)",
		                    			'thumbnailImageUrl' => $media_stack[2],

			                			// Action inside of carousel 2

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[2]

			                        		)

			                        	)

			                		)

			                	)

			                )

			            )

			        )

			    ));
				break;
			case 4:
				$client->replyMessage(array(

			        'replyToken' => $event['replyToken'],

			        'messages' => array(

			        	// First Message

			        	array(

			                'type' => 'text',

			                'text' => $text_builder

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

			                			'text' => "Image (1 of 4)",
		                    			'thumbnailImageUrl' => $media_stack[0],

			                			// Action inside of carousel 1

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[0]	

			                        		)

			                        	)

			                		),
		              		
			                		// Carousel Second Object

			                		array(

			                			'text' => "Image (2 of 4)",
		                    			'thumbnailImageUrl' => $media_stack[1],

			                			// Action inside of carousel 2

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[1]

			                        		)

			                        	)

			                		),
		              		
			                		// Carousel Third Object

			                		array(

			                			'text' => "Image (3 of 4)",
		                    			'thumbnailImageUrl' => $media_stack[2],

			                			// Action inside of carousel 2

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[2]

			                        		)

			                        	)

			                		),
		              		
			                		// Carousel Fourth Object

			                		array(

			                			'text' => "Image (4 of 4)",
		                    			'thumbnailImageUrl' => $media_stack[3],

			                			// Action inside of carousel 2

			                        	'actions' => array(

			                        		array(

			                        			'type' => 'uri',

			                        			'label' => 'View Full',

			                        			'uri' => $media_stack[3]

			                        		)

			                        	)

			                		)

			                	)

			                )

			            )

			        )

			    ));
				break;
		}

	}

	function twitter_text_builder ($tweets_data){
		$posted_time = date("d M Y", strtotime($tweets_data->created_at));
		$text_builder = "@" . $tweets_data->user->screen_name . " tweets \n> $posted_time\n\n" . $tweets_data->text;
		return $text_builder;
	}
	

?>