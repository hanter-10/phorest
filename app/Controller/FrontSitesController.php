<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class FrontSitesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'FrontSites';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public $layout = 'front_layout';

	public function beforeFilter() {
		// 親クラスをロード
		parent::beforeFilter();
		$this->Auth->allow('index');
	}

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 */
	public function index() {

		$this->layout = 'front_layout';

		$meta_data = $this->request->username;
		$this->set( compact( 'meta_data' ) );
	}

	public function preview() {

		$this->layout = 'front_layout';

		$meta_data = $this->request->username;

		// ログインユーザーしか見れない
		if ( $this->Auth->user( 'username' ) === $meta_data ) {
			$preview_mode = 'true';
			$this->set( compact( 'meta_data', 'preview_mode' ) );
		}
		else {
			$this->layout = 'home_layout';
			// postではない時は「400 Bad Request」
			//throw new BadRequestException(__('Bad Request.'));
		}
	}
}
