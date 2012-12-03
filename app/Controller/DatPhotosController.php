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
	public $components = array('RequestHandler');

	function beforeFilter() {
		// 親クラスをロード
		parent::beforeFilter();

		// ajax通信でのアクセスか確認
		if(!$this->RequestHandler->isAjax()) {
			// ajaxではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
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
					DatPhoto.name as photoName,
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
// 		if ($this->request->is('post')) {
// 			$this->DatPhoto->create();
// 			if ($this->DatPhoto->save($this->request->data)) {
// 				$this->Session->setFlash(__('The dat photo has been saved'));
// 				$this->redirect(array('action' => 'index'));
// 			} else {
// 				$this->Session->setFlash(__('The dat photo could not be saved. Please, try again.'));
// 			}
// 		}
// 		$datUsers = $this->DatPhoto->DatUser->find('list');
// 		$mstImageServers = $this->DatPhoto->MstImageServer->find('list');
// 		$datPhotosetPhotoRelations = $this->DatPhoto->DatPhotosetPhotoRelation->find('list');
// 		$this->set(compact('datUsers', 'mstImageServers', 'datPhotosetPhotoRelations'));

		// 返り値のデフォルトセット：false
		$this->set('datPhoto', false);

		// リクエストメソッド判断
		if ($this->request->is('post')) {

			/* TODO:$_FILEデータ受信処理 */

			// リクエストデータをJSON形式にエンコードで取得する
			$data = $this->request->input('json_decode');

			// TODO:$dataの内容を確認して動的にModelに値をセットできるようにする

			/* paramater set */
			$datPhoto['DatPhoto']['fk_user_id']				= $this->Auth->user('user_id');		// 会員ID:セッションより取得
			$datPhoto['DatPhoto']['fk_image_server_id']		= 1;								// TODO:対象の画像サーバのidを取得する
			$datPhoto['DatPhoto']['name']					= $data->photoName;					// 写真名
			$datPhoto['DatPhoto']['description']			= $data->description;				// 写真説明
			$datPhoto['DatPhoto']['file_name']				= '';								// TODO:画像の名前を決める
			$datPhoto['DatPhoto']['size']					= 100;								// TODO:画像のサイズを取得
			$datPhoto['DatPhoto']['type']					= 'jpg';							// TODO:画像のタイプを取得
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
			$data = $this->request->input('json_decode');

			// TODO:ここで$dataに格納されているパラメータの確認をして、$datAlbum配列に格納されているパラメータのみ格納するメソッドを書いて動的なパラメータでupdateAllに渡すようにする

			/* paramater set */
			$datPhoto;
			$datAlbum['DatPhoto']['photo_id']			= $id;
			$datAlbum['DatPhoto']['fk_user_id']			= $this->Auth->user('user_id');		// 会員ID:セッションより取得
			$datPhoto['DatPhoto']['name']				= $data->photoName;
// 			$datPhoto['DatPhoto']['description']		= $data->description;;
// 			$datPhoto['DatPhoto']['status']				= $data->status;
			$datPhoto['DatPhoto']['update_timestamp']		= date('Y-m-d h:i:s');


			// Modelに値をセット
			$this->DatPhoto->set($datPhoto);

			// バリデーションチェック
			if ($this->DatPhoto->validates()) {

				// TODO:update fieldを動的に設定


				/* バリデーション通過 */

				/* update query */
				$result = $this->DatAlbum->updateAll(
						// Update set
						array(
								'DatPhoto.name'				=> "'".$datPhoto['DatPhoto']['name']."'",
// 								'DatPhoto.description'		=> "'".$datPhoto['DatPhoto']['description']."'",
// 								'DatPhoto.status'			=> $datPhoto['DatPhoto']['status']."'",
								'DatPhoto.update_timestamp'	=> "'".$datPhoto['DatPhoto']['update_timestamp']."'",
						)
						// Where
						,array(
								array(
										'DatPhoto.photo_id' => $datAlbum['DatPhoto']['photo_id'],
										'DatPhoto.fk_user_id' => $datAlbum['DatPhoto']['fk_user_id'],
								)
						)
				);
				$this->set('datPhoto', $result);
			}
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
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->DatPhoto->id = $id;
		if (!$this->DatPhoto->exists()) {
			throw new NotFoundException(__('Invalid dat photo'));
		}
// 		if ($this->DatPhoto->delete()) {
// 			$this->Session->setFlash(__('Dat photo deleted'));
// 			$this->redirect(array('action' => 'index'));
// 		}
// 		$this->Session->setFlash(__('Dat photo was not deleted'));
// 		$this->redirect(array('action' => 'index'));


		/* paramater set */
		$datPhoto['DatPhoto']['photo_id'] = $id;
		$datPhoto['DatPhoto']['status']  = 0;
		$datPhoto['DatPhoto']['update_timestamp']  = date('Y-m-d h:i:s');

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
		$this->set('_serialize', 'datPhoto');
	}
}
