<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $components = array(
			'Session',
			'Auth' => array(
					'loginRedirect'	=> array('controller' => 'DatUsers', 'action' => 'index'),
					'logoutRedirect' => array('controller' => 'DatUsers', 'action' => 'login'),
					'loginAction' => Array('controller' => 'DatUsers', 'action' => 'login'),
					'authError' => 'このページを表示するには、ログインを行ってください。',
					'authenticate' => array(
							'Form' => array(
									'userModel'	=> 'DatUser',
									'fields'	=> array(
											'username'	=> 'username',
											'password'	=> 'password',
									),
									'scope'		=> array('status' => '1'),
							),
					),
			),
	);

	function beforeFilter() {
		$this->Auth->allow('*');

		// usernameをURIから取得してViewに設置
		$meta_data = '';
		if (isset($this->request->pass[0])) {
			$meta_data = $this->request->pass[0];
// 			var_dump($this->request->ownername);
		}
		$this->set(compact('meta_data'));

// 		var_dump($this->params['prefix']);
// 		var_dump(Configure::read('Routing.prefixes'));

// 		//Auth Settings
// 		if (!empty($this->params['prefix']) && in_array($this->params['prefix'], Configure::read('Routing.prefixes'))) {

// 			$this->layout = $this->params['prefix'];

// 			//for Member
// 			if ($this->params['prefix'] == 'member') {
// 				$this->Auth->loginAction = 'member/users/login';
// 				$this->Auth->loginRedirect = 'member/users/index';
// 				$this->Auth->fields = array('username' => 'login_id', 'password' => 'login_pw');
// 				$this->Auth->autoRedirect = true;
// 				$this->Auth->loginError = "IDかパスワードが正しくありません";
// 				$this->Auth->authError = "Authentication Error";
// 			}
// 		}
	}
}
