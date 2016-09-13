<?php

namespace TestModule\Controllers;

use R , \G2Design\G2App;

class Index extends \G2Design\G2App\Controller {
	function anyIndex() {
		return " The index Controller!";
	}
	
	function anyTest(){
		return " The Test Controller!";
	}
	
	function anyForm() {
		
		$form = new \G2Design\FormBuilder();
		
		$form->add_field($form->create_field('Test'));
		
		
		$form->render();
	}
	
	function anyRegister($id = false) {
		if (!empty($id)) {
			$winery = R::load('winery', $id);
		} else
			$winery = R::dispense('winery');

		$view = new G2App\View('pages/register');
		$view->w = new \TestModule\Model\Winery();

		if (( $view->form = $view->w->register($winery) ) === true) {

			$contact = current($winery->sharedContact);
			$view->w->login_forced($contact);

			$this->redirect('dashboard');
			return;
		}
		

		$view->render();
	}
	
	
	
	// +++++++++++++++++++++++++++++++ OLD CODE
	
	var $theme = null, $params = [];

	function __before() {
		parent::__before();
		\G2_User::init();
		$theme_location = 'themes/default';
		//Load the front end instance loader
		$theme = Theme_Loader::get_instance();
		$theme->set_theme($theme_location);

		$this->theme = $theme;
		ob_start();

		$wm = $this->loadModel('winery_model'); /* @var $wm \Winery_Model */

		if ($wm->get_current_login()) {
			$this->params['contact'] = $wm->get_current_login();
		}
	}

	public function __after() {
		parent::__after();


		$content = ob_get_clean();
		echo $this->theme->render_string($content, [
			'params' => $this->params
		]);
	}

	function index() {

		$wm = $this->loadModel('winery_model'); /* @var $wm \Winery_Model */

		if (!$wm->get_current_login()) {
			$this->redirect('login');
		}

		$this->redirect('dashboard');
	}

	function dashboard() {

		$wm = $this->loadModel('winery_model'); /* @var $wm \Winery_Model */

		if (!$wm->get_current_login()) {
			$this->redirect('login');
		}

		//Load wines connected to this contact winery
		// Load notes ment for 

		echo 'The Dashboard';
	}

	function login_test_winery() {
		$winery = R::findOne('winery');

		$contact = current($winery->sharedContact);
		$wm = $this->loadModel('winery_model'); /* @var $wm \Winery_Model */

		$wm->login_forced($contact);

		$this->redirect('dashboard');
	}

	function anyLogin() {

		$view = new G2App\View('pages/login');
		$view->w = new \TestModule\Model\Winery(); /* @var $view->w Winery_Model */

		if (( $view->form = $view->w->login() ) === true) {
			$this->redirect('dashboard');
		}

		$view->render();
	}

	function register($args) {
		$id = array_shift($args);

		if (!empty($id)) {
			$winery = R::load('winery', $id);
		} else
			$winery = R::dispense('winery');

		$view = new \G2_TwigView('pages/register');
		$view->w = $this->loadModel('winery_model'); /* @var $w Winery_Model */

		if (( $view->form = $view->w->register($winery) ) === true) {

			$contact = current($winery->sharedContact);
			$view->w->login_forced($contact);

			$this->redirect('dashboard');
			return;
		}

		$view->render();
	}
	
	function logout() {
		
		session_destroy();
		
		$this->redirect('');
	}
}

