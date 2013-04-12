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
		$this->Auth->allow( 'login', 'logout', 'add', 'sign_up', 'code', 'provision' );
	}

	// 初期登録時のアルバム数
	private $default_album = 3;

	public function login() {

		$this->layout = 'home_layout';

		if ( $this->request->is('post') ) {

			// ユーザー名に「＠」を使用できないという前提でusernameに「＠」を含んでいる場合はemailに置き換え
			if ( strstr( $this->request->data['DatUser']['username'], '@' ) ) {
				$this->request->data['DatUser']['email'] = $this->request->data['DatUser']['username'];
				$this->Auth->authenticate['Form']['fields']['username'] = 'email';

				unset( $this->DatUser->validate['username'] );
			}

			// データセット
			$this->DatUser->set( $this->request->data );
			// バリデーションチェック
			if ( $this->DatUser->validates() ) {
				if ( $this->Auth->login() ) {
					// CPへリダイレクト
					$this->Auth->redirect = '../control-panel/';
					$this->redirect( $this->Auth->redirect );
				}
				else {
					$this->set( 'error_message_login', 'IDまたはパスワードを確認してください' );
					// $this->Session->setFlash(__('ユーザ名またはパスワードが誤っています。再度入力してください。'));
				}
			}
		}
	}

	public function logout() {
		$this->layout = 'login';
		$this->redirect( $this->Auth->logout() );
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		// usernameを取得してViewに設置
		$meta_data = $this->Auth->user('username');
		$sitename = $this->Auth->user('sitename');
		$this->set( compact('meta_data', 'sitename') );

		$this->layout = 'user_layout';
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
			if ( $this->request->is( 'post' ) ) {

				// 対象sessionから値取得
				if ( $this->Session->check( 'TmpUser.temp_email' ) ) {

					$this->request->data['DatUser']['email'] = $this->Session->read('TmpUser.temp_email');
				}
				$this->request->data['DatUser']['status']	= STATUS_ON;		// デフォルト有効
				$this->request->data['DatUser']['create_datetime']	= date('Y-m-d h:i:s');
				$this->request->data['DatUser']['update_timestamp']	= date('Y-m-d h:i:s');

				// データセット
				$this->DatUser->set( $this->request->data );

				// バリデーションチェック
				if ( $this->DatUser->validates() ) {

					$this->DatUser->create();
					if ( $this->DatUser->save( $this->request->data ) ) {
						if ( $this->Auth->login() ) {

							// tempユーザーデータのステータスを認証済みとする
							$this->TmpUser->updateTmpUserStatus($this->request->data['DatUser']['email'], 1);

							// 登録時のデフォルトアルバムを登録する
							for ( $i = 1; $i <= $this->default_album; $i++ ) {

								// データセット
								$this->DatAlbum->create();
								$this->DatAlbum->set( 'fk_user_id', $this->Auth->user('user_id') );
								$this->DatAlbum->set( 'albumName', "アルバム$i" );
								$this->DatAlbum->set( 'public', STATUS_OFF );
								$this->DatAlbum->set( 'status', STATUS_ON );
								$this->DatAlbum->set( 'create_datetime', date('Y-m-d h:i:s') );
								$this->DatAlbum->set( 'update_timestamp', date('Y-m-d h:i:s') );

								// 追加
								$this->DatAlbum->save();
							}

							// 対象session削除
							$this->Session->delete('TmpUser');

							// CPへリダイレクト
							$this->redirect( $this->Auth->redirect() );
						}
					} else {
						$this->redirect( $this->Auth->logout() );
					}
				}
				$this->set( 'step', 3 );
				$this->set( 'meta_data', $this->request->data['DatUser']['email'] );
				$this->set( 'email', $this->request->data['DatUser']['email'] );
				$this->render( 'sign_up', 'sign_up_layout' );
			}
		} catch (Exception $e) {
			// username重複エラー対策
			$this->set( 'step', 3 );
			$this->set( 'meta_data', $this->request->data['DatUser']['email'] );
			$this->set( 'email', $this->request->data['DatUser']['email'] );
			$this->set( 'error_message', '該当のユーザーIDは使用済みです' );
			$this->render( 'sign_up', 'sign_up_layout' );
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
		$tmpUser = $this->TmpUser->getTmpUserDataByHash( $hash, 0, $fromDate );

		if ( $tmpUser ) {

			// 格納
			$this->Session->write('TmpUser.temp_email', $tmpUser[0]['tmp_users']['temp_email']);
			// メール認証後、ユーザー情報入力画面へ
			$this->redirect('/sign_up/3');

		}
		else {
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

				if ( $datUser > 0 ) {
					// 既存データに登録済みのメールアドレスの場合
					$this->set( 'error_message', '該当のメールアドレスは既に使用済みです' );
					$this->render('login', 'home_layout');
				}
				else {

					// データセット
					$this->TmpUser->set( $this->request->data );
					// バリデーションチェック
					if ( $this->TmpUser->validates() ) {

						$this->TmpUser->create();
						if ( $this->TmpUser->save( $this->request->data ) ) {

							// 通知URL作成
							$send_url = Router::url("/code/$hash_string", true);
							$data = array(
									'url' => $send_url,
									);
							// メール送信処理
							$email = new CakeEmail( 'default' );
							$email->to( $temp_email );
							$email->subject( 'メールアドレス確認通知' );
							$email->template( 'sign_up_notification_to_user' );
							$email->viewVars( $data );
							$email->send();

							// 格納
							$this->Session->write('TmpUser.temp_email', $temp_email);
							// メール送信しました画面へ
							$this->redirect('/sign_up/2');
							exit;

						} else {
							$this->redirect($this->Auth->logout());
						}
					}
				}
			}

			$this->render('login', 'home_layout');

		} catch (Exception $e) {
			$this->redirect($this->Auth->logout());
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($username = null) {

		$this->viewClass = 'Json';
		$this->components = array( 'RequestHandler' );

		// 返り値のデフォルトセット：false
		$this->set( 'datUser', array( 'errorMsg' => '更新に失敗しました。画面を更新して再度お試しください' ) );

		// usernameでデータ取得
		$datuser = $this->DatUser->getUserDataByUserName( $this->request->username );

		$this->DatUser->id = $datuser['DatUser']['id'];
		if (!$this->DatUser->exists()) {
			throw new NotFoundException(__('Invalid dat user'));
		}

		if ( $this->request->is('post') || $this->request->is('put') || $this->request->is('patch') ) {

			// リクエストデータをJSON形式にエンコードで取得する
// 			$requestData = $this->request->input( 'json_decode' );
			$request_array = split( '=', $this->request->input() );

			// データセット
			$this->DatUser->create( false );
			$this->DatUser->set( 'user_id', $datuser['DatUser']['id'] );
			if ( isset ( $request_array[1] ) ) $this->DatUser->set( 'sitename', urldecode( $request_array[1] ) );
			if ( isset ( $request_array[1] ) ) $this->DatUser->set( 'update_timestamp', date('Y-m-d h:i:s') );

			unset( $this->DatUser->validate['username'] );
			unset( $this->DatUser->validate['password'] );

			if ( $this->DatUser->validates() ) {
				// 更新処理
				$this->DatUser->save();
				$this->set( 'datUser', true );
			}
			else {
				if ( isset( $this->DatUser->validationErrors['sitename'][0] ) ) {
					$this->set( 'datUser', array( 'errorMsg' => $this->DatUser->validationErrors['sitename'][0] ) );
				}
			}
		}
		$this->set('_serialize', 'datUser');
	}

	public function resend_password() {

		$this->layout = 'sign_up_layout';
	}

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
