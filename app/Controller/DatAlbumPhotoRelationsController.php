<?php
App::uses('AppController', 'Controller');
/**
 * DatAlbumPhotoRelations Controller
 *
 * @property DatAlbumPhotoRelation $DatAlbumPhotoRelation
 */
class DatAlbumPhotoRelationsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$group = array('group' => 'DatAlbum.album_id');
		$conditions = array("DatAlbum.album_id" => 1);

		$this->DatAlbumPhotoRelation->recursive = 0;
		$datAlbumPhotoRelations = $this->DatAlbumPhotoRelation->find('all');
// 		$datAlbumPhotoRelations = $this->DatAlbumPhotoRelation->find('threaded', $group);
		$this->set('datAlbumPhotoRelations', $datAlbumPhotoRelations);

		// JsonViewは”_serialize”という名前で配列(array)を設定するとそれをJSONとして出力してくれる
		$this->set('_serialize', 'datAlbumPhotoRelations');
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {

		$this->DatAlbumPhotoRelation->recursive = 0;
// 		$this->DatAlbumPhotoRelation->id = $id;
// 		if (!$this->DatAlbumPhotoRelation->exists()) {
// 			throw new NotFoundException(__('Invalid dat album photo relation'));
// 		}
// 		$datAlbumPhotoRelations = $this->DatAlbumPhotoRelation->read(null, $id);

		$datAlbumPhotoRelations = $this->DatAlbumPhotoRelation->find(
			'first'
			,array('conditions' => array('album_id' => $id))
		);
		$this->set('datAlbumPhotoRelations', $datAlbumPhotoRelations);
		$this->set('_serialize', 'datAlbumPhotoRelations');
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
// 		if ($this->request->is('post')) {
// 			$this->DatAlbumPhotoRelation->create();
// 			if ($this->DatAlbumPhotoRelation->save($this->request->data)) {
// 				$this->Session->setFlash(__('The dat album photo relation has been saved'));
// 				$this->redirect(array('action' => 'index'));
// 			} else {
// 				$this->Session->setFlash(__('The dat album photo relation could not be saved. Please, try again.'));
// 			}
// 		}
// 		$datAlbums = $this->DatAlbumPhotoRelation->DatAlbum->find('list');
// 		$datPhotos = $this->DatAlbumPhotoRelation->DatPhoto->find('list');
// 		$this->set(compact('datAlbums', 'datPhotos'));



		/* paramater set */
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_album_id'] = 1;				// $this->params['data']['album_id'];
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_photo_id']  = 10;				// $this->params['data']['photo_id'];
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['status']  = 1;
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['create_datetime']  = date('Y-m-d h:i:s');
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['update_timestamp']  = date('Y-m-d h:i:s');

		/* insert query */
		$this->DatAlbumPhotoRelation->create();
		$this->DatAlbumPhotoRelation->save($datAlbumPhotoRelations);

		/* get insert new id */
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['album_photo_relation_id'] = $this->DatAlbumPhotoRelation->id;

		$this->set('datAlbumPhotoRelations', $datAlbumPhotoRelations);
		$this->set('_serialize', 'datAlbumPhotoRelations');
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->DatAlbumPhotoRelation->id = $id;
		if (!$this->DatAlbumPhotoRelation->exists()) {
			throw new NotFoundException(__('Invalid dat album photo relation'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->DatAlbumPhotoRelation->save($this->request->data)) {
				$this->Session->setFlash(__('The dat album photo relation has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The dat album photo relation could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->DatAlbumPhotoRelation->read(null, $id);
		}
		$datAlbums = $this->DatAlbumPhotoRelation->DatAlbum->find('list');
		$datPhotos = $this->DatAlbumPhotoRelation->DatPhoto->find('list');
		$this->set(compact('datAlbums', 'datPhotos'));
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
// 		$this->DatAlbumPhotoRelation->id = $id;
// 		if (!$this->DatAlbumPhotoRelation->exists()) {
// 			throw new NotFoundException(__('Invalid dat album photo relation'));
// 		}
// 		if ($this->DatAlbumPhotoRelation->delete()) {
// 			$this->Session->setFlash(__('Dat album photo relation deleted'));
// 			$this->redirect(array('action' => 'index'));
// 		}
// 		$this->Session->setFlash(__('Dat album photo relation was not deleted'));
// 		$this->redirect(array('action' => 'index'));



		/* paramater set */
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_album_id'] = $id;				// $this->params['data']['album_id'];
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_photo_id']  = 10;				// $this->params['data']['photo_id'];
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['status']  = 0;
		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['update_timestamp']  = date('Y-m-d h:i:s');

		/* update query */
		$result = $this->DatAlbumPhotoRelation->updateAll(
				// Update set
				array(
					'DatAlbumPhotoRelation.status' => $datAlbumPhotoRelations['DatAlbumPhotoRelation']['status']
					,'DatAlbumPhotoRelation.update_timestamp' => "'".$datAlbumPhotoRelations['DatAlbumPhotoRelation']['update_timestamp']."'"
				)
				// Where
				,array(
					array(
						'DatAlbumPhotoRelation.fk_album_id' => $datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_album_id']
						,'DatAlbumPhotoRelation.fk_photo_id' => $datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_photo_id']
					)
				)
		);

		$this->set('datAlbumPhotoRelations', $result);
		$this->set('_serialize', 'datAlbumPhotoRelations');
	}
}
