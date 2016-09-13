<?php

namespace G2Design;

class Config extends ClassStructs\Singleton {
	var $config = null;
	
	static function load($file) {
		$in = self::getInstance();
		
		$in->config = \Zend\Config\Factory::fromFile($file, true);
	}
	
	/**
	 * 
	 * @return \Zend\Config
	 */
	static function get(){
		$in = self::getInstance();
		return $in->config;
	}
	
	
}