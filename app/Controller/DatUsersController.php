<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');


/**
 * DatUsers Controller
 *
 * @property DatUser $DatUser
 */
class DatUsersController extends AppController {

	public $uses = array( 'DatUser', 'DatAlbum', 'TmpUser' );

	public $components = array( 'Cookie' );

	// ログインなしでアクセス可能なページ
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow( 'login', 'logout', 'add', 'sign_up', 'code', 'provision', 'resend_password', 'resend_password_completed', 'reset_password', 'reset_password_completed' );
	}

	// 初期登録時のアルバム数
	private $default_album = 3;

	public function login() {
		$this->layout = 'home_layout';

		$cookie = $this->Cookie->read( 'Auth.User' );  // クッキー取得
		if ( ! is_null( $cookie ) ) {
			// データセット
			$this->DatUser->set( $cookie );
			if ( $this->Auth->login() ) {
				// クッキーでログインできる場合はそのまま管理画面へ
				$this->Auth->redirect = '../control-panel/';
				$this->redirect( $this->Auth->redirect );
			} else {
				// cookieの内容でログイン失敗
				$this->Cookie->delete( 'Auth.DatUser' );  //  クッキー削除
			}
		}

		if ( $this->request->is('post') ) {

			// 次回からパスワードを入力しないのチェックありの場合
			if ( isset( $this->request->data['DatUser']['remember'] ) && $this->request->data['DatUser']['remember'] === 'on' ) {
				// 次回からパスワード入力しない場合
				$this->Cookie->write('Auth.User', $this->request->data, true, '+2 weeks');
			}

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
				}
			}
		}
	}

	public function logout() {
		$this->layout = 'login';
		$this->Cookie->delete( 'Auth.User' );  //  クッキー削除
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
		$user = $this->DatUser->read(null, $this->Auth->user('user_id'));
		$this->set( compact( 'meta_data', 'sitename', 'user' ) );

		$this->layout = 'control_panel_layout';
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

							$send_url = Router::url("/", true);

							// 対象ユーザへEmail送信
							$Email = new CakeEmail( 'default' );
							$message = $Email->to( $this->request->data['DatUser']['email'] )
							->subject( '会員登録が完了しました' )
							->template( 'user_add_complete_to_user' )
							->viewVars( array( 'username' => $this->request->data['DatUser']['username']
									,'email' => $this->request->data['DatUser']['email']
									,'url' => $send_url ) )
							->send();

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
			$requestData = split( '&', urldecode( $this->request->input() ) );
			foreach ( $requestData as $key => $data ) {
				$splitData = split( '=', $data );
				$makeData[$splitData[0]] = $splitData[1];
			}

			$this->DatUser->create( false );

			$password_flg = true;
			if ( isset ( $makeData['new_password'] ) && $makeData['new_password'] !== '' ) {
				// 古いパスワードのチェック
				$oldpassword = $this->Auth->password( $makeData['old_password'] );
				$datUser = $this->DatUser->checkUserByOldPassword( $this->request->username, $oldpassword );
				if ( $datUser ) {
					$this->DatUser->set( 'password', $makeData['new_password'] );
				}
				else {
					$password_flg = false;
					$this->set( 'datUser', array( 'errorMsg' => '古いパスワードをご確認ください。' ) );
				}
			}
			else {
				unset( $this->DatUser->validate['password'] );
			}

			if ( $password_flg ) {
				// データセット
				$this->DatUser->set( 'user_id', $datuser['DatUser']['id'] );
				if ( isset ( $makeData['sitename'] ) ) $this->DatUser->set( 'sitename', $makeData['sitename'] );
				if ( isset ( $makeData['intro'] ) ) $this->DatUser->set( 'intro', $makeData['intro'] );
				if ( isset ( $makeData['email'] ) ) $this->DatUser->set( 'email', $makeData['email'] );
				if ( isset ( $makeData['sitename'] ) || isset ( $makeData['email'] ) || isset ( $makeData['intro'] ) || isset ( $makeData['password'] ) ) $this->DatUser->set( 'update_timestamp', date('Y-m-d h:i:s') );

				unset( $this->DatUser->validate['username'] );
				if ( $this->DatUser->validates() ) {
					// 更新処理
					$this->DatUser->save();
					$this->set( 'datUser', true );
				}
				else {
					if ( isset( $this->DatUser->validationErrors['sitename'][0] ) ) {
						$this->set( 'datUser', array( 'errorMsg' => $this->DatUser->validationErrors['sitename'][0] ) );
					}
					else if ( isset( $this->DatUser->validationErrors['intro'][0] ) ) {
						$this->set( 'datUser', array( 'errorMsg' => $this->DatUser->validationErrors['intro'][0] ) );
					}
					else if ( isset( $this->DatUser->validationErrors['email'][0] ) ) {
						$this->set( 'datUser', array( 'errorMsg' => $this->DatUser->validationErrors['email'][0] ) );
					}
					else if ( isset( $this->DatUser->validationErrors['password'][0] ) ) {
						$this->set( 'datUser', array( 'errorMsg' => $this->DatUser->validationErrors['password'][0] ) );
					}
				}
			}
		}
		$this->set('_serialize', 'datUser');
	}

	/**
	 * 仮パスワード発行
	 */
	public function resend_password() {

		$this->layout = 'sign_up_layout';

		if ( $this->request->is( 'post' ) ) {
			// ボタン押下時
			unset( $this->DatUser->validate['username'] );
			unset( $this->DatUser->validate['password'] );

			$this->DatUser->set( 'email', $this->request->data['DatUser']['email'] );
			if ( $this->DatUser->validates() ) {
				$datUser = $this->DatUser->getUserDataByEmail( $this->request->data['DatUser']['email'] );
				if ( $datUser ) {
					// パスワード再発行
					$password = $this->_getRandomString( 16 );

					// 再発行パスワードでパスワード更新
					$this->DatUser->create( false );
					$this->DatUser->set(  'user_id', $datUser['DatUser']['user_id'] );
					$this->DatUser->set( 'password', $password );
					$this->DatUser->set( 'update_timestamp', date('Y-m-d h:i:s') );
					$this->DatUser->save();

					// 対象ユーザへEmail送信
					$Email = new CakeEmail( 'default' );
					$message = $Email->to( $this->request->data['DatUser']['email'] )
					->subject( 'パスワード再発行' )
					->template( 'resend_password_to_user' )
					->viewVars( array( 'password' => $password, 'username' => $datUser['DatUser']['username'] ) )
					->send();

					$this->redirect('/resend_password_completed');
				}
				else {
					// 登録されていないemail
					$this->set( 'error_message', '登録されていないE-mailです。' );
				}
			}
		}
	}

	/**
	 * 仮パスワード発行完了
	 */
	public function resend_password_completed() {

		$this->layout = 'sign_up_layout';
	}

	/**
	 * 新しいパスワード登録
	 * @param string $username
	 */
	public function reset_password( $username = null ) {

		$this->layout = 'sign_up_layout';

		if ( ! is_null( $username ) ) {
			$this->Session->write( 'tmp_username', $username );
		}
		else {
			$username = $this->Session->read( 'tmp_username' );
		}

		if ( $this->request->is( 'post' ) ) {

			// ボタン押下時
			unset( $this->DatUser->validate['username'] );

			$this->DatUser->set( 'password', $this->request->data['DatUser']['password'] );
			// バリデーションチェック
			if ( $this->DatUser->validates() ) {

				// データセット
				$oldpassword = $this->Auth->password( $this->request->data['DatUser']['oldpassword'] );
				$datUser = $this->DatUser->checkUserByOldPassword( $username, $oldpassword );
				if ( $datUser ) {

					// 再発行パスワードでパスワード更新
					$this->DatUser->create( false );
					$this->DatUser->set( 'user_id', $datUser['DatUser']['user_id'] );
					$this->DatUser->set( 'password', $this->request->data['DatUser']['password'] );
					$this->DatUser->set( 'update_timestamp', date('Y-m-d h:i:s') );
					$this->DatUser->save();

					// 完了画面へ
					$this->Session->delete( 'tmp_username' );
					$this->redirect('/reset_password_completed');
				}
				else {
					$this->set( 'error_message', '発行されたパスワードを確認してください。' );
				}
			}
		}
	}

	/**
	 * 新しいパスワード登録完了画面
	 */
	public function reset_password_completed() {

		$this->layout = 'sign_up_layout';
	}

	/**
	 * ランダムな文字列を生成
	 * @param number $nLengthRequired
	 * @return string
	 */
	private function _getRandomString( $nLengthRequired = 8 ) {

		$sCharList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
		mt_srand();
		$sRes = '';

		for($i = 0; $i < $nLengthRequired; $i++) {
			$sRes .= $sCharList{mt_rand(0, strlen($sCharList) - 1)};
		}
    	return $sRes;
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
