<?php

namespace G2Design;

class Config extends ClassStructs\Singleton {

	var $config = null;

	static function load($file) {
		$in = self::getInstance();

//		$in->config = \Zend\Config\Factory::fromFile($file, true);
		$reader = new \Zend\Config\Reader\Json();
		$config = $reader->fromFile($file);
		$in->config = new \Zend\Config\Config($config, true);
		//Create depends script

		if (isset($in->config->depends)) { // depends exists
			foreach ($in->config->depends as $conf) {
				if (is_string($conf) && file_exists($conf)) {
//					$dp_conf = \Zend\Config\Factory::fromFile($conf, true);
					$config = $reader->fromFile($conf);
					$dp_conf = new \Zend\Config\Config($config, true);
					$conf_new = $dp_conf->merge($in->config);
					$in->config = $conf_new;
				}
			}
		}

		//Complete all load functions
		self::load_config($in->config);
	}

	static function load_config(&$config) {
		try {
			$new_config = [];
			$recheck = true;
			while ($recheck) {
				$recheck = false;
				if (is_array($config) || $config instanceof \Traversable) {
					foreach ($config as $key => $value) {
						if (is_string($value) && strpos($value, 'load-config:') !== false) { // Found Load Path
							$config_ar = $config->toArray();

							$file = str_replace('load-config:', '', $value);

							$reader = new \Zend\Config\Reader\Json();
							$c_ar = $reader->fromFile($file);
							$new_config = array_merge($new_config, $c_ar);

							if ($key > 0) {
								$new_config = array_merge(array_slice($config_ar, 0, $key), $c_ar, array_slice($config_ar, $key + 1, count($config_ar) - $key));
							} else {
								$new_config = array_merge($c_ar, array_slice($config_ar, $key + 1, count($config_ar) - $key));
							}

							$recheck = true;
						} else {
							$config->{$key} = self::load_config($value);
						}
					}
				}

				$config = empty($new_config) ? $config : new \Zend\Config\Config($new_config, true);
			}

			return $config;
		} catch (Exception $ex) {
			echo $ex->getMessage();
			exit;
		}
	}

	static function load_env($env) {
		$in = self::getInstance();

		if (!$in->config) {
			throw new Exception("First Load the configuration file with ::load()");
		}

		//Find the enviroments config area
		if (isset($in->config->enviroments->{$env})) {
			$enviroment = $in->config->enviroments->$env;
			if (is_string($enviroment) && file_exists($enviroment)) {
				$enviroment = \Zend\Config\Factory::fromFile($enviroment, true);
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
