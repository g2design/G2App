<?php
namespace G2Design;
use Phroute\Phroute\RouteCollector;

class G2App extends ClassStructs\Singleton {
	
	/**
	 *
	 * @var RouteCollector
	 */
	var $router = null, $modules = [];
	
	protected function __construct() {
		$this->router = new RouteCollector();
	}

	function start() {
		
		$dispatcher = new \Phroute\Phroute\Dispatcher(self::getInstance()->router->getData());
		$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], Request::route());
		echo $response;
	}
	
	function add_route($slug, callable $function) {
		self::getInstance()->router->any($slug, $function);
	}
	
	function add_module(Module $module) {
		$module->connect($this);
		
		$module->init();
		
		$this->modules[] = $module;
	}
	
	
}