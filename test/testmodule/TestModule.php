<?php

namespace TestModule;

class TestModule extends \G2Design\ClassStructs\Module {
	
	public function init() {
		global $loader;
		$loader->add("TestModule",__DIR__.'/src');
		
		$this->add_controllers($this->getDir().'/src/TestModule/Controllers');
	}
	
}