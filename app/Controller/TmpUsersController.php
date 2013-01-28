<?php
App::uses('AppController', 'Controller');

/**
 * TmpUsers Controller
 *
 * @property TmpUser $TmpUser
 */
class TmpUsersController extends AppController {

	public $uses = array('TmpUser');

	// ログインなしでアクセス可能なページ
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index','view','add','sign_up');
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->layout = '_home_layout';

		$this->TmpUser->recursive = 0;
		$this->set('TmpUsers', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->TmpUser->id = $id;
		if (!$this->TmpUser->exists()) {
			throw new NotFoundException(__('Invalid dat user'));
		}
		$this->set('TmpUser', $this->TmpUser->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		try
		{
			if ($this->request->is('post')) {
				$this->request->data['TmpUser']['create_datetime'] = date('Y-m-d h:i:s');
				var_dump($this->request->data);

				var_dump(Security::rijndael($this->request->data['TmpUser']['tmp_email'], Configure::read('Security.key'), 'encrypt'));
				$encrypt = Security::rijndael($this->request->data['TmpUser']['tmp_email'], Configure::read('Security.key'), 'encrypt');
				var_dump(Security::rijndael($encrypt, Configure::read('Security.key'), 'decrypt'));
				exit;
				$this->TmpUser->create();
				if ($this->TmpUser->save($this->request->data)) {

				} else {
					// TODO:バリデーションとかその辺ハンドリングしなきゃ
					$this->redirect($this->Auth->logout());
// 					$this->Session->setFlash(__('The dat user could not be saved. Please, try again.'));
				}
			}
		} catch (Exception $e) {
			// TODO:SQL ERRORとかその辺ハンドリングしなきゃ
			$this->redirect($this->Auth->logout());
		}
	}

	public function sign_up(){
		var_dump($this->request->data);
		$this->layout = 'sign_up_layout';
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
		$this->TmpUser->id = $id;
		if (!$this->TmpUser->exists()) {
			throw new NotFoundException(__('Invalid dat user'));
		}
		if ($this->TmpUser->delete()) {
			$this->Session->setFlash(__('Dat user deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Dat user was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
