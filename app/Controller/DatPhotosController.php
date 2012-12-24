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


/**
 * index method
 *
 * @return void
 *
 * URI：/tempalbum/ -> /datphotos/
 */
	public function index() {

		//$this->DatPhoto->recursive = 0;				HABTMの際に関連テーブルを検索するので削除

// 		$option = array(
// 				'conditions' => array(
// 						//'DatAlbum.album_id'		=> '',
// 						'DatPhoto.status'		=> 1,
// 				),
// 		);
// 		$datPhotos = $this->DatPhoto->find('all', $option);
// 		$datPhotos = $this->DatAlbumPhotoRelation->find('all', $option);

		$db = $this->DatPhoto->getDataSource();
		$datPhotos = $db->fetchAll(
<<<EOF
				SELECT
					DatPhoto.photo_id as id,
					DatPhoto.fk_user_id,
					DatPhoto.photoName as photoName,
					DatPhoto.description as description,
					DatPhoto.file_name,
					DatPhoto.thum_file_name,
					concat('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.user_id,'/',DatPhoto.file_name) as imgUrl,
					concat('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.user_id,'/',DatPhoto.thum_file_name) as thumUrl,
					DatPhoto.size,
					DatPhoto.type,
					DatPhoto.status,
					DatPhoto.create_datetime,
					DatPhoto.update_timestamp
				FROM
					`dat_photos` as DatPhoto
					left outer join dat_album_photo_relations as DatAlbumPhotoRelation
						on DatPhoto.photo_id = DatAlbumPhotoRelation.fk_photo_id
					left outer join dat_users as DatUser
						on DatPhoto.fk_user_id = DatUser.user_id
					inner join mst_image_servers as MstImageServer
						on DatPhoto.fk_image_server_id = MstImageServer.image_server_id
				where
					DatAlbumPhotoRelation.fk_photo_id is null and DatPhoto.status = ?
EOF
				,array(1)
		);

		// TODO:データ入れ替え処理 もっと良いやり方があるはず・・・
		foreach( $datPhotos as $key => $datPhoto ) {
			$datPhotos[$key]['DatPhoto']['imgUrl'] = $datPhoto[0]['imgUrl'];
			$datPhotos[$key]['DatPhoto']['thumUrl'] = $datPhoto[0]['thumUrl'];

			// いらないものを消す
			unset($datPhotos[$key][0]);
		}

// 		$datPhotos = $this->DatPhoto->query("
// 				SELECT * FROM `dat_photos` as photo
// 				left outer join dat_album_photo_relations as relations on photo.photo_id = relations.fk_photo_id
// 				where relations.fk_photo_id is null
// 		");
		$this->set('datPhotos', $datPhotos);

		// JsonViewは”_serialize”という名前で配列(array)を設定するとそれをJSONとして出力してくれる
		$this->set('_serialize', 'datPhotos');
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {

		$this->DatPhoto->recursive = 0;		// 写真情報のみ取得

		$this->DatPhoto->id = $id;
		if (!$this->DatPhoto->exists()) {
			// Not Data
			throw new NotFoundException(__('Invalid dat photo'));
		}
		$this->set('datPhoto', $this->DatPhoto->read(null, $id));
		$this->set('_serialize', 'datPhoto');
	}

/**
 * add method
 *
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
			$imagePath = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username');
			$image = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . $_FILES['file']['name'];

			//保存先のディレクトリが存在しているかチェック
			if(!file_exists($imagePath)){
				mkdir($imagePath);
			}

			// 画像のアップロード
			move_uploaded_file($_FILES['file']['tmp_name'], $image);

			/**
			 *  サムネイル画像作成
			 **/
			// サムネイル画像アップロード先
			$thumbnail_image  = WWW_ROOT . 'img' . DS . 'phorest' . DS . $this->Auth->user('username') . DS . 'thumbnail' . DS . $_FILES['file']['name'];

			// 参考サムネイルサイズ
			$width	= 150;
			$height	= 114;

			// 元画像のファイルパスと保存先をセット
			$this->Thumbmake->setImage($image, $thumbnail_image);

			// 画像サイズ取得
			$Jsize = getimagesize("$image");

			/* リサイズ処理 */

			// 画像の縦横サイズチェック
			if ( $Jsize[0] >= $Jsize[1] ) {
				// 縦より横が大きい場合
				if (!$this->Thumbmake->width($width)) {
					// サムネイル画像作成失敗

				}
			} else {
				// 横より縦が大きい場合
				if (!$this->Thumbmake->height($height)) {
					// サムネイル画像作成失敗

				}
			}

// 			// 画像サイズ取得
// 			$Jsize = getimagesize("$image");
// 			// 横121ピクセル以上なら
// 			if ($Jsize[0] >= 121) {
// 				// 横120ピクセル
// 				$Jwidth = 120;
// 				// 縦サイズを計算
// 				$Jheight = $Jsize[1] * 120 / $Jsize[0];
// 				// 画像を縮小する
// 				$imagein = imagecreatefromjpeg("$image");
// 				// サイズ変更（GD2使用）
// 				$imageout = imagecreatetruecolor($Jwidth,$Jheight);
// 				imagecopyresampled($imageout,$imagein,0,0,0,0,$Jwidth,$Jheight,$Jsize[0],$Jsize[1]);
// 				imagejpeg($imageout,("$thumbnail_image"));	// サムネイル書き出し
// 				imagedestroy($imagein);						// メモリを解放する
// 				imagedestroy($imageout);					// メモリを解放する
// 			}

			// リクエストデータをJSON形式にエンコードで取得する
// 			$data = $this->request->input('json_decode');

			// TODO:$dataの内容を確認して動的にModelに値をセットできるようにする

			/* paramater set */
			$datPhoto['DatPhoto']['fk_user_id']				= $this->Auth->user('user_id');		// 会員ID:セッションより取得
			$datPhoto['DatPhoto']['fk_image_server_id']		= 1;								// TODO:対象の画像サーバのidを取得する
			$datPhoto['DatPhoto']['photoName']				= $_FILES['file']['name'];			// 写真名
// 			$datPhoto['DatPhoto']['description']			= '';								// 写真説明
			$datPhoto['DatPhoto']['file_name']				= $_FILES['file']['name'];			// 画像の名前を決める
			$datPhoto['DatPhoto']['thum_file_name']			= $_FILES['file']['name'];			// サムネイル画像の名前を決める
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

// 			$datPhoto['DatPhoto']['photo_id']			= $id;
// 			$datPhoto['DatPhoto']['fk_user_id']			= $this->Auth->user('user_id');		// 会員ID:セッションより取得
// 			$datPhoto['DatPhoto']['name']				= $data->photoName;
// 			$datPhoto['DatPhoto']['description']		= $data->description;;
// 			$datPhoto['DatPhoto']['status']				= $data->status;
// 			$datPhoto['DatPhoto']['update_timestamp']		= date('Y-m-d h:i:s');

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

// 		$datUsers = $this->DatPhoto->DatUser->find('list');
// 		$mstImageServers = $this->DatPhoto->MstImageServer->find('list');
// 		$datPhotosetPhotoRelations = $this->DatPhoto->DatPhotosetPhotoRelation->find('list');
// 		$this->set(compact('datUsers', 'mstImageServers', 'datPhotosetPhotoRelations'));
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
			$datPhoto['DatPhoto']['photo_id'] = $id;
			$datPhoto['DatPhoto']['status']  = 0;
			$datPhoto['DatPhoto']['update_timestamp']  = date('Y-m-d h:i:s');

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
