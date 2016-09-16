<?php

namespace G2Design\G2App\View;

//use Tale\Jade;

class Jade extends \G2Design\G2App\View {

	var $params = [], $jade, $template, $dir;

	public function __construct($template, $other_module = false) {
		$this->template = $template;
		$defaults = [
			'cache' => getcwd() . '/cache/twig'
		];



		//Merge config is declared
		if (( $conf = \G2Design\Config::get()->jade)) {
			//Convert to array;
			$conf = (array) $conf;
			$conf = array_merge($defaults, $conf);
		} else
			$conf = $defaults;

		// Create cache dir if not exist
		if (!is_dir(dirname($conf['cache'])))
			mkdir(dirname($conf['cache']), 0777, true);

		$this->dir = $this->get_module_dir($other_module).'Views';
		
		$this->jade = new \Tale\Jade\Renderer([
			'paths' => [ $this->dir ]
		]);
	}

	function render($return = false) {
		$content = $this->jade->render($this->template, $this->params);
		if ($return) {
			return $content;
		}

		echo $content;
	}

	public function __set($name, $value) {
		$this->params[$name] = $value;
	}

	public function __get($var) {
		return $this->params[$var];
	}

}