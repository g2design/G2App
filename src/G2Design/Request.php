<?php

namespace G2Design;

class Request extends ClassStructs\Singleton {

	var $data = null;

	protected function __construct() {

		if (!isset($_SERVER['SERVER_ADDR'])) {
			global $argv;
			$request_url = $argv[1];
//				$script_url = $request_url;

			print(var_export($request_url, true));
//				print(var_export($request_url, true));
		} else {
			// Get request url and script url
			$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
			$script_url = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
		}
		

		if ($request_url != $script_url) {
			
			
			
			$url = trim(preg_replace('/' . str_replace('/', '\/', str_replace('index.php', '', $script_url)) . '/', '', $request_url, 1), '/');
			
			$request = explode('/', trim($request_url, '/'));
			$script = explode('/', trim($script_url, '/'));
			$new = [];
			$folder = [];
			foreach($request as $index => $part) {
				if($part != $script[$index]) {
					$new[] = $part;
				} else {
					$folder[] = $part;
				}
			}
			
			$url = implode('/', $new);
			$folder =  implode('/', $folder);
		} else {
			$url = null;
		}

		//Strip getter from url
		if (strpos($url, '?') !== false) {
			$index = strpos($url, '?');
			$url = substr($url, 0, $index);
		}
		
		$this->data['route'] = $url;
		$this->data['folder'] = $folder;
	}

	static function route() {
		$in = self::getInstance();

		return $in->route;
	}
	static function folder(){
		$in = self::getInstance();

		return $in->folder;
	}
	
	public function __get($name) {
		return $this->data[$name];
	}

}
