<?php
App::uses('AppController', 'Controller');

/**
 * DatUsers Controller
 *
 * @property DatUser $DatUser
 */
class DatUsersController extends AppController {

	public $uses = array('DatUser', 'DatAlbum');

	// ログインなしでアクセス可能なページ
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login','logout','add');
	}

	public function login() {
		$this->layout = 'home_layout';
		if ($this->request->is('post')) {

			// ユーザー名に「＠」を使用できないという前提でusernameに「＠」を含んでいる場合はemailに置き換え
			if (strstr($this->request->data['DatUser']['username'], '@')) {
				$this->request->data['DatUser']['email']	= $this->request->data['DatUser']['username'];
// 				$this->Auth->fields['username']	= 'email';
// 				$this->Auth->fields = array(
// 						'username' => 'email',
// 				);
				$this->Auth->authenticate['Form']['fields']['username'] = 'email';
			}

			if ($this->Auth->login()) {

				// CPへリダイレクト
// 				$this->Auth->loginRedirect = $this->Auth->user('username') . '/cp';
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('ユーザ名またはパスワードが誤っています。再度入力してください。'));
			}
		}
	}

	public function logout() {
		$this->layout = 'login';
		$this->redirect($this->Auth->logout());
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		// usernameを取得してViewに設置
		$meta_data = $this->Auth->user('username');
		$this->set(compact('meta_data'));

		$this->layout = 'user_layout';

// 		var_dump($this->Auth->user('user_id'));
// 		$this->DatUser->recursive = 0;
// 		$this->set('datUsers', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->DatUser->id = $id;
		if (!$this->DatUser->exists()) {
			throw new NotFoundException(__('Invalid dat user'));
		}
		$this->set('datUser', $this->DatUser->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		try
		{
			if ($this->request->is('post')) {
				$this->DatUser->create();
				if ($this->DatUser->save($this->request->data)) {
					if ($this->Auth->login()) {

						// 初期登録デフォルトでアルバム3つ保持
						$datAlbum = array();
						$datAlbum['fk_user_id']					= $this->Auth->user('user_id');		// 会員ID:セッションより取得
						$datAlbum['flg']						= 0;								// デフォルトは非公開
						$datAlbum['status']						= 1;								// デフォルトは有効
						$datAlbum['create_datetime']			= date('Y-m-d h:i:s');
						$datAlbum['update_timestamp']			= date('Y-m-d h:i:s');

						/* insert query */
						for ($i = 1; $i <= 3; $i++) {
							$datAlbum['albumName']	= 'アルバム' . $i;		// アルバム名

							$this->DatAlbum->create();
							$this->DatAlbum->save($datAlbum);
						}

						// CPへリダイレクト
// 						$this->Auth->loginRedirect = $this->Auth->user('username') . '/cp';
						$this->redirect($this->Auth->redirect());
// 						$this->redirect(array('controller' => 'DatUsers', 'action' => 'index'));
					}
				} else {
					// TODO:バリデーションとかその辺ハンドリングしなきゃ
					$this->redirect($this->Auth->logout());
// 					$this->Session->setFlash(__('The dat user could not be saved. Please, try again.'));
				}
			}
		} catch (Exception $e) {
			// TODO:SQL ERRORとかその辺ハンドリングしなきゃ
			$this->redirect($this->Auth->logout());
		}
	}

	public function sign_up(){
		var_dump($this->request->data);
		$this->layout = 'sign_up_layout';
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->DatUser->id = $id;
		if (!$this->DatUser->exists()) {
			throw new NotFoundException(__('Invalid dat user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->DatUser->save($this->request->data)) {
				$this->Session->setFlash(__('The dat user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The dat user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->DatUser->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->DatUser->id = $id;
		if (!$this->DatUser->exists()) {
			throw new NotFoundException(__('Invalid dat user'));
		}
		if ($this->DatUser->delete()) {
			$this->Session->setFlash(__('Dat user deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Dat user was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
