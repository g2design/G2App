<?php

namespace G2Design\ClassStructs;

class Singleton {
	
	protected  function __construct() {
		;
	}
	
	public static function getInstance() {
		static $instance = null;
		if (null === $instance) {
			$instance = new static();
		}

		return $instance;
	}
	
}

