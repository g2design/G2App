<?php

namespace G2Design\FormBuilder\Field;

class Textarea extends \G2Design\FormBuilder\Field {

	public function render($return = true) {
		$field_string = $this->twig->render('fields/textarea.twig', array_merge($this->args, ['type' => $this->type], ['this' => $this]));
		if ($return) {
			return $field_string;
		} else {
			echo $field_string;
		}
	}

	public function set_required($required = true) {


		return parent::set_required($required);
	}

}
