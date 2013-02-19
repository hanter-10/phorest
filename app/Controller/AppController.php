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

	function beforeFilter() {
		// すべてを許可する
// 		$this->Auth->allow('*');

		// 環境によって読み込むファイルパス設定
		$connection = Configure::read('envronment');
		if (!empty($connection)) {

			if ($connection === 'production') {
				// 本番環境
				define('DASHBORD_DS_INDEX_JS', 'dashboard/index.js');
				define('FRONTSITE_DS_INDEX_JS', 'frontsite/index.js');
				define('MANAGEMENT_DS_APP_JS', 'management_center/app.js');
				define('MANAGEMENT_DS_MVC_DS_MODEL_JS', 'management_center/MVC/model.js');
				define('MANAGEMENT_DS_MVC_DS_VIEW_JS', 'management_center/MVC/view.js');
				define('MANAGEMENT_DS_MVC_DS_ROUTER_JS', 'management_center/MVC/router.js');
			}
			else {
				define('DASHBORD_DS_INDEX_JS', 'dashboard/_dev_index.js');
				define('FRONTSITE_DS_INDEX_JS', 'frontsite/_dev_index.js');
				define('MANAGEMENT_DS_APP_JS', 'management_center/_dev_app.js');
				define('MANAGEMENT_DS_MVC_DS_MODEL_JS', 'management_center/MVC/_dev_model.js');
				define('MANAGEMENT_DS_MVC_DS_VIEW_JS', 'management_center/MVC/_dev_view.js');
				define('MANAGEMENT_DS_MVC_DS_ROUTER_JS', 'management_center/MVC/_dev_router.js');
			}
		}

		// usernameをURIから取得してViewに設置用にセット
		$meta_data = '';
		if (isset($this->request->pass[0])) {
			$meta_data = $this->request->pass[0];
// 			var_dump($this->request->ownername);
		}
		if (isset($this->request->username)) {
			$meta_data = $this->request->username;
		}
		$this->set(compact('meta_data'));
	}
}
