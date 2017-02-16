<?php
namespace G2Design\Utils;

use ZipArchive;

class Zipper {

	private $files = array(), $zip, $names = array();
	private $content = [];

	public function __construct() {
		$this->zip = new ZipArchive;
	}

	public function &addFile($file, $name = false) {
		if ($file) {
			$this->files[] = ['file' => $file, 'name' => $name];
		}
		return $this;
	}
	
	public function &add_content($file, $data) {
		$this->content[] = ['file' => $file, 'data' => $data];
		return $this;
	}

	public function store($location = null) {
		$data = array_combine($this->names, $this->files);
		if (count($this->files) && $location) {
			foreach ($this->files as $index => $item) {
				$file = $item['file'];
				if (!file_exists($file)) {
					unset($this->files[$index]);
				}
			}
		}
		if (!is_dir(dirname($location))) {
			mkdir(dirname($location), 0777, true);
		}
		if ($this->zip->open($location, file_exists($location) ? ZipArchive::OVERWRITE : ZipArchive::CREATE)) {
			foreach ($this->files as $key => $item) {
				$file = $item['file'];
				$name = $item['name'];
				$this->zip->addFile( $file, ( !empty($name) ? $name . '/': ''  ) . $file);
				
			}
			
			foreach($this->content as $item) {
				$this->zip->addFromString($item['file'], $item['data']);
			}
			
			$this->zip->close();

			return true;
		}
	}

}
