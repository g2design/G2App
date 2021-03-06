<?php

namespace G2Design\Utils;
use Exception;

class Functions {

	static function endsWith($haystack, $needle) {
		$length = strlen($needle);
		$start = $length * -1; //negative
		return (substr($haystack, $start) === $needle);
	}

	/**
	 * Check if a string starts with a specific string
	 *
	 * @param type $haystack
	 * @param type $needle
	 * @return type
	 */
	static function startsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	static function get_current_site_url() {
		/* Get the current Directory */
		$cur_dir = explode(DIRECTORY_SEPARATOR, getcwd());
		/* Determine if this is an action or not */
		$cur_dir = array_reverse($cur_dir);
		$base = array_shift($cur_dir);

		$action = explode($base . '/', $_SERVER['REQUEST_URI']);
		$action_2 = array_shift($action);
		//var_dump('http://'.$_SERVER['HTTP_HOST'].$action.$base.'/');exit;


		/* Check if the site installation is dropped in the document root.
		  if so the base url should be equal to the domain address else the base url
		  should point to the folder in which the web installation reside

		 */
		(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? $http = 'https://' : $http = 'http://';

		$final_url = $http . $_SERVER['HTTP_HOST'] . $action_2 . $base . '/';

		if ($_SERVER['DOCUMENT_ROOT'] == str_replace('\\', '/', getcwd())) {
			$final_url = $http . $_SERVER['HTTP_HOST'];
		}

		$new_url[] = $http . $_SERVER['HTTP_HOST'];
		$new_url[] = \G2Design\Request::folder();
		$final_url = implode('/', $new_url);

		if (defined('OVERWRITE_SITEURL')) {
			return OVERWRITE_SITEURL;
		}

		if (!self::endsWith($final_url, '/')) {
			$final_url = $final_url . '/';
		}

		//Additional Check to see if .htaccess file is enabled
		if (!file_exists('.htaccess')) {
			$final_url .= 'index.php?';
		}
		return $final_url;
	}

	static function directoryToArray($directory, $recursive) {
		$array_items = array();
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if (is_dir($directory . "/" . $file)) {
						if ($recursive) {
							$array_items = array_merge($array_items, self::directoryToArray($directory . "/" . $file, $recursive));
						}
						$file = $directory . "/" . $file;
						$array_items[] = preg_replace("/\/\//si", "/", $file);
					} else {
						$file = $directory . "/" . $file;
						$array_items[] = preg_replace("/\/\//si", "/", $file);
					}
				}
			}
			closedir($handle);
		}
		return $array_items;
	}

	static function compress($code) {
		$code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code);
		$code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $code);
		$code = str_replace('{ ', '{', $code);
		$code = str_replace(' }', '}', $code);
		$code = str_replace('; ', ';', $code);

		return $code;
	}

	static function GET_to_string($arr = false) {
		if ($arr === false) {
			$arr = $_GET;
		}
		$string = [];
		foreach ($arr as $key => $value) {
			$key = urlencode($key);
			$value = urlencode($value);
			$string[] = "$key=$value";
		}
		return implode('&', $string);
	}

	static function curPageURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	static function get_extension($file) {
		$str = $file;
		$i = strrpos($str, ".");
		if (!$i) {
			return "";
		}
		$l = strlen($str) - $i;
		$ext = substr($str, $i + 1, $l);
		return $ext;
	}

	/**
	 * Determince location of connection user
	 *
	 * @return type
	 */
	static function get_location() {
		if (!isset($_SESSION['framework_location'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
			$details = file_get_contents("http://ipinfo.io/{$ip}/json");
			$_SESSION['framework_location'] = $details;
		} else {
			$details = $_SESSION['framework_location'];
		}

		return json_decode($details);
	}

	static function createDateRangeArray($strDateFrom, $strDateTo) {
		// takes two dates formatted as YYYY-MM-DD and creates an
		// inclusive array of the dates between the from and to dates.
		// could test validity of dates here but I'm already doing
		// that in the main script

		$aryRange = array();

		$iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
		$iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

		if ($iDateTo >= $iDateFrom) {
			array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry

			while ($iDateFrom < $iDateTo) {
				$iDateFrom += 86400; // add 24 hours
				array_push($aryRange, date('Y-m-d', $iDateFrom));
			}
		}
		return $aryRange;
	}

	static function array_to_csv_download($array, $filename = "export.csv", $delimiter = ";") {
		// open raw memory as file so no temp files needed, you might run out of memory though
		$f = fopen('php://memory', 'w');
		// loop over the input array
		foreach ($array as $line) {
			// generate csv lines from the inner arrays
			fputcsv($f, $line, $delimiter);
		}
		// rewrind the "file" with the csv lines
		fseek($f, 0);
		// tell the browser it's going to be a csv file
		header('Content-Type: application/csv');
		// tell the browser we want to save it instead of displaying it
		header('Content-Disposition: attachement; filename="' . $filename . '";');
		// make php send the generated csv lines to the browser
		fpassthru($f);
	}

	static function download($filename) {
		if(!file_exists($filename)) {
			throw new Exception("File `$filename` does not exist");
		}
		
		// Redirect output to a client’s web browser (Excel2007)
		$mime = self::mime($filename);
		header('Content-Type: ' . $mime);
		header('Content-Disposition: attachment;filename="' . basename($filename) . '"');
		header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		echo file_get_contents($filename);

//		$objWriter = \PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
//		$objWriter->save('php://output');
		exit;
	}

	static function mime($file_location) {
		$mimepath = '/usr/share/magic'; // may differ depending on your machine
		// try /usr/share/file/magic if it doesn't work
		$mime = finfo_open(FILEINFO_MIME);

		if ($mime === FALSE) {
			throw new Exception('Unable to open finfo');
		}
		$filetype = finfo_file($mime, $file_location);
		finfo_close($mime);
		if ($filetype === FALSE) {
			throw new Exception('Unable to recognise filetype');
		}

		return $filetype;
	}

	/**
	 * Creates an urlsave version of a string
	 * 
	 * @param type $text
	 * @return string
	 */
	static public function slugify($text) {
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}

	static function sanitize_from_word($content) {
		// Convert microsoft special characters
		$replace = array(
			"" => "'",
			"" => "'",
			"" => '"',
			"" => '"',
			"" => "-",
			"" => "-",
			"" => "&#8230;"
		);

		foreach ($replace as $k => $v) {
			$content = str_replace($k, $v, $content);
		}

		// Remove any non-ascii character
		$content = preg_replace('/[^\x20-\x7E]*/', '', $content);

		return $content;
	}

}
