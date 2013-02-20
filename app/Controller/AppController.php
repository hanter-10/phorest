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
// 			'loginRedirect'	=> array('controller' => 'DatUsers', 'action' => 'index'),
			'loginRedirect'		=> array('controller' => 'sign_up', 'action' => '4'),
			'logoutRedirect'	=> array('controller' => 'DatUsers', 'action' => 'login'),
			'loginAction'		=> Array('controller' => 'DatUsers', 'action' => 'login'),
			'authError'			=> 'このページを表示するには、ログインを行ってください。',
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

	// View埋め込みようメタデータ変数
	private $meta_data = '';

	function beforeFilter() {
		// すべてを許可する
// 		$this->Auth->allow('*');

		// usernameをURIから取得してViewに設置用にセット
		if (isset($this->request->pass[0])) {
			$this->meta_data = $this->request->pass[0];
// 			var_dump($this->request->ownername);
		}
		if (isset($this->request->username)) {
			$this->meta_data = $this->request->username;
		}
		$this->set(compact('meta_data'));
	}
}
