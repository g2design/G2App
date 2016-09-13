<?php

namespace G2Design\G2App;

class Base {

	function get_module_dir($module = false) {

		$t = debug_backtrace();
		$file_uri = $t[0]['file'];
		foreach ($t as $row) {
			$file = $row['file'] . '<br>';
			if (strpos($file, \G2Design\G2App::get_module_dir($file)) !== false) {
				$file_uri = $file;
				break;
			}
		}

		$path = str_replace(\G2Design\G2App::get_module_dir($file_uri), '', $file_uri);
		$paths_arr = explode(DIRECTORY_SEPARATOR, $path);
		list($junk, $package) = $paths_arr;
		return \G2Design\G2App::get_module_dir($file_uri).'/';

		
	}

}
