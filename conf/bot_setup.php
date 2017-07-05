<?php
	$file = fopen(__DIR__ . '/admin_setup.txt', 'r');
	$setting = array();
	while ($line = fgets($file)) {
		$exploded = explode('=', $line);
		$setting[$exploded[0]] = $exploded[1];
	}
	fclose($file);
	$function_log = $setting['function_log'];
	$universal_log = $setting['universal_log'];
?>