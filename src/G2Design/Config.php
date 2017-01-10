<?php

namespace G2Design;

class Config extends ClassStructs\Singleton {

	var $config = null;

	static function load($file) {
		$in = self::getInstance();

		$in->config = \Zend\Config\Factory::fromFile($file, true);
	}

	static function load_env($env) {
		$in = self::getInstance();

		if (!$in->config) {
			throw new Exception("First Load the configuration file with ::load()");
		}

		//Find the enviroments config area
		if (isset($in->config->enviroments->{$env})) {
			$enviroment = $in->config->enviroments->$env;
			if(is_string($enviroment) && file_exists($enviroment)) {
				$enviroment = \Zend\Config\Factory::fromFile($enviroment,true);
			} else if (is_string($enviroment)) {
				throw new Exception("String was provide in configuration file. But is not a valid file");
			}
			$in->config = $in->config->merge($enviroment);
		}
		
	}

	/**
	 * 
	 * @return \Zend\Config\Config
	 */
	static function &get() {
		$in = self::getInstance();
		return $in->config;
	}

}
