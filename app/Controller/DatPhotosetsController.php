<?php
App::uses('AppController', 'Controller');
/**
 * DatPhotosets Controller
 *
 * @property DatPhotoset $DatPhotoset
 */
class DatPhotosetsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->DatPhotoset->recursive = 0;
		$this->set('datPhotosets', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->DatPhotoset->id = $id;
		if (!$this->DatPhotoset->exists()) {
			throw new NotFoundException(__('Invalid dat photoset'));
		}
		$this->set('datPhotoset', $this->DatPhotoset->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
// 		if ($this->request->is('post')) {
// 			$this->DatPhotoset->create();
// 			if ($this->DatPhotoset->save($this->request->data)) {
// 				$this->Session->setFlash(__('The dat photoset has been saved'));
// 				$this->redirect(array('action' => 'index'));
// 			} else {
// 				$this->Session->setFlash(__('The dat photoset could not be saved. Please, try again.'));
// 			}
// 		}
// 		$datUsers = $this->DatPhotoset->DatUser->find('list');
// 		$datPhotosetPhotoRelations = $this->DatPhotoset->DatPhotosetPhotoRelation->find('list');
// 		$this->set(compact('datUsers', 'datPhotosetPhotoRelations'));

		/* paramater set */
		$datPhotosets['DatPhotoset']['fk_user_id'] = 1;
		$datPhotosets['DatPhotoset']['name']  = '';						// $this->params['data']['name'];
		$datPhotosets['DatPhotoset']['description']  = '';				// $this->params['data']['description'];
		$datPhotosets['DatPhotoset']['flg']  = 1;
		$datPhotosets['DatPhotoset']['status']  = 1;
		$datPhotosets['DatPhotoset']['create_datetime']  = date('Y-m-d h:i:s');
		$datPhotosets['DatPhotoset']['update_timestamp']  = date('Y-m-d h:i:s');

		/* insert query */
		$this->DatPhotoset->create();
		if ($this->DatPhotoset->save($datPhotosets)) {
			/* get insert new id */
			$datPhotosets['DatPhotoset']['photoset_id'] = $this->DatPhotoset->id;

			$this->set('datPhotosets', $datPhotosets);
			$this->set('_serialize', 'datPhotosets');
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
		$this->DatPhotoset->id = $id;
		if (!$this->DatPhotoset->exists()) {
			throw new NotFoundException(__('Invalid dat photoset'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->DatPhotoset->save($this->request->data)) {
				$this->Session->setFlash(__('The dat photoset has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The dat photoset could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->DatPhotoset->read(null, $id);
		}
		$datUsers = $this->DatPhotoset->DatUser->find('list');
		$datPhotosetPhotoRelations = $this->DatPhotoset->DatPhotosetPhotoRelation->find('list');
		$this->set(compact('datUsers', 'datPhotosetPhotoRelations'));
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
// 		$this->DatPhotoset->id = $id;
// 		if (!$this->DatPhotoset->exists()) {
// 			throw new NotFoundException(__('Invalid dat photoset'));
// 		}
// 		if ($this->DatPhotoset->delete()) {
// 			$this->Session->setFlash(__('Dat photoset deleted'));
// 			$this->redirect(array('action' => 'index'));
// 		}
// 		$this->Session->setFlash(__('Dat photoset was not deleted'));
// 		$this->redirect(array('action' => 'index'));

		/* paramater set */
		$datPhotosets['DatPhotoset']['photoset_id'] = $id;
		$datPhotosets['DatPhotoset']['status']  = 0;
		$datPhotosets['DatPhotoset']['update_timestamp']  = date('Y-m-d h:i:s');

		/* update query */
		$result = $this->DatPhotoset->updateAll(
				// Update set
				array(
						'DatPhotoset.status' => $datPhotosets['DatPhotoset']['status']
						,'DatPhotoset.update_timestamp' => "'".$datPhotosets['DatPhotoset']['update_timestamp']."'"
				)
				// Where
				,array(
						array(
								'DatPhotoset.photoset_id' => $datPhotosets['DatPhotoset']['photoset_id']
						)
				)
		);

		$this->set('datPhotosets', $result);
		$this->set('_serialize', 'datPhotosets');
	}
}
