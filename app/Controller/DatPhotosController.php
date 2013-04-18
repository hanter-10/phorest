<?php
App::uses('AppController', 'Controller');
/**
 * DatPhotos Controller
 *
 * @property DatPhoto $DatPhoto
 */
class DatPhotosController extends AppController {

/**
 * [ Phorest ] :
 */
	public $viewClass = 'Json';
	public $components = array('RequestHandler','Convert','Check','Thumbmake', 'Ftp');

	public $uses = array('DatAlbum','DatPhoto','DatUser','MstImageServer');

	function beforeFilter() {
		// 親クラスをロード
		parent::beforeFilter();

		// POSTの場合はajax規制を排除
		if ( ! $this->request->is( 'post' ) ) {
			// ajax通信でのアクセスか確認
			if( ! $this->RequestHandler->isAjax() ) {
				// ajaxではない時は「400 Bad Request」
				throw new BadRequestException(__('Bad Request.'));
			}
		}
	}


// /**
//  * index method
//  *
//  * @return void
//  *
//  * URI：/tempalbum/ -> /datphotos/
//  */
// 	public function index() {

// 		//$this->DatPhoto->recursive = 0;				HABTMの際に関連テーブルを検索するので削除

// 		// リクエストメソッド判断
// 		if ($this->request->is('get')) {

// 			//TODO:リファラーチェックとかhash認証コードとかもしといたほうがいいんだろな。

// 			$db = $this->DatPhoto->getDataSource();
// 			$datPhotos = $db->fetchAll(
// <<<EOF
// 					SELECT
// 						DatPhoto.photo_id as id,
// 						DatPhoto.fk_user_id,
// 						DatPhoto.photoName as photoName,
// 						DatPhoto.description as description,
// 						DatPhoto.file_name,
// 						DatPhoto.thum_file_name,
// 						concat('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.user_id,'/',DatPhoto.file_name) as imgUrl,
// 						concat('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.user_id,'/',DatPhoto.thum_file_name) as thumUrl,
// 						DatPhoto.size,
// 						DatPhoto.type,
// 						DatPhoto.status,
// 						DatPhoto.create_datetime,
// 						DatPhoto.update_timestamp
// 					FROM
// 						`dat_photos` as DatPhoto
// 						left outer join dat_album_photo_relations as DatAlbumPhotoRelation
// 							on DatPhoto.photo_id = DatAlbumPhotoRelation.fk_photo_id
// 						left outer join dat_users as DatUser
// 							on DatPhoto.fk_user_id = DatUser.user_id
// 						inner join mst_image_servers as MstImageServer
// 							on DatPhoto.fk_image_server_id = MstImageServer.image_server_id
// 					where
// 						DatAlbumPhotoRelation.fk_photo_id is null and DatPhoto.status = ?
// EOF
// 					,array(1)
// 			);

// 			// TODO:データ入れ替え処理 もっと良いやり方があるはず・・・
// 			foreach( $datPhotos as $key => $datPhoto ) {
// 				$datPhotos[$key]['DatPhoto']['imgUrl'] = $datPhoto[0]['imgUrl'];
// 				$datPhotos[$key]['DatPhoto']['thumUrl'] = $datPhoto[0]['thumUrl'];

// 				// いらないものを消す
// 				unset($datPhotos[$key][0]);
// 			}

// 			$this->set('datPhotos', $datPhotos);

// 		} else {
// 			// postではない時は「400 Bad Request」
// 			throw new BadRequestException(__('Bad Request.'));
// 		}
// 		// JsonViewは”_serialize”という名前で配列(array)を設定するとそれをJSONとして出力してくれる
// 		$this->set('_serialize', 'datPhotos');
// 	}

// /**
//  * view method
//  *
//  * @throws NotFoundException
//  * @param string $id
//  * @return void
//  */
// 	public function view($id = null) {

// 		$this->DatPhoto->recursive = 0;		// 写真情報のみ取得

// 		$this->DatPhoto->id = $id;
// 		if (!$this->DatPhoto->exists()) {
// 			// Not Data
// 			throw new NotFoundException(__('Invalid dat photo'));
// 		}
// 		$this->set('datPhoto', $this->DatPhoto->read(null, $id));
// 		$this->set('_serialize', 'datPhoto');
// 	}

/**
 * add method
 *
 * [POST]写真データ登録/写真アップロード処理API
 * @return void
 */
	public function add() {

		try
		{
			// 返り値のデフォルトセット：false
			$this->set( 'datPhoto', array( 'errorMsg' => '写真追加に失敗しました。画面を更新して画像が追加されているか確認してください。' ) );

			// Uploadのチェック
			if ( ! $this->Check->doCheckUploadAction( $_FILES) ) {
				// uploadではない時は「400 Bad Request」
				throw new BadRequestException(__('Bad Request.'));
			}

			// リクエストメソッド判断
			if ( $this->request->is( 'post' ) ) {

				$username	= $this->Auth->user('username');

				// TODO:ちょっといけすかないけどしょうがないさ
				$photoname	= explode( '.', $_FILES['file']['name'] );

				// 全角ファイル名のチェック
				if ( $this->_checkHalf( $_FILES['file']['name'] ) ) {
					// 全角ファイル名の場合はリネーム
					$filename = date( 'Ymd' ) . '-' . $this->_getRandomString() . '.' . $photoname[1];
				}
				else {
					// それ以外はそのまま
					$filename = $_FILES['file']['name'];
				}

				$imagePath	= WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username');

				// 重複ファイル名の確認＆リネーム
				$i = 1;
				while ( file_exists( $imagePath . DS . $filename ) ) {
					$filename	= $_FILES['file']['name'];
					$filename	= "($i)".$filename;
					$i++;
				}

				/**
				 * 画像アップロード処理
				 */
				// オリジナル画像アップロード先
				$image		= WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . $filename;

				//保存先のディレクトリが存在しているかチェック
				if ( ! file_exists( $imagePath ) ) {
					mkdir( $imagePath );
				}

				//TODO:拡張子のチェックとかも必要になる事だろう

				// 画像のアップロード
				move_uploaded_file( $_FILES['file']['tmp_name'], $image );

				/**
				 *  サムネイル画像作成
				 **/
				// サムネイル画像アップロード先
				$thumbnail_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . UPLOAD_DIR_THUMBNAIL . DS . $filename;

				// 参考サムネイルサイズ
				$width	= 150;
				$height	= 114;

				// 初期化
				$this->Thumbmake->init();
				// 元画像のファイルパスと保存先をセット
				$this->Thumbmake->setImage( $image, $thumbnail_image );

				// 画像サイズ取得
				$Jsize = getimagesize( "$image" );

				// リサイズ処理
				// 画像の縦横サイズチェック
				if ( $Jsize[0] >= $Jsize[1] ) {
					// 縦より横が大きい場合
					if ( ! $this->Thumbmake->width( $width ) ) {
						//TODO:サムネイル画像作成失敗
					}
				} else {
					// 横より縦が大きい場合
					if ( ! $this->Thumbmake->height( $height ) ) {
						//TODO:サムネイル画像作成失敗
					}
				}

				/**
				 * スクエア画像作成
				 */
				// スクエア画像アップロード先
				$square_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . UPLOAD_DIR_SQUARE . DS . $filename;

				// 参考スクエアサイズ
				$width	= 270;
				$height	= 270;

				// 初期化
				$this->Thumbmake->init();
				// 元画像のファイルパスと保存先をセット
				$this->Thumbmake->setImage( $image, $square_image );

				// リサイズ処理
				if ( $this->Thumbmake->resizeCrop( $width, $height ) ) {
					//TODO:スクエア画像作成失敗
				}

	// TODO:処理が重いので現状はコメントアウトしておく
	// 			/**
	// 			 * Small (横幅500)画像作成
	// 			 */
	// 			// スクエア画像アップロード先
	// 			$small_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . UPLOAD_DIR_SMALL . DS . $filename;

	// 			// 参考サムネイルサイズ
	// 			if ($Jsize[0] >= 500) {

	// 				$width	= 500;

	// 				// 初期化
	// 				$this->Thumbmake->init();
	// 				// 元画像のファイルパスと保存先をセット
	// 				$this->Thumbmake->setImage($image, $small_image);

	// 				// リサイズ処理
	// 				if (!$this->Thumbmake->width($width)) {
	// 					//TODO:サムネイル画像作成失敗
	// 				}
	// 			}
	// 			else {
	// 				// オリジナル画像コピー
	// 				copy($image, $medium_image);
	// 			}

				/**
				 * medium (横幅1280)画像作成
				 */
				// ミディアム画像アップロード先
				$medium_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . UPLOAD_DIR_MEDIUM . DS . $filename;
				$medium_path = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . UPLOAD_DIR_MEDIUM;

				// 参考サムネイルサイズ
				if ($Jsize[0] >= 1280) {

					$width	= 1280;

					// 初期化
					$this->Thumbmake->init();
					// 元画像のファイルパスと保存先をセット
					$this->Thumbmake->setImage( $image, $medium_image );

					// リサイズ処理
					if ( ! $this->Thumbmake->width( $width ) ) {
						//TODO:サムネイル画像作成失敗
					}
				}
				else {

					//保存先のディレクトリが存在しているかチェック
					if ( ! file_exists( $medium_path ) ) {
						mkdir($medium_path);
					}
					// オリジナル画像コピー
					copy( $image, $medium_image );
				}

	// 			/**
	// 			 * large (横幅1600)画像作成
	// 			 */
	// 			// スクエア画像アップロード先
	// 			$large_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . UPLOAD_DIR_LARGE . DS . $filename;

	// 			// 参考サムネイルサイズ
	// 			if ($Jsize[0] >= 2000) {

	// 				$width	= 2000;

	// 				// 初期化
	// 				$this->Thumbmake->init();
	// 				// 元画像のファイルパスと保存先をセット
	// 				$this->Thumbmake->setImage($image, $large_image);

	// 				// リサイズ処理
	// 				if (!$this->Thumbmake->width($width)) {
	// 					//TODO:サムネイル画像作成失敗
	// 				}
	// 			}
	// 			else {
	// 				// オリジナル画像コピー
	// 				copy($image, $medium_image);
	// 			}

	// 			/**
	// 			 * huge (横幅3000)画像作成
	// 			 */
	// 			// スクエア画像アップロード先
	// 			$huge_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . UPLOAD_DIR_HUGE . DS . $filename;

	// 			// 参考サムネイルサイズ
	// 			if ($Jsize[0] >= 3000) {

	// 				$width	= 3000;

	// 				// 初期化
	// 				$this->Thumbmake->init();
	// 				// 元画像のファイルパスと保存先をセット
	// 				$this->Thumbmake->setImage($image, $huge_image);

	// 				// リサイズ処理
	// 				if (!$this->Thumbmake->width($width)) {
	// 					//TODO:サムネイル画像作成失敗
	// 				}
	// 			}
	// 			else {
	// 				// オリジナル画像コピー
	// 				copy($image, $medium_image);
	// 			}

				// ランダムに画像サーバを選択する
				$image_server_id = $this->MstImageServer->getSelectImageServer();

				// 画像パスを取得する
				$mstImage = $this->MstImageServer->getImageServerPathByUser( $image_server_id, $username, $filename );

				/* paramater set */
				$datPhoto['fk_user_id']				= $this->Auth->user( 'user_id' );				// 会員ID:セッションより取得
				$datPhoto['fk_image_server_id']		= $image_server_id;
				$datPhoto['photoName']				= $photoname[0];								// 写真名
				$datPhoto['description']			= '';											// 写真説明
				$datPhoto['width']					= $Jsize[0];									// 画像の横幅
				$datPhoto['height']					= $Jsize[1];									// 画像の縦幅
				$datPhoto['file_name']				= $filename;									// 画像の名前を決める
				$datPhoto['imgUrl']					= $mstImage['MstImageServer']['imgUrl'];
				$datPhoto['thumUrl']				= $mstImage['MstImageServer']['thumUrl'];
				$datPhoto['thumUrl_square']			= $mstImage['MstImageServer']['thumUrl_square'];
				$datPhoto['imgUrl_m']				= $mstImage['MstImageServer']['imgUrl_m'];
				$datPhoto['size']					= $_FILES['file']['size'];						// 画像のサイズを取得
				$datPhoto['type']					= $_FILES['file']['type'];						// 画像のタイプを取得
				$datPhoto['status']					= STATUS_ON;									// デフォルトは有効
				$datPhoto['create_datetime']		= date('Y-m-d h:i:s');
				$datPhoto['update_timestamp']		= date('Y-m-d h:i:s');

				$this->DatPhoto->set( $datPhoto );

				// バリデーションチェック
				if ( $this->DatPhoto->validates() ) {

					/* insert query */
					$this->DatPhoto->create();
					if ( $this->DatPhoto->save( $datPhoto ) ) {
						/* get insert new id */
						$datPhoto['id'] = $this->DatPhoto->id;
						$this->set('datPhoto', $datPhoto);
					}

					// 外部画像サーバへの送信は本番環境のみ
					if ( Configure::read( 'envronment' ) === 'production' ) {

						// FTPサーバ接続
						$conn_id = $this->Ftp->FtpLogin();
						// アップロード処理
						$this->Ftp->FtpUpload( $conn_id, $this->Auth->user('username'), $filename, $image, null );						// オリジナルファイル
						$this->Ftp->FtpUpload( $conn_id, $this->Auth->user('username'), $filename, $thumbnail_image, 'thumbnail' );		// サムネイルファイル
						$this->Ftp->FtpUpload( $conn_id, $this->Auth->user('username'), $filename, $square_image, 'square' );			// スクエアファイル
						$this->Ftp->FtpUpload( $conn_id, $this->Auth->user('username'), $filename, $medium_image, 'medium' );			// メディアムファイル
						// FTPサーバ接続切断
						$this->Ftp->FtpClose( $conn_id );
					}
				}
				else {
					if ( isset( $this->DatPhoto->validationErrors['file_name'][0] ) ) {
						$this->set( 'datPhoto', array( 'errorMsg' => $this->DatPhoto->validationErrors['file_name'][0] ) );
					}
					else {
						// バリデーションエラー
						$this->set( 'datPhoto', array( 'errorMsg' => '写真追加に失敗しました。画面を更新して画像が追加されているか確認してください。' ) );
					}
				}
			} else {
				// postではない時は「400 Bad Request」
				throw new BadRequestException(__('Bad Request.'));
			}
			$this->set( '_serialize', 'datPhoto' );
		}
		catch ( Exception $e ) {
			$this->set( 'datPhoto', array( 'errorMsg' => '写真追加に失敗しました。画面を更新して画像が追加されているか確認してください。' ) );
			$this->set( '_serialize', 'datPhoto' );
		}
	}

/**
 * edit method
 *
 * [PUT/id]写真情報の変更API
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		// 返り値のデフォルトセット：false
		$this->set('datPhoto', false);

		$this->DatPhoto->id = $id;
		if ( ! $this->DatPhoto->exists() ) {
			// Not Data
			throw new NotFoundException(__('Invalid dat photo'));
		}

		// リクエストメソッド判断
		if ( $this->request->is( 'put' ) || $this->request->is( 'patch' ) ) {

			// リクエストデータをJSON形式にエンコードで取得する
			$requestData = $this->request->input('json_decode');

			// Modelに値をセット
			$this->DatPhoto->set( 'photo_id', $id );
			$this->DatPhoto->set( 'fk_user_id', $this->Auth->user('user_id') );
			if ( isset( $requestData->photoName ) ) $this->DatPhoto->set( 'photoName', $requestData->photoName );
			if ( isset( $requestData->description ) )$this->DatPhoto->set( 'description', $requestData->description );
			$this->DatPhoto->set( 'update_timestamp', date('Y-m-d h:i:s') );

			// バリデーションチェック
			if ( $this->DatPhoto->validates() ) {

				// 更新処理
				if ( $this->DatPhoto->save() ) {
					$this->set( 'datPhoto', true );
				}
			}
		} else {
			// putではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
		$this->set('_serialize', 'datPhoto');
	}

/**
 * delete method
 *
 * [PUT/id]写真情報の論理削除API
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {

		// 返り値のデフォルトセット：false
		$this->set('datPhoto', false);

		// idが存在するかチェック
		$this->DatPhoto->id = $id;
		if ( ! $this->DatPhoto->exists() ) {
			throw new NotFoundException(__('Invalid dat photo'));
		}

		// リクエストメソッド判断
		if ( $this->request->is( 'delete' ) ) {

			// Modelに値をセット
			$this->DatPhoto->set( 'photo_id', $id );
			$this->DatPhoto->set( 'status', STATUS_OFF );
			$this->DatPhoto->set( 'update_timestamp', date('Y-m-d h:i:s') );

			// バリデーションチェック
			if ( $this->DatPhoto->validates() ) {

				if ( $this->DatPhoto->save() ) {
					$this->set( 'datPhoto', true );
				}
			}
		} else {
			throw new MethodNotAllowedException();
		}
		$this->set('_serialize', 'datPhoto');
	}

	/**
	 * 全角文字チェック関数
	 * @param unknown $data
	 * @return boolean
	 */
	private function _checkHalf( $data ) {

		$ret = false;
		$len = strlen( $data );
		$mblen = mb_strlen( $data,mb_internal_encoding() );
		if ( $len !== $mblen ) {
			$ret = true;
		}
		return $ret;
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
}
