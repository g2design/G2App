<?php

namespace G2Design\FormBuilder\Field;

class Radio extends \G2Design\FormBuilder\Field {

	private $options;

	function render($return = true) {
		$field_string = $this->twig->render('fields/radio.twig', array_merge($this->args, ['options' => $this->options, 'this' => $this]));
		if ($return) {
			return $field_string;
		} else {
			echo $field_string;
		}
	}

	function add_option($label, $value = false) {
		if (!$value)
			$value = $label;
		$this->options[] = ['label' => $label, 'value' => $value];
		return $this;
	}

}
