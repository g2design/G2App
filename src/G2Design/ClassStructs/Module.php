<?php

namespace G2Design\ClassStructs;
use ReflectionClass;

abstract class Module extends \G2Design\G2App\Base {
	var $app=  null, $controllers = [];
	protected $registered_crons = [];

	abstract function init();
	function connect(\G2Design\G2App $app){
		$this->app = $app;
	}
	
	function add_route($slug, callable $function) {
		$this->app->router->any(strtolower(get_class($this)).'/'.$slug, $function);
	}
	
	function addController($slug, $controller) {
		
		$reflection = new ReflectionClass(get_class($this));
		$slug = strtolower($reflection->getShortName()).'/'.$slug;
		$slug = trim($slug, '/');
		
		$this->controller[] = $controller;
		
		$this->app->router->controller($slug, $controller);
	}
	
	function getDir() {
		$reflector = new ReflectionClass(get_class($this));
		
		return dirname($reflector->getFileName());
	}
	
	function add_controllers($directory) {
		$files = \G2Design\Utils\Functions::directoryToArray($directory, false);
		
		foreach($files as $file) {
			//strip the module directory from the filepath
			$file = str_replace('src', '', str_replace($this->getDir(), '', $file));
			$file = trim($file, '\\ ,\/');
			$file = str_replace(".". \G2Design\Utils\Functions::get_extension($file), '', $file);
			//Convert filepath to class name
			$parts = explode("/", $file);
			
			$classname = "\\";
			foreach($parts as &$part) $part = ucfirst ($part);
			
			$classname .= implode('\\', $parts);
		
			
			if(class_exists($classname)) {
				$slug = current(array_reverse($parts));
				
				$slug = strtolower($slug);
				if($slug == 'index') {
					$slug = '';
				}
				
				$this->addController($slug,$classname);
			}
			
		}
	}
	
	
	/**
	 * Register a cronjob
	 * 
	 * @param \G2Design\G2App\Cron $cron
	 */
	function add_cron(\G2Design\G2App\Cron $cron) {
		$this->registered_crons[] = $cron;
	}
	
	function get_crons() {
		return $this->registered_crons;
	}
}
