<?php

namespace G2Design\G2App;

abstract class Cron extends Base {
	
	var $schedule = null,$output = null, $name = null;
	var $closure = null, $params = [];
	
	function __construct($name, $schedule, $output){
		$this->schedule = $schedule;
		$this->output = $output;
		$this->closure = function(){};
	}
	
	/**
	 * Returns a closure function to be run
	 * 
	 * @return type
	 */
	function run(){
		return $this->closure;
	}
	
	function set_closure($closure) {
		$this->closure = $closure;
	}
	
}