<?php
App::uses('AppModel', 'Model');
/**
 * DatPhoto Model
 *
 * @property DatUser $DatUser
 * @property DatPhotosetPhotoRelation $DatPhotosetPhotoRelation
 */
class DatPhoto extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'photo_id';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Model Column Set
 *
 * @var unknown
 */
	public $modelColumn = array(
		'photo_id',
		'fk_user_id',
		'fk_image_server_id',
		'photoName',
		'description',
		'size',
		'type',
		'status',
		'create_datetime',
		'update_timestamp',
	);

	public $updateColumn = array(
		'photoName',
		'description',
		'flg',
		'status',
		'update_timestamp',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'photo_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'fk_user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'fk_image_server_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'photoName' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'size' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'create_datetime' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'update_timestamp' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DatUser' => array(
			'className' => 'DatUser',
			'foreignKey' => 'fk_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MstImageServer' => array(
				'className' => 'MstImageServer',
				'foreignKey' => 'fk_image_server_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
		)
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'DatAlbum' => array(
				'className' => 'DatAlbum',
				'joinTable' => 'dat_album_photo_relations',
				//'with' => 'DatAlbumtPhotoRelation',				// 指定テーブル名がCakePHPの自動Model名と異なる場合に設定
				'foreignKey' => 'fk_photo_id',
				'associationForeignKey' => 'fk_album_id',
				'unique' => 'keepExisting',
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => ''
		)
	);


	/**
	 * アルバムに紐づく写真の検索
	 *
	 * @param unknown_type $album_id
	 * @return Ambigous <NULL, multitype:>
	 */
	function getAlbumPhotoRelationByUserIdAlbumID($user_id = null, $album_id = null) {

		/* 検索項目 */
		$fields = array(
				'DatPhoto.photo_id AS id',
				'DatPhoto.photoName AS photoName',
				'DatPhoto.fk_user_id',
				'DatPhoto.description',
				'DatPhoto.imgUrl',
				'DatPhoto.thumUrl',
				'DatPhoto.thumUrl_square',
				'DatPhoto.imgUrl_m',
				'DatPhoto.width',
				'DatPhoto.height',
				'DatPhoto.file_name',
				'DatPhoto.size',
				'DatPhoto.type',
				'DatPhoto.status',
				'DatPhoto.create_datetime',
				'DatPhoto.update_timestamp',
				'DatPhoto.fk_image_server_id',
		);
		/* バーチャルフィールドを定義 */
		$this->virtualFields = array(
				'imgUrl' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/',DatPhoto.file_name)",
				'thumUrl' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/thumbnail/',DatPhoto.file_name)",
				'thumUrl_square' 	=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/square/',DatPhoto.file_name)",
				'imgUrl_m' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/medium/',DatPhoto.file_name)",
		);

		/* contain */
		$contain = array(
				'DatUser' => array(
						'conditions' => array(
								'DatUser.status' => 1,
						),
				),
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
		);

		/* join */
		$joins = array(
				array("type" => "LEFT",
						"table" => "dat_album_photo_relations",
						"alias" => "DatAlbumPhotoRelation",
						"conditions" => "DatPhoto`.`photo_id = DatAlbumPhotoRelation.fk_photo_id",
				),
		);

		/* 検索条件 */
		$conditions = array(
				'DatUser.user_id' => $user_id,
				'DatPhoto.status' => 1,		// 有効
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
				'contain'		=> $contain,
				'conditions'	=> $conditions,
				'order'			=> $order,
				'joins'			=> $joins,
		);

		$this->Behaviors->attach('Containable');
		return $this->find('all', $option);
	}

	function getAlbumPhotoRelationByUserIdAlbumIDs($user_id = null, $album_ids = array()) {

		$condition_in = '';
		$cnt = count($album_ids);
		for ($i = 0; $i < $cnt; $i++) {
			if ($cnt-1 == $i) {
				$condition_in .= $album_ids[$i]['DatAlbum']['id'];
			} else {
				$condition_in .= $album_ids[$i]['DatAlbum']['id'] . ',';
			}
		}

		/* 検索項目 */
		$fields = array(
				'DatPhoto.photo_id AS id',
				'DatPhoto.photoName AS photoName',
				'DatPhoto.fk_user_id',
				'DatPhoto.description',
				'DatPhoto.imgUrl',
				'DatPhoto.thumUrl',
				'DatPhoto.thumUrl_square',
				'DatPhoto.imgUrl_m',
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

		/* contain */
		$contain = array(
				'DatUser' => array(
						'conditions' => array(
								'DatUser.status' => 1,
						),
				),
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
		);

		/* join */
		$joins = array(
				array("type" => "LEFT",
						"table" => "dat_album_photo_relations",
						"alias" => "DatAlbumPhotoRelation",
						"conditions" => "DatPhoto`.`photo_id = DatAlbumPhotoRelation.fk_photo_id",
				),
		);

		/* 検索条件 */
		$conditions = array(
				'DatUser.user_id' => $user_id,
				'DatPhoto.status' => 1,		// 有効
				'DatUser.status' => 1,
				'MstImageServer.status' => 1,
				'DatAlbumPhotoRelation.status' => 1,
				"DatAlbumPhotoRelation.fk_album_id in ($condition_in)",
		);
		$order = array(
				'DatPhoto.create_datetime DESC',
		);

		$option = array(
				'fields'		=> $fields,
				'contain'		=> $contain,
				'conditions'	=> $conditions,
				'order'			=> $order,
				'joins'			=> $joins,
		);

		$this->Behaviors->attach('Containable');
		return $this->find('all', $option);
	}

	/**
	 * 会員のアルバムに属さない写真データ取得
	 *
	 * @param unknown_type $user_id
	 * @return Ambigous <NULL, multitype:>
	 */
	function getTempAlbumPhotoRelationByUserID($user_id) {

		/* 検索項目 */
		$fields = array(
				'DatPhoto.photo_id AS id',
				'DatPhoto.photoName AS photoName',
				'DatPhoto.fk_user_id',
				'DatPhoto.description',
				'DatPhoto.imgUrl',
				'DatPhoto.thumUrl',
				'DatPhoto.thumUrl_square',
				'DatPhoto.imgUrl_m',
				'DatPhoto.width',
				'DatPhoto.height',
				'DatPhoto.file_name',
				'DatPhoto.size',
				'DatPhoto.type',
				'DatPhoto.status',
				'DatPhoto.create_datetime',
				'DatPhoto.update_timestamp',
				'DatPhoto.fk_image_server_id',
		);
		/* バーチャルフィールドを定義 */
		$this->virtualFields = array(
				'imgUrl' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/',DatPhoto.file_name)",
				'thumUrl' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/thumbnail/',DatPhoto.file_name)",
				'thumUrl_square' 	=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/square/',DatPhoto.file_name)",
				'imgUrl_m' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,DatUser.username,'/medium/',DatPhoto.file_name)",
		);

		/* contain */
		$contain = array(
				'DatUser' => array(
						'conditions' => array(
								'DatUser.status' => 1,
						),
				),
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
		);

		/* join */
		$joins = array(
				array("type" => "LEFT",
						"table" => "dat_album_photo_relations",
						"alias" => "DatAlbumPhotoRelation",
						"conditions" => "DatPhoto`.`photo_id = DatAlbumPhotoRelation.fk_photo_id",
				),
		);

		/* 検索条件 */
		$conditions = array(
				'DatUser.user_id' => $user_id,
				'DatUser.status' => 1,
				'MstImageServer.status' => 1,
				'DatPhoto.status' => 1,		// 有効
// 				'DatAlbumPhotoRelation.status' => 1,
				'DatAlbumPhotoRelation.fk_photo_id is null',		// アルバムと紐づかない写真
		);
		$order = array(
				'DatPhoto.create_datetime DESC',
		);

		$option = array(
				'fields'		=> $fields,
				'contain'		=> $contain,
				'conditions'	=> $conditions,
				'order'			=> $order,
				'joins'			=> $joins
		);

		$this->Behaviors->attach('Containable');
		return $this->find('all', $option);
	}
}
