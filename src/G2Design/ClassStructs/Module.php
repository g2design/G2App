<?php

namespace G2Design\ClassStructs;

abstract class Module {
	var $app=  null;

	abstract function init();
	function connect(\G2Design\G2App $app){
		$this->app = $app;
	}
	
	function add_route($slug, callable $function) {
		$this->app->router->any(strtolower(get_class($this)).'/'.$slug, $function);
	}
	
	function addController($slug, $controller) {
		$this->app->router->controller(strtolower(get_class($this)).'/'.$slug, $controller);
	}
}
