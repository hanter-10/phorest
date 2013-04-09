<?php
App::uses('AppController', 'Controller');
/**
 * DatAlbumPhotoRelations Controller
 *
 * @property DatAlbumPhotoRelation $DatAlbumPhotoRelation
 */
class DatAlbumPhotoRelationsController extends AppController {

	public $viewClass = 'Json';
	public $components = array('RequestHandler','Convert','Check');

	public $uses = array('DatAlbum','DatPhoto','DatAlbumPhotoRelation');

	function beforeFilter() {
		// 親クラスをロード
		parent::beforeFilter();

		// ajax通信でのアクセスか確認
		if ( ! $this->RequestHandler->isAjax() ) {
			// ajaxではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
	}

/**
 * index method
 *
 * @return void
 */
// 	public function index() {

// 		$group = array('group' => 'DatAlbum.album_id');
// 		$conditions = array("DatAlbum.album_id" => 1);

// 		$this->DatAlbumPhotoRelation->recursive = 0;
// 		$datAlbumPhotoRelations = $this->DatAlbumPhotoRelation->find('all');
// // 		$datAlbumPhotoRelations = $this->DatAlbumPhotoRelation->find('threaded', $group);
// 		$this->set('datAlbumPhotoRelations', $datAlbumPhotoRelations);

// 		// JsonViewは”_serialize”という名前で配列(array)を設定するとそれをJSONとして出力してくれる
// 		$this->set('_serialize', 'datAlbumPhotoRelations');
// 	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
// 	public function view($id = null) {

// 		$this->DatAlbumPhotoRelation->recursive = 0;
// // 		$this->DatAlbumPhotoRelation->id = $id;
// // 		if (!$this->DatAlbumPhotoRelation->exists()) {
// // 			throw new NotFoundException(__('Invalid dat album photo relation'));
// // 		}
// // 		$datAlbumPhotoRelations = $this->DatAlbumPhotoRelation->read(null, $id);

// 		$datAlbumPhotoRelations = $this->DatAlbumPhotoRelation->find(
// 			'first'
// 			,array('conditions' => array('album_id' => $id))
// 		);
// 		$this->set('datAlbumPhotoRelations', $datAlbumPhotoRelations);
// 		$this->set('_serialize', 'datAlbumPhotoRelations');
// 	}

/**
 * add method
 * [PUT/undefined]アルバムに写真を紐づけるAPI
 * @return void
 */
	public function add() {

		// 返り値のデフォルトセット：false
		$this->set('datAlbumPhotoRelations', false);

		// リクエストメソッド判断
		if ($this->request->is('put')) {

			// リクエストデータをJSON形式にエンコードで取得する
			$data = $this->request->input('json_decode');

			// 選択された写真毎に実行
			foreach ( $data->photos as $key => $photo_id ) {

				if( $photo_id == null ) {
					throw new BadRequestException(__('Bad Request.'));
				}

				$this->DatAlbumPhotoRelation->create();
				$this->DatAlbumPhotoRelation->set( 'fk_album_id', $data->targetAlbum );
				$this->DatAlbumPhotoRelation->set( 'fk_photo_id', $photo_id );
				$this->DatAlbumPhotoRelation->set( 'status', STATUS_ON );
				$this->DatAlbumPhotoRelation->set( 'create_datetime', date('Y-m-d h:i:s') );
				$this->DatAlbumPhotoRelation->set( 'update_timestamp', date('Y-m-d h:i:s') );

				if ( $this->DatAlbumPhotoRelation->validates() ) {

					if ( $this->DatAlbumPhotoRelation->save() ) {
						$this->set( 'datAlbumPhotoRelations', true );
					}
				}
				else {
					// 失敗したらfalseを返す
					$this->set( 'datAlbumPhotoRelations', array( 'errorMsg' => '写真追加に失敗しました。画面を更新して写真が追加されているか確認してください。' ) );
				}
			}
		}
		else {
			// putではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
		$this->set('_serialize', 'datAlbumPhotoRelations');
	}

/**
 * edit method
 *
 * [PUT/id]アルバムと写真の紐づき情報の更新API
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		// 返り値のデフォルトセット：false
		$this->set( 'datAlbumPhotoRelation', false );

		// from Album_id Check
		$this->DatAlbum->id = $id;
		if ( ! $this->DatAlbum->exists() ) {
			// No Data
			throw new NotFoundException(__('Invalid dat album photo relation'));
		}

		// リクエストメソッド判断
		if ( $this->request->is('put') ) {

			// リクエストデータをJSON形式にエンコードで取得する
			$requestData = $this->request->input('json_decode');

			$result = false;
			foreach ( $requestData->photos as $key => $photo_id ) {

				$this->DatAlbumPhotoRelation->set( 'fk_album_id', $requestData->targetAlbum );
				$this->DatAlbumPhotoRelation->set( 'fk_photo_id', $photo_id );

				if ( $this->DatAlbumPhotoRelation->validates() ) {
					// 更新処理
					$result[$key] = $this->DatAlbumPhotoRelation->updateAlbumPhotoRelationByAlbumId( $requestData->targetAlbum, $id, $photo_id );
				}
				else {
					// バリデーションエラーの場合
					$result[$key] = array( 'errorMsg' => '写真追加に失敗しました。画面を更新して写真が追加されているか確認してください。' );
				}
			}
			$this->set('datAlbumPhotoRelation', $result);
		}
		else {
			// putではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
		$this->set('_serialize', 'datAlbumPhotoRelation');
	}

// /**
//  * delete method
//  *
//  * @throws MethodNotAllowedException
//  * @throws NotFoundException
//  * @param string $id
//  * @return void
//  */
// 	public function delete($id = null) {
// // 		if (!$this->request->is('post')) {
// // 			throw new MethodNotAllowedException();
// // 		}
// // 		$this->DatAlbumPhotoRelation->id = $id;
// // 		if (!$this->DatAlbumPhotoRelation->exists()) {
// // 			throw new NotFoundException(__('Invalid dat album photo relation'));
// // 		}
// // 		if ($this->DatAlbumPhotoRelation->delete()) {
// // 			$this->Session->setFlash(__('Dat album photo relation deleted'));
// // 			$this->redirect(array('action' => 'index'));
// // 		}
// // 		$this->Session->setFlash(__('Dat album photo relation was not deleted'));
// // 		$this->redirect(array('action' => 'index'));

// 		/* paramater set */
// 		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_album_id'] = $id;				// $this->params['data']['album_id'];
// 		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_photo_id']  = 10;				// $this->params['data']['photo_id'];
// 		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['status']  = 0;
// 		$datAlbumPhotoRelations['DatAlbumPhotoRelation']['update_timestamp']  = date('Y-m-d h:i:s');

// 		/* update query */
// 		$result = $this->DatAlbumPhotoRelation->updateAll(
// 				// Update set
// 				array(
// 					'DatAlbumPhotoRelation.status' => $datAlbumPhotoRelations['DatAlbumPhotoRelation']['status']
// 					,'DatAlbumPhotoRelation.update_timestamp' => "'".$datAlbumPhotoRelations['DatAlbumPhotoRelation']['update_timestamp']."'"
// 				)
// 				// Where
// 				,array(
// 					array(
// 						'DatAlbumPhotoRelation.fk_album_id' => $datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_album_id']
// 						,'DatAlbumPhotoRelation.fk_photo_id' => $datAlbumPhotoRelations['DatAlbumPhotoRelation']['fk_photo_id']
// 					)
// 				)
// 		);

// 		$this->set('datAlbumPhotoRelations', $result);
// 		$this->set('_serialize', 'datAlbumPhotoRelations');
// 	}
}
