<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');


/**
 * DatUsers Controller
 *
 * @property DatUser $DatUser
 */
class DatUsersController extends AppController {

	public $uses = array('DatUser', 'DatAlbum', 'TmpUser');

	// ログインなしでアクセス可能なページ
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'logout', 'add', 'sign_up', 'code', 'provision');
	}

	// 初期登録時のアルバム数
	private $default_album = 3;

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
				$this->Auth->redirect = '../control-panel/';
				$this->redirect($this->Auth->redirect);
// 				$this->redirect($this->Auth->redirect());
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

				// 対象sessionから値取得
				if ($this->Session->check('TmpUser.temp_email')) {

					$this->request->data['DatUser']['email'] = $this->Session->read('TmpUser.temp_email');
				}
				$this->request->data['DatUser']['create_datetime'] = date('Y-m-d h:i:s');
				$this->request->data['DatUser']['update_timestamp'] = date('Y-m-d h:i:s');

				$this->DatUser->create();
				if ($this->DatUser->save($this->request->data)) {
					if ($this->Auth->login()) {

						// tempユーザーデータのステータスを認証済みとする
						$this->TmpUser->updateTmpUserStatus($this->request->data['DatUser']['email'], 1);

						// 初期登録デフォルトでアルバム3つ保持
						$datAlbum = array();
						$datAlbum['fk_user_id']					= $this->Auth->user('user_id');		// 会員ID:セッションより取得
						$datAlbum['flg']						= 0;								// デフォルトは非公開
						$datAlbum['status']						= 1;								// デフォルトは有効
						$datAlbum['create_datetime']			= date('Y-m-d h:i:s');
						$datAlbum['update_timestamp']			= date('Y-m-d h:i:s');

						// 登録時のデフォルトアルバムを登録する
						for ($i = 1; $i <= $this->default_album; $i++) {
							$datAlbum['albumName']	= 'アルバム' . $i;		// アルバム名

							$this->DatAlbum->create();
							$this->DatAlbum->save($datAlbum);
						}

						// 対象session削除
						$this->Session->delete('TmpUser');

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
			// username重複エラー対策
			$this->redirect('/sign_up/3');
// 			$this->redirect($this->Auth->logout());
		}
	}

	public function sign_up(){

		$this->layout = 'sign_up_layout';

		// sessionにtemp_emailがある場合はセット
		if ($this->Session->check('TmpUser.temp_email')) {

			$this->set('email', $this->Session->read('TmpUser.temp_email'));
// 			$this->Session->delete('TmpUser');
		}
		$this->set('step', $this->request->step);
	}


	public function code() {

		$this->layout = 'sign_up_layout';

		$hash = $this->request->hash;

		// 対象期間 7日間
		$fromDate 	= date('Y-m-d h:i:s' ,strtotime('-7 day'));

		// 該当Hash値で検索して一時Userデータを取得する
		$tmpUser = $this->TmpUser->getTmpUserDataByHash($hash, 0, $fromDate);

		if ($tmpUser) {

			// 格納
			$this->Session->write('TmpUser.temp_email', $tmpUser[0]['tmp_users']['temp_email']);

			// メール認証後、ユーザー情報入力画面へ
			$this->redirect('/sign_up/3');
		} else {

			// ログイン画面へ
			$this->redirect('/login');
		}
		exit;
	}

	public function provision() {
		try
		{
			if ($this->request->is('post')) {

				$temp_email = $this->request->data['TmpUser']['temp_email'];
				$email_key = $temp_email . '_' . date('Ymdhis');

				// データセット
				$this->request->data['TmpUser']['hash_string']		= Security::hash($email_key, 'sha256', Configure::read('Security.key'));
				$this->request->data['TmpUser']['create_datetime']	= date('Y-m-d h:i:s');

				$hash_string = $this->request->data['TmpUser']['hash_string'];

				// 既存ユーザーデータにすでにEmailが登録されていないかチェック
				$datUser = $this->DatUser->checkUserDataByEmail($temp_email, 1);

				if ($datUser[0][0]['cnt'] >= 1) {
					// 既存データに登録済みのメールアドレスの場合
					$this->redirect($this->Auth->logout());
				}

				$this->TmpUser->create();
				if ($this->TmpUser->save($this->request->data)) {

					// 通知URL作成
					$send_url = Router::url("/code/$hash_string", true);

					// メール送信処理
					$email = new CakeEmail( 'default' );
// 					$email->from(array('yashiro@XXX.com' => 'My Site'));
					$email->to( $temp_email );
					$email->subject( 'メールアドレス確認通知' );
					$email->send( $send_url );

					// メール送信しました画面へ
					$this->redirect('/sign_up/2');
					exit;

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

// /**
//  * edit method
//  *
//  * @throws NotFoundException
//  * @param string $id
//  * @return void
//  */
// 	public function edit($id = null) {
// 		$this->DatUser->id = $id;
// 		if (!$this->DatUser->exists()) {
// 			throw new NotFoundException(__('Invalid dat user'));
// 		}
// 		if ($this->request->is('post') || $this->request->is('put')) {
// 			if ($this->DatUser->save($this->request->data)) {
// 				$this->Session->setFlash(__('The dat user has been saved'));
// 				$this->redirect(array('action' => 'index'));
// 			} else {
// 				$this->Session->setFlash(__('The dat user could not be saved. Please, try again.'));
// 			}
// 		} else {
// 			$this->request->data = $this->DatUser->read(null, $id);
// 		}
// 	}

// /**
//  * delete method
//  *
//  * @throws MethodNotAllowedException
//  * @throws NotFoundException
//  * @param string $id
//  * @return void
//  */
// 	public function delete($id = null) {
// 		if (!$this->request->is('post')) {
// 			throw new MethodNotAllowedException();
// 		}
// 		$this->DatUser->id = $id;
// 		if (!$this->DatUser->exists()) {
// 			throw new NotFoundException(__('Invalid dat user'));
// 		}
// 		if ($this->DatUser->delete()) {
// 			$this->Session->setFlash(__('Dat user deleted'));
// 			$this->redirect(array('action' => 'index'));
// 		}
// 		$this->Session->setFlash(__('Dat user was not deleted'));
// 		$this->redirect(array('action' => 'index'));
// 	}
}
