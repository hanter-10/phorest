<?php
App::uses('AppModel', 'Model');
/**
 * DatAlbumPhotoRelation Model
 *
 * @property DatAlbum $DatAlbum
 * @property DatPhoto $DatPhoto
 */
class DatAlbumPhotoRelation extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'album_photo_relation_id';

/**
 * Model Column Set
 *
 * @var unknown
 */
	public $modelColumn = array(
		'album_photo_relation_id',
		'fk_album_id',
		'fk_photo_id',
		'status',
		'create_datetime',
		'update_timestamp',
	);

	public $updateColumn = array(
		'fk_album_id',
		'fk_photo_id',
		'status',
		'update_timestamp',
	);

	public $requestColumn = array(
		'fromAlbum',
		'targetAlbum',
		'photos',
		'status',
		'update_timestamp',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
			'fk_album_id' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => 'アルバムIDを指定してください'),
					'numeric' => array(
							'rule' => array( 'numeric' ),
							'message' => 'アルバムIDは数値で指定してください')),

			'fk_photo_id' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => '写真IDを指定してください'),
					'numeric' => array(
							'rule' => array( 'numeric' ),
							'message' => '写真IDは数値で指定してください')),
			);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DatAlbum' => array(
			'className' => 'DatAlbum',
			'foreignKey' => 'fk_album_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DatPhoto' => array(
			'className' => 'DatPhoto',
			'foreignKey' => 'fk_photo_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	function getAlbumPhotoRelationDataByUserIdAlbumId($user_id, $album_id) {


		/* 検索項目 */
		$fields = array(
				'DatAlbum.album_id as id',
				'DatAlbum.albumName as albumName',
				'DatAlbum.description',
				'DatAlbum.flg as public',
				'DatAlbum.status',
				'DatAlbum.create_datetime',
				'DatAlbum.update_timestamp',
				'DatPhoto.photo_id AS id',
				'DatPhoto.photoName AS photoName',
				'DatPhoto.fk_user_id',
				'DatPhoto.description',
				'imgUrl',
				'thumUrl',
				'thumUrl_square',
				'imgUrl_m',
				'DatPhoto.width',
				'DatPhoto.height',
				'DatPhoto.file_name',
				'DatPhoto.size',
				'DatPhoto.type',
				'DatPhoto.status',
				'DatPhoto.create_datetime',
				'DatPhoto.update_timestamp',
		);
		/* バーチャルフィールドを定義 */
		$this->virtualFields = array(
				'imgUrl' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/',DatPhoto.file_name)",
				'thumUrl' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/thumbnail/',DatPhoto.file_name)",
				'thumUrl_square' 	=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/square/',DatPhoto.file_name)",
				'imgUrl_m' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/medium/',DatPhoto.file_name)",
		);

		/* join */
		$joins = array(
				array("type" => "LEFT",
						"table" => "dat_albums",
						"alias" => "DatAlbum",
						"conditions" => "DatAlbumPhotoRelation.fk_album_id= DatAlbum.album_id",
				),
				array("type" => "LEFT",
						"table" => "dat_photos",
						"alias" => "DatPhoto",
						"conditions" => "DatAlbumPhotoRelation.fk_photo_id = DatPhoto.photo_id",
				),
				array("type" => "LEFT",
						"table" => "mst_image_servers",
						"alias" => "MstImageServer",
						"conditions" => "DatPhoto.fk_image_server_id = MstImageServer.image_server_id",
				),
				array("type" => "LEFT",
						"table" => "dat_users",
						"alias" => "DatUser",
						"conditions" => "DatUser.user_id = DatPhoto.fk_user_id",
				),
		);

		/* 検索条件 */
		$conditions = array(
				'DatUser.user_id' => $user_id,
				'DatAlbum.fk_user_id' => $user_id,
				'DatAlbum.status' => 1,
				'DatPhoto.status' => 1,
				'DatUser.status' => 1,
				'MstImageServer.status' => 1,
				'DatAlbumPhotoRelation.status' => 1,
				'DatAlbumPhotoRelation.fk_album_id' => $album_id,
		);
		$order = array(
				'DatPhoto.create_datetime DESC',
		);

		$option = array(
				'fields'		=> $fields,
				'conditions'	=> $conditions,
				'order'			=> $order,
				'joins'			=> $joins,
		);

		$this->unbindModel(array('belongsTo'=>array('DatAlbum')), false);
		$this->unbindModel(array('belongsTo'=>array('DatPhoto')), false);

		return $this->find('all', $option);
	}

	public function updateAlbumPhotoRelationByAlbumId( $target_album_id, $from_album_id, $photo_id ) {

		/* 検索条件 */
		$conditions = array(
				'DatAlbumPhotoRelation.fk_album_id' => $from_album_id,
				'DatAlbumPhotoRelation.fk_photo_id' => $photo_id,
		);

		$fields = array(
				'DatAlbumPhotoRelation.fk_album_id' => $target_album_id,
				);

		return $this->updateAll( $fields, $conditions );
	}
}
