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
		if(strpos($loc, \G2Design\Utils\Functions::get_current_site_url()) === false) {
			$loc = \G2Design\Utils\Functions::get_current_site_url() . $loc;
		}
		header('Location: '. $loc);
	}
	
}