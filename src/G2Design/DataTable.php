<?php

namespace G2Design;

use Twig_Environment,
	Twig_Loader_Filesystem,
	G2Design\Utils\Functions,
	G2Design\Database,
	Exception;

/**
 * Class improves fields management of table renderer
 *
 * To use class features use the function set_fields
 */
class DataTable extends Table {

	var $fields, $tb_functions = [], $classes = '';

	/**
	 * Sets the fields in the correct form
	 * $fields variable definition
	 * array of object containing a label and name key.
	 * name is equal to the name of the field
	 * and label is the label connected to that field
	 *
	 * @param type $fields
	 */
	function set_fields($fields) {
		$this->fields = $fields;
	}

	/**
	 * append another field
	 * 
	 * @param type $field The field or an array
	 * @param type $label The Label
	 * @return type
	 */
	function add_field($field , $label = false) {
		
		$this->fields = $this->fields != null ? $this->fields : [];
		
		if(is_string($field)) {
			$field = ['name' => $field, 'label' => $label ? $label : $field];
		}
		
		array_push($this->fields, $field);
		
	}

	function default_fields() {
		$data = $this->get_resultset();
		$final = array();
		if (empty($data)) {
			return $final;
		}
		$fields = current($data);
		if ($fields) {
			foreach ($fields as $key => $value) {
				$final[] = array('label' => $key, 'name' => $key);
			}
		}
		return $final;
	}

//	public function get_resultset() {
//		return $result = parent::get_resultset();
//		// Reformat resultset to fit to fields selected
//
//		if (!empty($this->fields)) {
//			$fields = $this->get_table_fields();
//			$new_results = [];
//
//			foreach ($result as $set) {
//				$n_set = [];
//				if (is_array($set)) {
//					$set = $this->array_to_object($set);
//				}
//				foreach ($fields as $field) {
//					$n_set[$field] = $set->$field;
//				}
////				$n_set = $this->array_to_object($n_set);
//				$new_results[] = $n_set;
//			}
////			var_dump($new_results);
//			return $new_results;
//		} else
//			return $result;
//	}

	function get_headers() {
		if (empty($this->fields)) {
			return parent::get_headers();
		} else {
			return $this->get_table_fields();
		}
	}

	function get_table_fields() {

		$fields = [];
		foreach ($this->fields as $field) {
			if (is_array($field)) { // Convert array to object
				$field = $this->array_to_object($field);
			}

			if (isset($field->name)) {
				$fields[] = $field->name;
			} else {
				throw new Exception('Expect there to be keys name and label in given array');
			}
		}

		return $fields;
	}

	function render() {
		if (empty($this->fields)) {
			parent::render();
		} else {
			$twig_cache = getcwd() . '/cache/tables/';
			$twig_folder = __DIR__ . '/DataTable';

			$params = array(
				'cache' => $twig_cache,
				'auto_reload' => true,
				'autoescape' => false,
//			'debug' => true
			);
			$loader = new Twig_Loader_Filesystem($twig_folder);
			$twig = new Twig_Environment($loader, $params);
//		var_dump($twig_folder);exit;
			$current_url = substr(Functions::curPageURL(), 0, strpos(Functions::curPageURL(), '?') !== false ? strpos(Functions::curPageURL(), '?') : strlen(Functions::curPageURL()));
			$copy = $_GET;
			unset($copy['p']);
			if (empty($copy)) {
				$get_con = '?';
			} else {
				$current_url .= '?' . Functions::GET_to_string($copy);
				$get_con = '&';
			}
//			echo '<pre>';
//			var_dump($this->get_resultset());
//			echo '</pre>';
//			exit;
			return $twig->render('improved-table.twig', [
						'data' => $this->get_resultset(),
						'pages' => Database::get_last_total_pages() != false ? Database::get_last_total_pages() : $this->total_pages,
						'current' => Database::get_current_page() != false ? Database::get_current_page() : $this->current_page,
						'headers' => $this->get_headers(),
						'functions' => $this->get_functions(),
						'instance' => $this,
						'get_con' => $get_con,
						'current_url' => $current_url,
						'table_functions' => $this->tb_functions(),
						'classes' => $this->classes
			]);
		}
	}
	
	function add_table_function($label, $closure) {
		//Generate a link for this function
		$key = 'table-function-'.md5($label);
		$this->tb_functions[$key] = ['label' => $label, 'key' => $key];
		
		if(isset($_GET[$key])) {
			$closure($this, $this->get_full_resultset());
		}
	}
	
	function tb_functions(){
		return $this->tb_functions;
	}

	function get_label($fieldname) {

		foreach ($this->fields as $field) {
			if (is_array($field)) { // Convert array to object
				$field = $this->array_to_object($field);
			}

			if (isset($field->label)) {
				if ($field->name == $fieldname) {
					return $field->label;
				}
			} else {
				throw new Exception('Expect there to be keys name and label in given array');
			}
		}
	}

}
