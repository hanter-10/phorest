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
	public $components = array('RequestHandler','Convert','Check','Thumbmake');

	function beforeFilter() {
		// 親クラスをロード
		parent::beforeFilter();

		// POSTの場合はajax規制を排除
		if (!$this->request->is('post')) {
			// ajax通信でのアクセスか確認
			if(!$this->RequestHandler->isAjax()) {
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

		// 返り値のデフォルトセット：false
		$this->set('datPhoto', false);

		// Uploadのチェック
		if (!$this->Check->doCheckUploadAction($_FILES)) {
			// uploadではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}

		// リクエストメソッド判断
		if ($this->request->is('post')) {

			/**
			 * 画像アップロード処理
			 */
			// オリジナル画像アップロード先
			$imagePath	= WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username');
			$image		= WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . $_FILES['file']['name'];

			//保存先のディレクトリが存在しているかチェック
			if (!file_exists($imagePath)) {
				mkdir($imagePath);
			}

			//TODO:拡張子のチェックとかも必要になる事だろう

			// 画像のアップロード
			move_uploaded_file($_FILES['file']['tmp_name'], $image);

			/**
			 *  サムネイル画像作成
			 **/
			// サムネイル画像アップロード先
			$thumbnail_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . 'thumbnail' . DS . $_FILES['file']['name'];

			// 参考サムネイルサイズ
			$width	= 150;
			$height	= 114;

			// 初期化
			$this->Thumbmake->init();
			// 元画像のファイルパスと保存先をセット
			$this->Thumbmake->setImage($image, $thumbnail_image);

			// 画像サイズ取得
			$Jsize = getimagesize("$image");

			// リサイズ処理
			// 画像の縦横サイズチェック
			if ( $Jsize[0] >= $Jsize[1] ) {
				// 縦より横が大きい場合
				if (!$this->Thumbmake->width($width)) {
					//TODO:サムネイル画像作成失敗
				}
			} else {
				// 横より縦が大きい場合
				if (!$this->Thumbmake->height($height)) {
					//TODO:サムネイル画像作成失敗
				}
			}


			/**
			 * スクエア画像作成
			 */
			// スクエア画像アップロード先
			$square_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . 'square' . DS . $_FILES['file']['name'];

			// 参考サムネイルサイズ
			$width	= 270;
			$height	= 270;

			// 初期化
			$this->Thumbmake->init();
			// 元画像のファイルパスと保存先をセット
			$this->Thumbmake->setImage($image, $square_image);

			// 画像サイズ取得
// 			$Jsize = getimagesize("$image");

			// リサイズ処理
			if ($this->Thumbmake->resizeCrop($width,$height)) {
				//TODO:スクエア画像作成失敗
			}


			/**
			 * Small (横幅500)画像作成
			 */
			// スクエア画像アップロード先
			$small_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . 'small' . DS . $_FILES['file']['name'];

			// 参考サムネイルサイズ
			$width	= 500;

			// 初期化
			$this->Thumbmake->init();
			// 元画像のファイルパスと保存先をセット
			$this->Thumbmake->setImage($image, $small_image);

			// 画像サイズ取得
// 			$Jsize = getimagesize("$image");

			// リサイズ処理
			if (!$this->Thumbmake->width($width)) {
				//TODO:サムネイル画像作成失敗
			}


			/**
			 * medium (横幅1000)画像作成
			 */
			// スクエア画像アップロード先
			$medium_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . 'medium' . DS . $_FILES['file']['name'];

			// 参考サムネイルサイズ
			$width	= 1000;

			// 初期化
			$this->Thumbmake->init();
			// 元画像のファイルパスと保存先をセット
			$this->Thumbmake->setImage($image, $medium_image);

			// 画像サイズ取得
// 			$Jsize = getimagesize("$image");

			// リサイズ処理
			if (!$this->Thumbmake->width($width)) {
				//TODO:サムネイル画像作成失敗
			}


			/**
			 * large (横幅2000)画像作成
			 */
			// スクエア画像アップロード先
			$large_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . 'large' . DS . $_FILES['file']['name'];

			// 参考サムネイルサイズ
			$width	= 2000;

			// 初期化
			$this->Thumbmake->init();
			// 元画像のファイルパスと保存先をセット
			$this->Thumbmake->setImage($image, $large_image);

			// 画像サイズ取得
// 			$Jsize = getimagesize("$image");

			// リサイズ処理
			if (!$this->Thumbmake->width($width)) {
				//TODO:サムネイル画像作成失敗
			}

			/**
			 * large (横幅2000)画像作成
			 */
			// スクエア画像アップロード先
			$large_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . 'large' . DS . $_FILES['file']['name'];

			// 参考サムネイルサイズ
			$width	= 2000;

			// 初期化
			$this->Thumbmake->init();
			// 元画像のファイルパスと保存先をセット
			$this->Thumbmake->setImage($image, $large_image);

			// 画像サイズ取得
			// 			$Jsize = getimagesize("$image");

			// リサイズ処理
			if (!$this->Thumbmake->width($width)) {
				//TODO:サムネイル画像作成失敗
			}


			/**
			 * huge (横幅3000)画像作成
			 */
			// スクエア画像アップロード先
			$huge_image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . 'huge' . DS . $_FILES['file']['name'];

			// 参考サムネイルサイズ
			$width	= 3000;

			// 初期化
			$this->Thumbmake->init();
			// 元画像のファイルパスと保存先をセット
			$this->Thumbmake->setImage($image, $huge_image);

			// 画像サイズ取得
// 			$Jsize = getimagesize("$image");

			// リサイズ処理
			if (!$this->Thumbmake->width($width)) {
				//TODO:サムネイル画像作成失敗
			}


			/* paramater set */
			$datPhoto['DatPhoto']['fk_user_id']				= $this->Auth->user('user_id');		// 会員ID:セッションより取得
			$datPhoto['DatPhoto']['fk_image_server_id']		= 1;								// TODO:対象の画像サーバのidを取得する
			$datPhoto['DatPhoto']['photoName']				= $_FILES['file']['name'];			// 写真名
// 			$datPhoto['DatPhoto']['description']			= '';								// 写真説明
			$datPhoto['DatPhoto']['width']					= $Jsize[0];						// 画像の横幅
			$datPhoto['DatPhoto']['height']					= $Jsize[1];						// 画像の縦幅
			$datPhoto['DatPhoto']['file_name']				= $_FILES['file']['name'];			// 画像の名前を決める
			$datPhoto['DatPhoto']['size']					= $_FILES['file']['size'];			// 画像のサイズを取得
			$datPhoto['DatPhoto']['type']					= $_FILES['file']['type'];			// 画像のタイプを取得
			$datPhoto['DatPhoto']['status']					= 1;								// デフォルトは有効
			$datPhoto['DatPhoto']['create_datetime']		= date('Y-m-d h:i:s');
			$datPhoto['DatPhoto']['update_timestamp']		= date('Y-m-d h:i:s');

			// Modelに値をセット
			$this->DatPhoto->set($datPhoto);

			// バリデーションチェック
			if ($this->DatPhoto->validates()) {

				/* バリデーション通過 */

				/* insert query */
				$this->DatPhoto->create();
				if ($this->DatPhoto->save($datPhoto)) {
					/* get insert new id */
					$datPhoto['DatPhoto']['photo_id'] = $this->DatPhoto->id;

					$this->set('datPhoto', $datPhoto);
				}
			}
		} else {
			// postではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
		$this->set('_serialize', 'datPhoto');
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
		if (!$this->DatPhoto->exists()) {
			// Not Data
			throw new NotFoundException(__('Invalid dat photo'));
		} else {

		}

		// リクエストメソッド判断
		if ($this->request->is('put')) {

			// リクエストデータをJSON形式にエンコードで取得する
			$requestData = $this->request->input('json_decode');

			/* 固定パラメータセット */
			$optionData = array();
			$optionData = array(
				'photo_id'			=> $id,
				'fk_user_id'		=> $this->Auth->user('user_id'),
				'update_timestamp'	=> date('Y-m-d h:i:s'),
			);
			/* リクエストパラメータセット */
			$datPhoto = $this->Convert->doConvertObjectToModelArray($requestData, 'DatPhoto', $optionData);

			/* 配列のキー値の例外チェック */
			if ( !$this->Check->doCheckArrayKeyToModel( $datPhoto['DatPhoto'], $this->DatPhoto->modelColumn ) ) {
				// エラー：例外パラメータ
				throw new BadRequestException(__('Bad Request.'));
			}

			// Modelに値をセット
			$this->DatPhoto->set($datPhoto);

			// バリデーションチェック
			if ($this->DatPhoto->validates()) {

				/* バリデーション通過 */

				/* update query */
				$result = $this->DatPhoto->updateAll(
						// Update set
// 						array(
// 								'DatPhoto.name'				=> "'".$datPhoto['DatPhoto']['name']."'",
// 								'DatPhoto.description'		=> "'".$datPhoto['DatPhoto']['description']."'",
// 								'DatPhoto.status'			=> $datPhoto['DatPhoto']['status']."'",
// 								'DatPhoto.update_timestamp'	=> "'".$datPhoto['DatPhoto']['update_timestamp']."'",
// 						)
						// update fieldを動的に設定
						$this->Convert->doConvertArrayKeyToQueryArray( $datPhoto['DatPhoto'], 'DatPhoto', $this->DatPhoto->updateColumn ),
						// Where
						array(
								array(
										'DatPhoto.photo_id' => $datPhoto['DatPhoto']['photo_id'],
										'DatPhoto.fk_user_id' => $datPhoto['DatPhoto']['fk_user_id'],
								)
						)
				);
				$this->set('datPhoto', $result);
			}
			// Validationエラー内容
// 			$this->DatAlbum->validationErrors;

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
		if (!$this->DatPhoto->exists()) {
			throw new NotFoundException(__('Invalid dat photo'));
		}

		// リクエストメソッド判断
		if ($this->request->is('delete')) {

			/* paramater set */
			$datPhoto['DatPhoto']['photo_id']			= $id;
			$datPhoto['DatPhoto']['status'] 			= 0;
			$datPhoto['DatPhoto']['update_timestamp']	= date('Y-m-d h:i:s');

			// Modelに値をセット
			$this->DatPhoto->set($datPhoto);

			// バリデーションチェック
			if ($this->DatPhoto->validates()) {

				/* バリデーション通過 */

				/* update query */
				$result = $this->DatPhoto->updateAll(
						// Update set
						array(
								'DatPhoto.status'		 	=> $datPhoto['DatPhoto']['status'],
								'DatPhoto.update_timestamp'	=> "'".$datPhoto['DatPhoto']['update_timestamp']."'"
						)
						// Where
						,array(
								array(
										'DatPhoto.photo_id'	=> $datPhoto['DatPhoto']['photo_id']
								)
						)
				);
				$this->set('datPhoto', $result);
			}
		} else {
			throw new MethodNotAllowedException();
		}
		$this->set('_serialize', 'datPhoto');
	}
}
