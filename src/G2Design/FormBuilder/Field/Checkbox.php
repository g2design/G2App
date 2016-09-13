<?php

namespace G2Design\FormBuilder\Field;

class Checkbox extends G2Design\FormBuilder\Field {
	
	
	function render($return = true) {
		$field_string = $this->twig->render('fields/checkbox.twig', array_merge($this->args,['this' => $this]));
		if($return){
			return $field_string;
		} else {
			echo $field_string;
		}
	}
	
	
}