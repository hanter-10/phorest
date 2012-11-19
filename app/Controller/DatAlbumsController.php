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
	public $components = array('RequestHandler');

/**
 * index method
 *
 * @return void
 */
	public function index() {

		//$this->DatAlbum->recursive = 0;					HABTMの際に関連テーブルを検索するので削除
		$datAlbums = $this->DatAlbum->find('all');
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
 * @return void
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

		/* paramater set */
		$datAlbum['DatAlbum']['fk_user_id']			= $this->params['data']['user_id'];		// TODO:セッションより取得する
		$datAlbum['DatAlbum']['name']				= $this->params['data']['name'];
		$datAlbum['DatAlbum']['description']		= $this->params['data']['description'];
		$datAlbum['DatAlbum']['flg']				= 0;		// デフォルトは非公開
		$datAlbum['DatAlbum']['status']				= 1;		// デフォルトは有効
		$datAlbum['DatAlbum']['create_datetime']	= date('Y-m-d h:i:s');
		$datAlbum['DatAlbum']['update_timestamp']	= date('Y-m-d h:i:s');

		/* insert query */
		$this->DatAlbum->create();
		if ($this->DatAlbum->save($datAlbum)) {
			/* get insert new id */
			$datAlbum['DatAlbum']['album_id'] = $this->DatAlbum->id;

			$this->set('datAlbum', $datAlbum);
			$this->set('_serialize', 'datAlbum');
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->DatAlbum->id = $id;
		if (!$this->DatAlbum->exists()) {
			// Not Data
			throw new NotFoundException(__('Invalid dat album'));
		}

		$this->set('datAlbum', false);
		if ($this->request->is('put')) {

			/* paramater set */
			$datAlbum['DatAlbum']['album_id']			= $id;
			$datAlbum['DatAlbum']['name']				= $this->params['data']['name'];
			$datAlbum['DatAlbum']['description']		= $this->params['data']['description'];
			$datAlbum['DatAlbum']['flg']				= $this->params['data']['flg'];
			$datAlbum['DatAlbum']['status']				= $this->params['data']['status'];
			$datAlbum['DatAlbum']['update_timestamp']	= date('Y-m-d h:i:s');

			/* update query */
			$result = $this->DatAlbum->updateAll(
					// Update set
					array(
							'DatAlbum.name'				=> $datAlbum['DatAlbum']['name'],
							'DatAlbum.description'		=> $datAlbum['DatAlbum']['description'],
							'DatAlbum.flg'				=> $datAlbum['DatAlbum']['flg'],
							'DatAlbum.status'			=> $datAlbum['DatAlbum']['status'],
							'DatAlbum.update_timestamp'	=> "'".$datAlbum['DatAlbum']['update_timestamp']."'",
					)
					// Where
					,array(
							array(
									'DatAlbum.album_id' => $datAlbum['DatAlbum']['album_id']
							)
					)
			);
			$this->set('datAlbum', $result);

		} else {
			// Bad Request
			$this->request->data = $this->DatAlbum->read(null, $id);
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
// 		if (!$this->request->is('post')) {
// 			throw new MethodNotAllowedException();
// 		}
// 		$this->DatAlbum->id = $id;
// 		if (!$this->DatAlbum->exists()) {
// 			throw new NotFoundException(__('Invalid dat album'));
// 		}
// 		if ($this->DatAlbum->delete()) {
// 			$this->Session->setFlash(__('Dat album deleted'));
// 			$this->redirect(array('action' => 'index'));
// 		}
// 		$this->Session->setFlash(__('Dat album was not deleted'));
// 		$this->redirect(array('action' => 'index'));

		/* paramater set */
		$datAlbum['DatAlbum']['album_id'] = $id;
		$datAlbum['DatAlbum']['status']  = 0;
		$datAlbum['DatAlbum']['update_timestamp']  = date('Y-m-d h:i:s');

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
		$this->set('_serialize', 'datAlbum');
	}
}
