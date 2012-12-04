<?php
App::uses('AppController', 'Controller');
/**
 * DatAlbums Controller
 *
 * @property DatAlbum $DatAlbum
 */
class DatAlbumsController extends AppController {

/**
 * [ Phorest ] :
 */
	public $viewClass = 'Json';
	public $components = array('RequestHandler','Convert');

	function beforeFilter() {
		// 親クラスをロード
		parent::beforeFilter();

		// ajax通信でのアクセスか確認
		if (!$this->RequestHandler->isAjax()) {
			// ajaxではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}

// 		if (isset($this->Auth->user('user_id'))) {
// 			// 会員以外の操作の場合は「400 Bad Request」
// 			throw new BadRequestException(__('Bad Request.'));
// 		}
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		//$this->DatAlbum->recursive = 0;					HABTMの際に関連テーブルを検索するので削除

		/* 検索項目 */
		$fields = array(
				'DatAlbum.album_id as id',
				'DatAlbum.name as albumName',
				'DatAlbum.description',
				'DatAlbum.flg',
				'DatAlbum.status',
				'DatAlbum.create_datetime',
				'DatAlbum.update_timestamp',
		);
		$contain = array(
				'DatPhoto' => array(
					'MstImageServer' => array(
						'fields' => array(
							'image_server_id',
							'grobal_ip',
							'file_path'
						),
						'conditions' => array(
							'MstImageServer.status' => 1,
						),
					),
					'fields' => array(
						'photo_id as id',
						'name as photoName',
						'description',
						'file_name',
						'thum_file_name',
						'size',
						'type',
						'status',
						'create_datetime',
						'update_timestamp',
					),
					'conditions' => array(
						'DatPhoto.status' => 1,
					),
				),
		);

		$this->DatAlbum->Behaviors->attach('Containable');
		$option = array(
				'fields' => $fields,
				'contain' => $contain,
		);

		$datAlbums = $this->DatAlbum->find('all', $option);

		// TODO:データ入れ替え処理 もっと良いやり方があるはず・・・
		foreach ( $datAlbums as $albumkey => $datAlbum ) {

			foreach ( $datAlbum['DatPhoto'] as $photoKey => $datPhoto) {
				// オリジナル写真
				$datAlbums[$albumkey]['DatPhoto'][$photoKey]['imgUrl'] = 'http://' . $datPhoto['MstImageServer']['grobal_ip'] . $datPhoto['MstImageServer']['file_path'] . $this->Auth->user('username') . '/' . $datAlbum['DatAlbum']['id'] . '/' . $datPhoto['file_name'];
				// サムネイル写真
// 				$datAlbums[$albumkey]['DatPhoto'][$photoKey]['thumUrl'] = 'http://' . $datPhoto['MstImageServer']['grobal_ip'] . $datPhoto['MstImageServer']['file_path'] . $this->Auth->user('username') . '/' . $datAlbum['DatAlbum']['id'] . '/' . $datPhoto['thum_file_name'];
				$datAlbums[$albumkey]['DatPhoto'][$photoKey]['thumUrl'] = 'http://' . $datPhoto['MstImageServer']['grobal_ip'] . $datPhoto['MstImageServer']['file_path'] . $this->Auth->user('username') . '/' . $datAlbum['DatAlbum']['id'] . '/' . $datPhoto['file_name'];

				// いらないものを消す
				unset($datAlbums[$albumkey]['DatPhoto'][$photoKey]['DatAlbumPhotoRelation']);
				unset($datAlbums[$albumkey]['DatPhoto'][$photoKey]['MstImageServer']);
			}
		}

		$this->set('datAlbums', $datAlbums);

		// JsonViewは”_serialize”という名前で配列(array)を設定するとそれをJSONとして出力してくれる
		$this->set('_serialize', 'datAlbums');
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {

		$this->DatAlbum->recursive = 0;		// アルバム情報のみ取得

		$this->DatAlbum->id = $id;
		if (!$this->DatAlbum->exists()) {
			// Not Data
			throw new NotFoundException(__('Invalid dat album'));
		}
		$this->set('datAlbum', $this->DatAlbum->read(null, $id));
		$this->set('_serialize', 'datAlbum');
	}

/**
 * add method
 *
 * @return 成功：追加したアルバム情報 / 失敗：false
 */
	public function add() {
// 		if ($this->request->is('post')) {
// 			$this->DatAlbum->create();
// 			if ($this->DatAlbum->save($this->request->data)) {
// 				$this->Session->setFlash(__('The dat album has been saved'));
// 				$this->redirect(array('action' => 'index'));
// 			} else {
// 				$this->Session->setFlash(__('The dat album could not be saved. Please, try again.'));
// 			}
// 		}
// 		$datUsers = $this->DatAlbum->DatUser->find('list');
// 		$datAlbumPhotoRelations = $this->DatAlbum->DatAlbumPhotoRelation->find('list');


// 		$this->set(compact('datUsers', 'datAlbumPhotoRelations'));

		// 返り値のデフォルトセット：false
		$this->set('datAlbum', false);

		// リクエストメソッド判断
		if ($this->request->is('post')) {

			// リクエストデータをJSON形式にエンコードで取得する
			$data = $this->request->input('json_decode');

			// TODO:$dataの内容を確認して動的にModelに値をセットできるようにする


			/* paramater set */
			$datAlbum = array();
			$datAlbum['DatAlbum']['fk_user_id']			= $this->Auth->user('user_id');		// 会員ID:セッションより取得
			$datAlbum['DatAlbum']['name']				= $data->albumName;					// アルバム名
			$datAlbum['DatAlbum']['description']		= $data->description;				// アルバム説明
			$datAlbum['DatAlbum']['flg']				= 0;								// デフォルトは非公開
			$datAlbum['DatAlbum']['status']				= 1;								// デフォルトは有効
			$datAlbum['DatAlbum']['create_datetime']	= date('Y-m-d h:i:s');
			$datAlbum['DatAlbum']['update_timestamp']	= date('Y-m-d h:i:s');

			// Modelに値をセット
			$this->DatAlbum->set($datAlbum);

			// バリデーションチェック
			if ($this->DatAlbum->validates()) {

				/* バリデーション通過 */

				/* insert query */
				$this->DatAlbum->create();
				if ($this->DatAlbum->save($datAlbum)) {
					/* get insert new id */
					$datAlbum['DatAlbum']['album_id'] = $this->DatAlbum->id;

					$this->set('datAlbum', $datAlbum);
				}
			}

			/* バリデーションエラー内容出力 */
			//var_dump($this->validateErrors($this->DatAlbum));
		} else {

			// postではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
		$this->set('_serialize', 'datAlbum');
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
		$this->set('datAlbum', false);

		$this->DatAlbum->id = $id;
		if (!$this->DatAlbum->exists()) {
			// Not Data
			throw new NotFoundException(__('Invalid dat album'));
		} else {

		}

		// リクエストメソッド判断
		if ($this->request->is('put')) {

			// リクエストデータをJSON形式にエンコードで取得する
			$requestData = $this->request->input('json_decode');

			// TODO:ここで$dataに格納されているパラメータの確認をして、$datAlbum配列に格納されているパラメータのみ格納するメソッドを書いて動的なパラメータでupdateAllに渡すようにする
			$optionData = array();

			/* 固定パラメータセット */
			$optionData = array(
						'album_id'			=> $id,
						'fk_user_id'		=> $this->Auth->user('user_id'),
// 						'flg'				=> ,
// 						'status'			=> ,
						'update_timestamp'	=> date('Y-m-d h:i:s'),
					);
			/* リクエストパラメータセット */
			$datAlbum = $this->Convert->doConvertObjectToArray($requestData, 'DatAlbum', $optionData);

// 			$datAlbum['DatAlbum']['album_id']			= $id;
// 			$datAlbum['DatAlbum']['fk_user_id']			= $this->Auth->user('user_id');		// 会員ID:セッションより取得
// 			$datAlbum['DatAlbum']['name']				= $data->albumName;
// 			$datAlbum['DatAlbum']['description']		= $data->description;
// 			$datAlbum['DatAlbum']['flg']				= $data->flg;
// 			$datAlbum['DatAlbum']['status']				= $data->status;
// 			$datAlbum['DatAlbum']['update_timestamp']	= date('Y-m-d h:i:s');




			// Modelに値をセット
			$this->DatAlbum->set($datAlbum);

			// バリデーションチェック
			if ($this->DatAlbum->validates()) {

				// TODO:update fieldを動的に設定


				/* バリデーション通過 */

				/* update query */
				$result = $this->DatAlbum->updateAll(
						// Update set
						array(
								'DatAlbum.name'				=> "'".$datAlbum['DatAlbum']['name']."'",
// 								'DatAlbum.description'		=> "'".$datAlbum['DatAlbum']['description']."'",
// 								'DatAlbum.flg'				=> $datAlbum['DatAlbum']['flg'],
// 								'DatAlbum.status'			=> $datAlbum['DatAlbum']['status'],
								'DatAlbum.update_timestamp'	=> "'".$datAlbum['DatAlbum']['update_timestamp']."'",
						)
						// Where
						,array(
								array(
										'DatAlbum.album_id' => $datAlbum['DatAlbum']['album_id'],
// 										'DatAlbum.fk_user_id' => $datAlbum['DatAlbum']['fk_user_id'],
								)
						)
				);
				$this->set('datAlbum', $result);
			}
		} else {

			// putではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
		$this->set('_serialize', 'datAlbum');

// 		$datUsers = $this->DatAlbum->DatUser->find('list');
// 		$datAlbumPhotoRelations = $this->DatAlbum->DatAlbumPhotoRelation->find('list');
// 		$this->set(compact('datUsers', 'datAlbumPhotoRelations'));
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
		$this->set('datAlbum', false);

		// idが存在するかチェック
		$this->DatAlbum->id = $id;
		if (!$this->DatAlbum->exists()) {
			throw new NotFoundException(__('Invalid dat album'));
		} else {

		}

		// リクエストメソッド判断
		if ($this->request->is('delete')) {

			/* paramater set */
			$datAlbum['DatAlbum']['album_id'] = $id;
			$datAlbum['DatAlbum']['status']  = 0;
			$datAlbum['DatAlbum']['update_timestamp']  = date('Y-m-d h:i:s');

			// Modelに値をセット
			$this->DatAlbum->set($datAlbum);

			// バリデーションチェック
			if ($this->DatAlbum->validates()) {

				/* バリデーション通過 */

				/* update query */
				$result = $this->DatAlbum->updateAll(
						// Update set
						array(
								'DatAlbum.status' => $datAlbum['DatAlbum']['status']
								,'DatAlbum.update_timestamp' => "'".$datAlbum['DatAlbum']['update_timestamp']."'"
						)
						// Where
						,array(
								array(
										'DatAlbum.album_id' => $datAlbum['DatAlbum']['album_id']
								)
						)
				);
				$this->set('datAlbum', $result);
			}
		} else {
			// deleteではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}


		$this->set('_serialize', 'datAlbum');
	}
}
