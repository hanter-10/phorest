<?php
App::uses('AppController', 'Controller');
/**
 * DatPhotosetPhotoRelations Controller
 *
 * @property DatPhotosetPhotoRelation $DatPhotosetPhotoRelation
 */
class DatPhotosetPhotoRelationsController extends AppController {

	public $name = 'DatPhotosetPhotoRelations';

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->DatPhotosetPhotoRelation->recursive = 0;
		$datPhotosetPhotoRelations = $this->DatPhotosetPhotoRelation->find('all');
		$this->set('datPhotosetPhotoRelations', $datPhotosetPhotoRelations);

		// JsonViewは”_serialize”という名前で配列(array)を設定するとそれをJSONとして出力してくれる
		$this->set('_serialize', 'datPhotosetPhotoRelations');
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {

		$this->DatPhotosetPhotoRelation->recursive = 0;
// 		$this->DatPhotosetPhotoRelation->id = $id;
// 		if (!$this->DatPhotosetPhotoRelation->exists()) {
// 			throw new NotFoundException(__('Invalid dat photoset photo relation'));
// 		}
// 		$datPhotosetPhotoRelations = $this->DatPhotosetPhotoRelation->read(null, $id);

		$datPhotosetPhotoRelations = $this->DatPhotosetPhotoRelation->find(
			'first'
			,array('conditions' => array('photoset_id' => $id))
		);
		$this->set('datPhotosetPhotoRelations', $datPhotosetPhotoRelations);
		$this->set('_serialize', 'datPhotosetPhotoRelations');
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
// 		if ($this->request->is('post')) {
// 			$this->DatPhotosetPhotoRelation->create();
// 			if ($this->DatPhotosetPhotoRelation->save($this->request->data)) {
// 				$this->Session->setFlash(__('The dat photoset photo relation has been saved'));
// 				$this->redirect(array('action' => 'index'));
// 			} else {
// 				$this->Session->setFlash(__('The dat photoset photo relation could not be saved. Please, try again.'));
// 			}
// 		}
// 		$datPhotosets = $this->DatPhotosetPhotoRelation->DatPhotoset->find('list');
// 		$datPhotos = $this->DatPhotosetPhotoRelation->DatPhoto->find('list');
// 		$this->set(compact('datPhotosets', 'datPhotos'));



		/* paramater set */
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['fk_photoset_id'] = 1;				// $this->params['data']['photoset_id'];
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['fk_photo_id']  = 10;				// $this->params['data']['photo_id'];
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['status']  = 1;
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['create_datetime']  = date('Y-m-d h:i:s');
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['update_timestamp']  = date('Y-m-d h:i:s');

		/* insert query */
		$this->DatPhotosetPhotoRelation->create();
		$this->DatPhotosetPhotoRelation->save($datPhotosetPhotoRelations);

		/* get insert new id */
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['photoset_photo_relation_id'] = $this->DatPhotosetPhotoRelation->id;

		$this->set('datPhotosetPhotoRelations', $datPhotosetPhotoRelations);
		$this->set('_serialize', 'datPhotosetPhotoRelations');
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->DatPhotosetPhotoRelation->id = $id;
		if (!$this->DatPhotosetPhotoRelation->exists()) {
			throw new NotFoundException(__('Invalid dat photoset photo relation'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->DatPhotosetPhotoRelation->save($this->request->data)) {
				$this->Session->setFlash(__('The dat photoset photo relation has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The dat photoset photo relation could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->DatPhotosetPhotoRelation->read(null, $id);
		}
		$datPhotosets = $this->DatPhotosetPhotoRelation->DatPhotoset->find('list');
		$datPhotos = $this->DatPhotosetPhotoRelation->DatPhoto->find('list');
		$this->set(compact('datPhotosets', 'datPhotos'));
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
// 		$this->DatPhotosetPhotoRelation->id = $id;
// 		if (!$this->DatPhotosetPhotoRelation->exists()) {
// 			throw new NotFoundException(__('Invalid dat photoset photo relation'));
// 		}
// 		if ($this->DatPhotosetPhotoRelation->delete()) {
// 			$this->Session->setFlash(__('Dat photoset photo relation deleted'));
// 			$this->redirect(array('action' => 'index'));
// 		}
// 		$this->Session->setFlash(__('Dat photoset photo relation was not deleted'));
// 		$this->redirect(array('action' => 'index'));



		/* paramater set */
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['fk_photoset_id'] = $id;				// $this->params['data']['photoset_id'];
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['fk_photo_id']  = 10;				// $this->params['data']['photo_id'];
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['status']  = 0;
		$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['update_timestamp']  = date('Y-m-d h:i:s');

		/* update query */
		$result = $this->DatPhotosetPhotoRelation->updateAll(
				// Update set
				array(
					'DatPhotosetPhotoRelation.status' => $datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['status']
					,'DatPhotosetPhotoRelation.update_timestamp' => "'".$datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['update_timestamp']."'"
				)
				// Where
				,array(
					array(
						'DatPhotosetPhotoRelation.fk_photoset_id' => $datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['fk_photoset_id']
						,'DatPhotosetPhotoRelation.fk_photo_id' => $datPhotosetPhotoRelations['DatPhotosetPhotoRelation']['fk_photo_id']
					)
				)
		);

		$this->set('datPhotosetPhotoRelations', $result);
		$this->set('_serialize', 'datPhotosetPhotoRelations');
	}
}
