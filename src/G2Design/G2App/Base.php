<?php

namespace G2Design\G2App;

use Aura\Session;

class Base {
	/**
	 *
	 * @var Session\Segment
	 */
	static $session = null;

	function get_module_dir($module = false) {

		$file_uri = $this->get_running_module_file();

		return \G2Design\G2App::get_module_dir($file_uri).'/';
		
	}
	
	function get_running_module_file() {
		$t = debug_backtrace();
		$file_uri = $t[0]['file'];
		foreach ($t as $row) {
			$file = $row['file'] . '<br>';
			if (strpos($file, \G2Design\G2App::get_module_dir($file)) !== false) {
				$file_uri = $file;
				break;
			}
		}
		
		return $file_uri;
	}
	
	function get_module_instance() {
		return \G2Design\G2App::get_module_instance($this->get_running_module_file());
	}
	
	/**
	 * 
	 * @return Session\Segment;
	 */
	function session() {
		if(!isset(self::$session)) {
			$factory = new Session\SessionFactory();
			$session = $factory->newInstance($_COOKIE);
			self::$session = $session->getSegment(get_class($this->get_module_instance()));
		}
		
		return self::$session;
	}

}
