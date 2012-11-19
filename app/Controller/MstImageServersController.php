<?php
App::uses('AppController', 'Controller');
/**
 * MstImageServers Controller
 *
 * @property MstImageServer $MstImageServer
 */
class MstImageServersController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->MstImageServer->recursive = 0;
		$this->set('mstImageServers', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->MstImageServer->id = $id;
		if (!$this->MstImageServer->exists()) {
			throw new NotFoundException(__('Invalid mst image server'));
		}
		$this->set('mstImageServer', $this->MstImageServer->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->MstImageServer->create();
			if ($this->MstImageServer->save($this->request->data)) {
				$this->Session->setFlash(__('The mst image server has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The mst image server could not be saved. Please, try again.'));
			}
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
		$this->MstImageServer->id = $id;
		if (!$this->MstImageServer->exists()) {
			throw new NotFoundException(__('Invalid mst image server'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->MstImageServer->save($this->request->data)) {
				$this->Session->setFlash(__('The mst image server has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The mst image server could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->MstImageServer->read(null, $id);
		}
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
		$this->MstImageServer->id = $id;
		if (!$this->MstImageServer->exists()) {
			throw new NotFoundException(__('Invalid mst image server'));
		}
		if ($this->MstImageServer->delete()) {
			$this->Session->setFlash(__('Mst image server deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Mst image server was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
