<?php

namespace G2Design;
use R;

class Database extends R {

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
				self::selectDatabase($config->name);
				break;
			
		}
	}

}
