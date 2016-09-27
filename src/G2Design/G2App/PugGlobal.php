<?php

namespace G2Design\G2App;

class PugGlobal extends \Pug\Pug {

	/**
	 * 
	 * @staticvar type $instance
	 * @return PugGlobal
	 */
	public static function getInstance() {
		static $instance = null;
		if (null === $instance) {
			$instance = new static();
		}
		$params = [
			'SITEURL' => \G2Design\Utils\Functions::get_current_site_url()
		];

		$instance->share($params);
		return $instance;
	}

}
