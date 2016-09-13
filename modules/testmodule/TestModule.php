<?php

class TestModule extends \G2Design\ClassStructs\Module {
	
	public function init() {
		$this->add_controllers($this->getDir().'/TestModule/Controllers');
	}
	
}