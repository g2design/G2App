<?php
namespace G2Design;
use Phroute\Phroute\RouteCollector;

class G2App extends ClassStructs\Singleton {
	private static $instance = null;
	
	var $loader = null;
	/**
	 *
	 * @var RouteCollector
	 */
	var $router = null, $modules = [];
	
	protected function __construct(\Composer\Autoload\ClassLoader $loader ) {
		$this->router = new RouteCollector();
		
		$this->loader = $loader;
	}
	
	public static function getInstance() {
		if(!self::$instance) {
			throw new Exception("App instance not initiated. Please call init() function");
		}
		return self::$instance; 
	}
	
	/**
	 * 
	 * @param \Composer\Autoload\ClassLoader $loader
	 * @return G2App
	 */
	public static function init(\Composer\Autoload\ClassLoader $loader) {
		self::$instance = new self($loader);
		
		return self::$instance;
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
//						include $file;
						
						$class_name = str_replace(".$ext",'',basename($file));
						$this->loader->add("$class_name",  dirname($file));
						
						$class = "\\$class_name";
						if(class_exists($class)) {
							$module = new $class();/* @var $module ClassStructs\Module */
							$this->add_module($module);
						}
					}
				}
			}
		}
	}
	
	static function get_module_dir($file) {
		$instance = self::getInstance();
		if ($instance) {
				foreach ($instance->modules as $module) { /* @var $module ClassStructs\Module */
					$reflection = new \ReflectionClass($module);
					$dir = dirname($reflection->getFileName());
					if (strpos($file, $dir) !== false) {
						return $dir;
					}
				}
			} else {
				throw new Exception('Singleton Not instantiated');
			}
	}
	
	function defaultController($controller) {
		$this->router->controller('/', $controller);
	}
	
	
}