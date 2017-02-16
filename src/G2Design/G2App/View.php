<?php

namespace G2Design\G2App;

class View extends Base {

	var $params = [], $twig, $template;

	public function __construct($template, $other_module = false) {
		$this->template = $template;
		$defaults = [
			'cache' => getcwd() . '/cache/twig'
		];



		//Merge config is declared
		if (( $conf = \G2Design\Config::get()->twig)) {
			//Convert to array;
			$conf = (array) $conf;
			$conf = array_merge($defaults, $conf);
		} else
			$conf = $defaults;

		//Create cache dir if not exist
		if (!is_dir(dirname($conf['cache'])))
			mkdir(dirname($conf['cache']), 0777, true);

		//Load twig filesystem
		if (is_dir($this->__module_dir($other_module) . 'Views')) {
			$loader = new \Twig_Loader_Filesystem($this->__module_dir($other_module) . 'Views');

			$this->twig = new \Twig_Environment($loader, array(
				'cache' => $conf['cache'],
				'auto_reload' => true,
				'autoescape' => false,
//			'debug' => true
			));
		}
	}

	/**
	 * 
	 * @param type $template
	 * @param type $other_module
	 * @return \G2Design\G2App\View
	 */
	public static function getInstance($template, $other_module = false) {
		$class = get_called_class();
		return new $class($template, $other_module);
	}

	function render($return = false) {
		$content = $this->twig->render($this->template . ".twig", $this->params);
		if ($return) {
			return $content;
		}

		echo $content;
	}

	function render_string($string) {
		$tl = new \Twig_Loader_Array([
			'string' => $string
		]);

		$twig = new \Twig_Environment($tl);
		return $twig->render('string', $this->params);
	}

	public function __set($name, $value) {
		$this->params[$name] = $value;
	}

	public function __get($var) {
		return $this->params[$var];
	}

	public function set($name, $value) {
		$this->params[$name] = $value;
		return $this;
	}

}
