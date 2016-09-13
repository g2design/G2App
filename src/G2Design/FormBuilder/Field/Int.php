<?php

namespace G2Design\FormBuilder\Field;
use Twig_Environment;

interface Int {
	
	function __construct($fieldname, $classes);
	function render($return = true);
	function set_enviroment(Twig_Environment $twig);
}

