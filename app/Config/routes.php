<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */



/**
 * [ Phorest ] : RESTful Mapping
 * 	該当のControllerに対するリクエストをHTTPメソッドにマッピング
 * 	※Webサービスとして公開するコントローラが増えた場合 Router::mapResources(コントローラ名)”を追加していく
 *
 */
	Router::mapResources('datphotos');
	Router::mapResources('datalbums');
	Router::mapResources('datalbumphotorelations');


/**
 * [ Phorest ] : Parser
 * 	AcceptHeaderが'aplication/json'の場合、JsonViewに切り替える
 */
	Router::parseExtensions('json');


/**
 * [ Phorest ] : Routing
 */
	//「 tempalbum 」コントローラーが呼び出しされたら「 datphotos 」コントローラーにすり替え
// 	Router::connect('/tempalbum/*', array('controller' => 'datphotos'));
	//「 uploads 」コントローラーが呼び出しされたら「 datphotos 」コントローラーにすり替え
	Router::connect('/uploads/*', array('controller' => 'datphotos', 'action' => 'add'));

// 	Router::connect('/:username/cp/*', array('controller' => 'DatUsers', 'action' => 'index'));
	Router::connect('/datalbums/userSearch/:username', array('controller' => 'datalbums', 'action' => 'userSearch'));
	Router::connect('/datalbums/previewSearch/:username', array('controller' => 'datalbums', 'action' => 'previewSearch'));

// 	Router::connect('/DashBoards/*', array('controller' => 'DashBoards', 'action' => 'index'));

	//「 DatUsers 」routhing
// 	Router::connect('/login', array('controller' => 'DatUsers', 'action' => 'login'));
	Router::connect('/', array('controller' => 'DatUsers', 'action' => 'login'));
	Router::connect('/DatUsers/logout', array('controller' => 'DatUsers', 'action' => 'logout'));
	Router::connect('/DatUsers/provision', array('controller' => 'DatUsers', 'action' => 'provision'));
	Router::connect('/sign_up/:step', array('controller' => 'DatUsers', 'action' => 'sign_up'));
	Router::connect('/code/:hash', array('controller' => 'DatUsers', 'action' => 'code'));
	Router::connect('/DatUsers/album/:albumName', array('controller' => 'DatUsers', 'action' => 'index'));
	Router::connect('/control-panel/', array('controller' => 'DatUsers', 'action' => 'index'));
	Router::connect('/control-panel/album/:albumName', array('controller' => 'DatUsers', 'action' => 'index'));
	Router::connect('/DatUsers/add', array('controller' => 'DatUsers', 'action' => 'add'));
	Router::connect('/DatUsers', array('controller' => 'DatUsers'));

	Router::connect('/datalbumphotorelations/undefined', array('controller' => 'datalbumphotorelations', 'action' => 'add'));

	Router::connect('/:username', array('controller' => 'DashBoards', 'action' => 'index'));
	Router::connect('/:username/albums/:albumname', array('controller' => 'FrontSites', 'action' => 'index'));
	Router::connect('/:username/preview/albums/:albumname', array('controller' => 'FrontSites', 'action' => 'preview'));

	// すべてのルーティングをパスした場合(デフォルトルーティング)
	Router::connect('/*', array('controller' => 'DatUsers', 'action' => 'login'));

	// requestパラメータに付与する  $this->request->username で取得
// 	Router::connect('/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
// 	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
// 	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes.  See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
