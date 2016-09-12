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
	
	function add_module(ClassStructs\Module $module) {
		$module->connect($this);
		
		$module->init();
		
		$this->modules[] = $module;
	}
	
	function add_modules($directory) {
		$dirs = Utils\Functions::directoryToArray($directory, false);
		
		foreach($dirs as $dir) {
			if(is_dir($dir)) {
				$files = Utils\Functions::directoryToArray($dir, false);
				
				foreach($files as $file) {
					$ext = Utils\Functions::get_extension($file);
					
					if($ext == 'php') { // Is a php file
						//Load the file
						include $file;
						$class_name = str_replace(".$ext",'',basename($file));
						$class = "\\$class_name\\$class_name";
						if(class_exists($class)) {
							$module = new $class();/* @var $module ClassStructs\Module */
							$this->add_module($module);
						}
					}
				}
			}
		}
	}
	
	function defaultController($controller) {
		$this->router->controller('/', $controller);
	}
	
	
}