<?php 

	// Finding The Definition From Urban Dictionary
	function exact_term ($term){
		$term_url = 'http://api.urbandictionary.com/v0/define?term=' . str_replace(' ', '+', $term);
		$term_json = file_get_contents($term_url);
		$term_array = json_decode($term_json, true);
		if (count($term_array['list']) == 0) {
			$term_return = no_result_text();
		} else {
			$random_array_number = rand(0,count($term_array['list'])-1);
			$term_return = format_return_text($term_array, $random_array_number, count($term_array['list']));
		}
		return $term_return ;
	}

	function random_term (){
		$term_url = 'http://api.urbandictionary.com/v0/random';
		$term_json = file_get_contents($term_url);
		$term_array = json_decode($term_json, true);
		$random_array_number = rand(0,count($term_array['list'])-1);
		$term_return = format_return_text($term_array, $random_array_number, count($term_array['list']));
		return $term_return ;	
	}

	function format_return_text ($term_array, $chosen_array, $variation_total){
		$word_format = ucwords($term_array['list'][$chosen_array]['word']);
		$definition_format = "> " . $term_array['list'][$chosen_array]['definition'];
		$example_format = "Example :\n" . $term_array['list'][$chosen_array]['example'];
		$variation_format = 'This is variation ' . ($chosen_array + 1) .  ' of ' . $variation_total ;
		$source_format = "Source : https://www.urbandictionary.com" ;

		$term_result_array = array ($word_format, $definition_format, $example_format, $variation_format, $source_format);
		$text_return = implode("\n\n",$term_result_array) . "";
		
		return strip_tags($text_return) ;
	}

	function no_result_text (){
		$text_return = "No definition found" ;
		return strip_tags($text_return) ;
	}

?>