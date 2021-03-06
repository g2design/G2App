<?php

namespace G2Design\G2App;

use Aura\Session;

class Base {
	/**
	 *
	 * @var Session\Segment
	 */
	static $session = [];
	
	/**
	 *
	 * @var Mono 
	 */
	static $logger = [];
	static $session_manager = null;
	
	var $session_key = null;

	function __module_dir($module = false) {

		$file_uri = $this->__running_module_file();

		return \G2Design\G2App::__module_dir($file_uri).'/';
		
	}
	
	function __running_module_file() {
		$t = debug_backtrace();
		$file_uri = $t[0]['file'];
		foreach ($t as $row) {
			$file = $row['file'] . '<br>';
			if (strpos($file, \G2Design\G2App::__module_dir($file)) !== false) {
				$file_uri = $file;
				break;
			}
		}
		
		return $file_uri;
	}
	
	function __module_instance() {
		return \G2Design\G2App::__module_instance($this->__running_module_file());
	}
	
	/**
	 * 
	 * @return Session\Segment;
	 */
	function session() {
		$id = $this->session_key != null ? $this->session_key : getcwd();
		
		if(!isset(self::$session[$id])) {
			$factory = new Session\SessionFactory();
			$session = $factory->newInstance($_COOKIE);
			$session->setCacheExpire(24*60);
			self::$session_manager = $session;
			self::$session[$id] = $session->getSegment($id);
		}
		
		return self::$session[$id];
	}
	
	/**
	 * 
	 * @return Aura\Session\Session
	 */
	function session_manager() {
		return self::$session_manager != null ? self::$session_manager : false;
	}
	
	/**
	 * 
	 * @staticvar type $logger
	 * @return \Monolog\Logger
	 */
	function logger() {
		static $logger = null;
		$id = get_class($this->__module_instance());
		if(!isset(self::$logger[$id])) {
			$logger = new \Monolog\Logger($id);
			$logger->pushHandler(new \Monolog\Handler\ChromePHPHandler());
			\Monolog\ErrorHandler::register($logger, [\Psr\Log\LogLevel::ERROR, \Psr\Log\LogLevel::WARNING], [\Psr\Log\LogLevel::ERROR]);
			self::$logger[$id] = $logger;
		}
		
		return self::$logger[$id];
	}

}
