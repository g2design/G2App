<?php

namespace G2Design;

use Phroute\Phroute\RouteCollector,
	Exception;

class G2App extends ClassStructs\Singleton {

	private static $instance = null;
	var $loader = null;
	private static $preregisterd = [];

	/**
	 *
	 * @var RouteCollector
	 */
	var $router = null, $modules = [];

	protected function __construct(\Composer\Autoload\ClassLoader $loader) {
		$this->router = new RouteCollector();

		$this->loader = $loader;
	}

	public static function getInstance() {
		if (!self::$instance) {
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
		$reflection = new \ReflectionClass(get_class($loader));
		define(G2_PROJECT_ROOT, dirname($reflection->getFileName()) . '../../');

		// Register Modules loaded with pre register function
		foreach (self::$preregisterd as $dir) {
			self::$instance->add_modules($dir);
		}

		return self::$instance;
	}

	/**
	 * Pre Registers directory for inclusion into module load
	 * @param type $dir
	 */
	public static function register_modules_dir($dir) {
		self::$preregisterd[] = $dir;
	}

	function start() {
		foreach ($this->modules as $mod)
			$mod->init();

		$dispatcher = new \Phroute\Phroute\Dispatcher(self::getInstance()->router->getData());
		try {
			$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], Request::route());
		} catch (\Phroute\Phroute\Exception\HttpRouteNotFoundException $ex) {
			http_response_code(404);
			echo "
			<h1>404 page does not exist</h1>
			";
		} catch (\Phroute\Phroute\Exception\HttpMethodNotAllowedException $ex) {
			http_response_code(405);
			echo "
			<h1>405 Method not allowed</h1>
			";
		}

		echo $response;
	}

	function add_route($slug, callable $function) {
		self::getInstance()->router->any($slug, $function);
	}

	function add_module(ClassStructs\Module $module) {
		$module->connect($this);

//		$module->init();

		$this->modules[] = $module;
	}

	function add_modules($directory) {
		$dirs = Utils\Functions::directoryToArray($directory, false);

		$classes = [];
		foreach ($dirs as $dir) {
			if (is_dir($dir)) {
				$files = Utils\Functions::directoryToArray($dir, false);
				foreach ($files as $file) {
					$ext = Utils\Functions::get_extension($file);

					if ($ext == 'php') { // Is a php file
						//Load the file
//						include $file;
						$class_name = str_replace(".$ext", '', basename($file));
						$this->loader->add("$class_name", dirname($file));

						$class = "\\$class_name";
						$classes[] = $class;
					}
				}
			}
		}

		foreach ($classes as $class) {
			if (class_exists($class) && is_subclass_of($class, '\G2Design\ClassStructs\Module', true)) {
				$module = new $class(); /* @var $module ClassStructs\Module */
				$this->add_module($module);
			}
		}
	}

	static function __module_instance($file) {
		$instance = self::getInstance();
		if ($instance) {
			foreach ($instance->modules as $module) { /* @var $module ClassStructs\Module */
				$reflection = new \ReflectionClass($module);
				$dir = dirname($reflection->getFileName());
				if (strpos($file, $dir) !== false) {
					return $module;
				}
			}
		} else {
			throw new Exception('Singleton Not instantiated');
		}
	}

	static function __module_dir($file) {
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

	/**
	 * Instead of using start. Run Actions
	 */
	function cron_run($runall = false) { //
		$jobby = new \Jobby\Jobby();

		foreach ($this->modules as $mod) {
			if (method_exists($mod, 'get_crons')) { /* @var $mod ClassStructs\Module */
				$crons = $mod->get_crons();

				foreach ($crons as $cron) { /* @var $cron \G2Design\G2App\Cron */
					$jobby->add($cron->name, array_merge([
						'closure' => $cron->run(),
						'output' => $cron->output ? $cron->output : 'logs/command.log',
						'schedule' => $cron->schedule,
						// You can turn off a job by setting 'enabled' to false
						'enabled' => true,
						'debug' => true
									], $cron->params));

					if ($runall) {
						$func = $cron->run();
						$func();
					}
				}
			}
		}

		$jobby->run();
	}

}
