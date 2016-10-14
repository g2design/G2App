<?php

namespace G2Design;
use R;

class Database extends R {
	
	private static $dsn, $username, $password, $frozen, $connected, $page_count, $current_page;

	static function setup(\Zend\Config\Config $config) {
		if(!$config instanceof \Traversable) {
			$config = [ $config ];
		}
		
		foreach($config as $db) {
			self::setup_factory($db);
		}
	}
	
	private static function setup_factory($config) {
		
		
		switch ($config->type) {
			
			case "sqlite" :
				
				//Create dir if not exit
				if(!is_dir(dirname($config->file))) {
					mkdir( dirname($config->file), 0777, true );
				}
				
				self::addDatabase($config->name, 'sqlite:'. $config->file );
				break;
			case "mysql":
				self::addDatabase($config->name, "mysql:host=$config->host;dbname=$config->database", $config->username, $config->password);
				
			
		}
		
		if(isset($config->default)) {
			self::selectDatabase($config->name);
		}
	}
	
	/**
	 *
	 * @param type $type
	 * @param type $limit
	 * @param type $sql
	 * @param type $bindings
	 * @return type
	 */
	public static function paginate_findAll($type, $limit, $sql = NULL, $bindings = array()) {

		//Get the current Page
		if (isset($_GET['p'])) {
			$page = $_GET['p'];
		} else
			$page = 1;
		self::$current_page = $page;
		self::$page_count = ceil(R::count($type, $sql, $bindings) / $limit);
		return self::findAll($type, $sql . ' LIMIT ' . (($page - 1) * $limit ) . ', ' . $limit, $bindings);
	}

	public static function paginate_query($sql, $limit) {
		if (isset($_GET['p'])) {
			$page = $_GET['p'];
		} else
			$page = 1;
		self::$current_page = $page;
		$data = self::getAll($sql . ' LIMIT ' . (($page - 1) * $limit ) . ', ' . $limit, $bindings);
		self::$page_count = ceil(count(self::getAll($sql)) / $limit);
		return $data;
	}

	/**
	 * Returns the last paginate_findAll page count
	 * @return type
	 */
	public static function get_last_total_pages() {
		return self::$page_count;
	}

	public static function get_current_page() {
		return self::$current_page;
	}

}
