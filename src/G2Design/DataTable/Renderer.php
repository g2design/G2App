<?php
namespace G2Design\DataTable;


class Renderer {

	var $field = null, $wrap;
	private $thousands;
	private $decimals;
	private $currency;
	private $function = null;

	public function __construct($field) {
		$this->field = $field;
	}

	function field() {
		return $this->field;
	}

	function render($fieldname, $value, $row_data = []) {
		if(!empty($this->function)){
			$function = $this->function;
			$value = $function($fieldname,$value,$row_data);
		}

		if (!empty($this->wrap)) {
			$value = $this->render_wrap($value, $this->wrap);
		}

		if(!empty($this->currency)){
			$value = $this->render_currency($value);
		}


		return $value;
	}
	/**
	 * Use a function as a callback for change output.
	 * Fields passed to callback
	 * $fieldname,$value,$row_data
	 *
	 * @param type $function
	 */
	function set_function($function){
		$this->function = $function;
	}

	/**
	 * Add [value] inside your string to put the value in that specific position
	 * @param type $wrap
	 */
	function wrap($wrap) {
		$this->wrap = $wrap;
	}

	function set_currency($symbol, $decimals, $thousands) {
		$this->currency = $symbol;
		$this->decimals = $decimals;
		$this->thousands = $thousands;
	}

	function render_currency($value){
		return "$this->currency".number_format($value, $this->decimals, '.', $this->thousands);
	}

	function render_wrap($value, $wrap) {
		return str_replace('[value]', $value, $wrap);
	}

}
