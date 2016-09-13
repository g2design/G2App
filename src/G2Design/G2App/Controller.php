<?php

namespace G2Design\G2App;

class Controller extends Base {
	
	public function _404(){
		http_response_code(404);
		echo "
			<h1>404 page does not exist</h1>
		";

	}

	public function redirect($loc)
	{
		global $config;
		
		if(strpos($loc, BASE_URL) === false) {
			$loc = BASE_URL . $loc;
		}
		header('Location: '. $loc);
	}
	
}