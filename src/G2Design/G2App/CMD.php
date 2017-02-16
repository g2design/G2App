<?php

namespace G2Design\G2App;

class CMD {
	protected $options = [];
	function &set_options($options){
		$this->options = array_merge($this->options, $options);
		return $this;
	}
}