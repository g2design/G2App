<?php

namespace G2Design\FormBuilder;
use Twig_Environment;


class Field implements Field\FieldInterface {

	var $name, $classes, $label;
	protected $value;
	protected $args;
	protected $atrs = [];
	public $required = false;
	protected $type = 'text';

	/**
	 * The twig Env to use to render the outputs
	 * @var Twig_Environment
	 */
	protected $twig;

	function __construct($fieldname, $classes, $options = []) {
		$this->name = $fieldname;
		$this->classes = $classes;
		$this->label = $fieldname;

		$this->args = [
			'name' => &$this->name,
			'classes' => &$this->classes,
			'value' => &$this->value,
			'label' => &$this->label,
		];

		$this->args = array_merge($this->args, $options);
	}

	/**
	 * Render this form field
	 *
	 * @param type $return
	 * @return type
	 */
	function render($return = true) {

		$field_string = $this->twig->render('fields/text.twig', array_merge($this->args, ['type' => $this->type], ['this' => $this]));
		if ($return) {
			return $field_string;
		} else {
			echo $field_string;
		}
	}

	function set_enviroment(Twig_Environment $twig) {
		$this->twig = $twig;
	}

	function set_value($value) {
		$this->value = $value;
		return $this;
	}

	function set_type($type) {
		$this->type = $type;
		return $this;
	}

	function invalidate($message) {
		$this->error = $message;
	}

	function set_attributes($atr = []) {
		$this->atrs = $atr;
		return $this;
	}

	function set_label($label) {
		$this->label = $label;
		return $this;
	}

	function set_required($required = true) {
		$this->required = $required;
		return $this;
	}

	function render_attrs() {
		$atr_string = '';
		foreach ($this->atrs as $key => $value) {
			$atr_string .= "$key=\"$value\"";
		}

		return $atr_string;
	}

}
