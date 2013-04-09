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
	public $components = array( 'RequestHandler','Convert','Check' );

	public $uses = array( 'DatAlbum','DatPhoto','DatUser','DatAlbumPhotoRelation','MstImageServer' );

	public function beforeFilter() {
		// 親クラスをロード
		parent::beforeFilter();
		$this->Auth->allow( 'userSearch' );

		// ajax通信でのアクセスか確認
		if ( ! $this->RequestHandler->isAjax() ) {
			// ajaxではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
	}

/**
 * index method
 *
 * [GET]アルバム情報とそれに紐づく写真情報とアルバムに紐づいていない写真情報一覧取得API
 * @throws BadRequestException
 * @return void
 */
	public function index() {

		//$this->DatAlbum->recursive = 0;			// HABTMの際に関連テーブルを検索するので削除
		$this->set( 'datAlbumPhotos', array( 'errorMsg' => 'データ取得に失敗しました。画面を更新して再度お試しください' ) );

		// リクエストメソッド判断
		if ( $this->request->is( 'get' ) ) {

			//TODO:ユーザー権限がないと実行できないようにもしなきゃ
			//TODO:リファラーチェックとかhash認証コードとかもしといたほうがいいんだろな。

			// 初期化
			$datAlbumPhotos = array();

			// 会員のアルバム情報取得
			$datAlbumPhotos = $this->DatAlbum->getAlbumDataByUserId( $this->Auth->user( 'user_id' ) );

			// アルバム情報分アルバムに紐づく写真情報を取得
			foreach ( $datAlbumPhotos as $album_key => $datAlbum ) {

				// 対象のアルバムの写真情報取得
				$datPhotos = $this->DatPhoto->getAlbumPhotoRelationByUserIdAlbumID( $this->Auth->user('user_id'), $datAlbum['DatAlbum']['id'] );

				// 配列構造調整
				foreach( $datPhotos as $photo_key => $datPhoto ) {

					// 値をセット
					$datAlbumPhotos[$album_key]['DatPhoto'][$photo_key]	= $datPhotos[$photo_key]['DatPhoto'];

					// いらないものを消す
					unset( $datPhotos[$photo_key]['DatPhoto'] );
				}
			}

			// アルバム以外の写真情報取得(いわゆるtempAlbum)
			$datTempPhotos = $this->DatPhoto->getTempAlbumPhotoRelationByUserID( $this->Auth->user( 'user_id' ) );

			// 配列構造調整
			foreach( $datTempPhotos as $key => $datTempPhoto ) {

				$datTempPhotos[$key]	= $datTempPhotos[$key]['DatPhoto'];

				// いらないものを消す
				unset( $datTempPhotos[$key]['DatPhoto'] );
			}

			$tempAlbums['DatAlbum'] = array( 'tempAlbum' => true );
			$tempAlbums['DatPhoto'] = $datTempPhotos;
			$datAlbumPhotos[] = $tempAlbums;

			// データセット
			$this->set( 'datAlbumPhotos', $datAlbumPhotos );

// 			// SQLクエリログの確認方法
// 			$log = $this->DatAlbum->getDataSource()->getLog(false, false);
// 			echo '<pre>';
// 			var_dump($log);
// 			echo '</pre>';

		} else {
			// getではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}

		// JSONレスポンス
		$this->set( '_serialize', 'datAlbumPhotos' );
// 		$this->set('_serialize', compact('datAlbums','datPhotos'));
	}

/**
 * view method
 * [GET/id]指定アルバム情報取得API
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {

		$this->DatAlbum->recursive = 0;		// アルバム情報のみ取得

		$this->DatAlbum->id = $id;
		if ( ! $this->DatAlbum->exists() ) {
			// Not Data
			throw new NotFoundException(__('Invalid dat album'));
		}
		$this->set( 'datAlbum', $this->DatAlbum->read( null, $id ) );
		$this->set( '_serialize', 'datAlbum' );
	}

/**
 * add method
 * [POST]アルバム追加API
 * @return 成功：追加したアルバム情報 / 失敗：false
 */
	public function add() {

		// 返り値のデフォルトセット：false
		$this->set( 'datAlbum', array( 'errorMsg' => 'アルバム追加に失敗しました。画面を更新して再度お試しください' ) );

		// リクエストメソッド判断
		if ( $this->request->is( 'post' ) ) {

			// リクエストデータをJSON形式にエンコードで取得する
			$request_data = $this->request->input('json_decode');

			$this->DatAlbum->create();
			$this->DatAlbum->set( 'albumName', $request_data->albumName );
			$this->DatAlbum->set( 'fk_user_id', $this->Auth->user('user_id') );
			$this->DatAlbum->set( 'public', STATUS_OFF );
			$this->DatAlbum->set( 'status', STATUS_ON );
			$this->DatAlbum->set( 'create_datetime', date('Y-m-d h:i:s') );
			$this->DatAlbum->set( 'create_datetime', date('Y-m-d h:i:s') );

			if ( $this->DatAlbum->validates() ) {

				// 追加
				$save_result = $this->DatAlbum->save();
				$save_result['id'] = $save_result['DatAlbum']['album_id'];
				$this->set( 'datAlbum', $save_result );
			}
		} else {

			// postではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
		$this->set('_serialize', 'datAlbum');
	}

/**
 * edit method
 * [PUT/id]指定アルバム情報更新API
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		// 返り値のデフォルトセット：false
		$this->set( 'datAlbum', array( 'errorMsg' => 'データ更新に失敗しました。画面を更新して再度お試しください' ) );

		$this->DatAlbum->id = $id;
		if ( ! $this->DatAlbum->exists() ) {
			// Not Data
			throw new NotFoundException(__('Invalid dat album'));
		}

		// リクエストメソッド判断
		if ( $this->request->is( 'put' ) || $this->request->is( 'patch' ) ) {

			// リクエストデータをJSON形式にエンコードで取得する
			$requestData = $this->request->input( 'json_decode' );

			// データセット
			$this->DatAlbum->create( false );
			$this->DatAlbum->set( 'album_id', $id );
			if ( isset ( $requestData->albumName ) ) $this->DatAlbum->set( 'albumName', $requestData->albumName );
			if ( isset ( $requestData->public ) ) $this->DatAlbum->set( 'public', $requestData->public );
			$this->DatAlbum->set( 'update_timestamp', date('Y-m-d h:i:s') );

			// バリデーションチェック
			if ( $this->DatAlbum->validates() ) {

				// 更新処理
				$this->DatAlbum->save();
				$this->set( 'datAlbum', true );
			}
			else {
				if ( isset( $this->DatAlbum->validationErrors['albumName'][0] ) ) {
					$this->set( 'datAlbum', array( 'errorMsg' => $this->DatAlbum->validationErrors['albumName'][0] ) );
				}
			}
		} else {
			// putではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}
		$this->set('_serialize', 'datAlbum');
	}

/**
 * delete method
 *
 * [DELETE/id]指定アルバム情報論理削除API
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {

		// 返り値のデフォルトセット：false
		$this->set( 'datAlbum', array( 'errorMsg' => 'データ更新に失敗しました。画面を更新して再度お試しください' ) );

		// idが存在するかチェック
		$this->DatAlbum->id = $id;
		if ( ! $this->DatAlbum->exists() ) {
			throw new NotFoundException(__('Invalid dat album'));
		}

		// リクエストメソッド判断
		if ( $this->request->is( 'delete' ) ) {

			$this->DatAlbum->create( false );
			$this->DatAlbum->set( 'album_id', $id );
			$this->DatAlbum->set( 'public', STATUS_OFF );
			$this->DatAlbum->set( 'status', STATUS_OFF );
			$this->DatAlbum->set( 'update_timestamp', date('Y-m-d h:i:s') );

			if ( $this->DatAlbum->validates() ) {

				// 更新
				$this->DatAlbum->save();
				$this->set( 'datAlbum', true );

			}
		} else {
			// deleteではない時は「400 Bad Request」
			throw new BadRequestException(__('Bad Request.'));
		}

		$this->set('_serialize', 'datAlbum');
	}

/**
 * userSearch method
 *
 * [GET]ユーザー情報と紐づく公開アルバム情報と紐づく写真情報取得API
 */
	public function userSearch () {

		try
		{
			// 返り値のデフォルトセット：false
			$this->set( 'datUserAlbums', array( 'errorMsg' => 'データ取得に失敗しました。画面を更新して再度お試しください' ) );

			// リクエストメソッド判断
			if ( $this->request->is( 'get' ) ) {

				// 初期化
				$datUserAlbums = array();

				// 会員情報取得
				$datUserAlbums[0] = $this->DatUser->getUserDataByUserName($this->request->username);

				// アルバム情報取得
				$datAlbums = $this->DatAlbum->getPublicAlbumDataByUserId($datUserAlbums[0]['DatUser']['user_id']);

				// 配列構造調整
				foreach ( $datAlbums as $album_key => $datAlbum ) {

					$datUserAlbums[0]['DatAlbum'][$album_key]	= $datAlbums[$album_key]['DatAlbum'];

					// アルバムに紐づく写真情報取得
					$datAlbumPhotos = $this->DatPhoto->getAlbumPhotoRelationByUserIdAlbumID($datUserAlbums[0]['DatUser']['user_id'], $datAlbum['DatAlbum']['id']);

					// 配列構造調整
					foreach ($datAlbumPhotos as $photo_key => $datAlbumPhoto) {

						$datUserAlbums[0]['DatAlbum'][$album_key]['DatPhoto'][$photo_key] = $datAlbumPhotos[$photo_key]['DatPhoto'];

						// いらないものを消す
						unset($datAlbumPhoto[$photo_key]);
					}

					if ( ! isset( $datUserAlbums[0]['DatAlbum'][$album_key]['DatPhoto'] ) ) {
						// 写真データがないアルバムはビューの対象外とする
						unset( $datUserAlbums[0]['DatAlbum'][$album_key] );
					}

					// いらないものを消す
					unset($datAlbums[$album_key]);
				}

				// データセット
				$this->set('datUserAlbums', $datUserAlbums);

			} else {
				// getではない時は「400 Bad Request」
				throw new BadRequestException(__('Bad Request.'));
			}

			// JSONレスポンス
			$this->set('_serialize', 'datUserAlbums');

		} catch (Exception $e) {

			$this->set( 'datUserAlbums', array( 'errorMsg' => 'データ取得に失敗しました。画面を更新して再度お試しください' ) );
			$this->set('_serialize', 'datUserAlbums');
		}
	}

/**
 * userSearch method
 *
 * [GET]ユーザー情報と紐づく公開アルバム情報と紐づく写真情報取得API
 */
	public function previewSearch() {

		try
		{
			// 返り値のデフォルトセット：false
			$this->set( 'datUserAlbums', array( 'errorMsg' => 'データ取得に失敗しました。画面を更新して再度お試しください' ) );

			// リクエストメソッド判断
			if ( $this->request->is( 'get' ) ) {

				// 初期化
				$datUserAlbums = array();

				// 会員情報取得
				$datUserAlbums[0] = $this->DatUser->getUserDataByUserName($this->request->username);

				// アルバム情報取得
				$datAlbums = $this->DatAlbum->getPreviewAlbumDataByUserId($datUserAlbums[0]['DatUser']['user_id']);

				// 配列構造調整
				foreach ( $datAlbums as $album_key => $datAlbum ) {

					$datUserAlbums[0]['DatAlbum'][$album_key]	= $datAlbums[$album_key]['DatAlbum'];

					// アルバムに紐づく写真情報取得
					$datAlbumPhotos = $this->DatPhoto->getAlbumPhotoRelationByUserIdAlbumID($datUserAlbums[0]['DatUser']['user_id'], $datAlbum['DatAlbum']['id']);

					// 配列構造調整
					foreach ($datAlbumPhotos as $photo_key => $datAlbumPhoto) {

						$datUserAlbums[0]['DatAlbum'][$album_key]['DatPhoto'][$photo_key] = $datAlbumPhotos[$photo_key]['DatPhoto'];

						// いらないものを消す
						unset($datAlbumPhoto[$photo_key]);
					}

					if ( ! isset( $datUserAlbums[0]['DatAlbum'][$album_key]['DatPhoto'] ) ) {
						// 写真データがないアルバムはビューの対象外とする
						unset( $datUserAlbums[0]['DatAlbum'][$album_key] );
					}

					// いらないものを消す
					unset($datAlbums[$album_key]);
				}

				// データセット
				$this->set('datUserAlbums', $datUserAlbums);

			} else {
				// getではない時は「400 Bad Request」
				throw new BadRequestException(__('Bad Request.'));
			}

			// JSONレスポンス
			$this->set('_serialize', 'datUserAlbums');

		} catch (Exception $e) {

			$this->set( 'datUserAlbums', array( 'errorMsg' => 'データ取得に失敗しました。画面を更新して再度お試しください' ) );
			$this->set('_serialize', 'datUserAlbums');
		}
	}

// 	public function userSearchAll () {

// 		// アルバム以外の写真検索
// // 		$db = $this->DatPhoto->getDataSource();
// // 		$datPhotos = $db->fetchAll(
// // <<<EOF
// // 				SELECT *
// // 					FROM dat_users AS users
// // 				LEFT JOIN dat_albums AS albums
// // 					ON users.user_id = albums.fk_user_id
// // 				LEFT JOIN dat_album_photo_relations AS relations
// // 					ON albums.album_id = relations.fk_album_id
// // 				LEFT JOIN dat_photos AS photos
// // 					ON photos.photo_id = relations.fk_photo_id
// // EOF
// // 		);

// 		// 返り値のデフォルトセット：false
// 		$this->set('datUser', false);

// 		/* 検索項目 */
// 		$fields = array(
// 				'DatUser.user_id as id',
// 				'DatUser.username',
// 				'DatUser.first_name',
// 				'DatUser.last_name',
// 				'DatUser.status',
// 				'DatUser.create_datetime',
// 				'DatUser.update_timestamp',
// 		);
// 		$contain = array(
// 				'DatAlbum' => array(
// 						'fields' => array(
// 								'album_id as id',
// 								'albumName as albumName',
// 								'description',
// 								'flg',
// 								'status',
// 								'create_datetime',
// 								'update_timestamp',
// 						),
// 						'conditions' => array(
// 								'DatAlbum.status' => 1,
// 								'DatAlbum.flg' => 1,
// 						),
// 				),
// 		);
// 		$conditions = array(
// 				'DatUser.status' => 1,
// 		);

// 		$this->DatUser->Behaviors->attach('Containable');
// 		$option = array(
// 				'fields' => $fields,
// 				'conditions' => $conditions,
// 				'contain' => $contain,
// 		);
// 		$datUsers = $this->DatUser->find('all', $option);

// 		// 必要ない関連テーブルは検索しない

// 		$this->DatAlbumPhotoRelation->unbindModel(array('belongsTo'=>array('DatAlbum')), false);			//,'hasAndBelongsToMany' => array('DatAlbum')
// 		foreach ( $datUsers as $userkey => $datUser ) {

// 			foreach ( $datUser['DatAlbum'] as $albumkey => $Album) {

// 				$datPhotos = $this->DatAlbumPhotoRelation->find('all', array('conditions' => array('DatAlbumPhotoRelation.fk_album_id' => $Album['id'])));

// 				foreach( $datPhotos as $photokey => $Photo ) {

// 					$datPhotos[$photokey]['id'] = $datPhotos[$photokey]['DatPhoto']['photo_id'];
// 					$datPhotos[$photokey]['fk_user_id'] = $datPhotos[$photokey]['DatPhoto']['fk_user_id'];
// 					$datPhotos[$photokey]['photoName'] = $datPhotos[$photokey]['DatPhoto']['photoName'];
// 					$datPhotos[$photokey]['description'] = $datPhotos[$photokey]['DatPhoto']['description'];
// 					$datPhotos[$photokey]['file_name'] = $datPhotos[$photokey]['DatPhoto']['file_name'];
// 					$datPhotos[$photokey]['thum_file_name'] = $datPhotos[$photokey]['DatPhoto']['thum_file_name'];
// // 					$datPhotos[$photokey]['imgUrl'] = $datPhotos[$photokey]['DatPhoto']['imgUrl'];
// // 					$datPhotos[$photokey]['thumUrl'] = $datPhotos[$photokey]['DatPhoto']['thumUrl'];
// 					$datPhotos[$photokey]['size'] = $datPhotos[$photokey]['DatPhoto']['size'];
// 					$datPhotos[$photokey]['type'] = $datPhotos[$photokey]['DatPhoto']['type'];
// 					$datPhotos[$photokey]['status'] = $datPhotos[$photokey]['DatPhoto']['status'];
// 					$datPhotos[$photokey]['create_datetime'] = $datPhotos[$photokey]['DatPhoto']['create_datetime'];
// 					$datPhotos[$photokey]['update_timestamp'] = $datPhotos[$photokey]['DatPhoto']['update_timestamp'];

// 					// いらないものを消す
// // 					unset($datPhotos[$key][0]);
// 					unset($datPhotos[$photokey]['DatPhoto']);
// 					unset($datPhotos[$photokey]['DatAlbumPhotoRelation']);
// 				}

// 				$datUsers[$userkey]['DatAlbum'][$albumkey]['DatPhoto'] = $datPhotos;
// 			}
// 		}

// 		$this->set('datUser', $datUsers);
// 		$this->set('_serialize', 'datUser');
// 	}
}
