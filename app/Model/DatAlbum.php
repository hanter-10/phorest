<?php
App::uses('AppModel', 'Model');
/**
 * DatAlbum Model
 *
 * @property DatUser $DatUser
 * @property DatAlbumPhotoRelation $DatAlbumPhotoRelation
 */
class DatAlbum extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'album_id';

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
		'album_id',
		'fk_user_id',
		'albumName',
		'description',
		'flg',
		'status',
		'create_datetime',
		'update_timestamp',
	);

	public $updateColumn = array(
		'albumName',
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
			'album_id' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => 'アルバムIDを指定してください'),
					'numeric' => array(
							'rule' => array( 'numeric' ),
							'message' => 'アルバムIDは数値で指定してください')),

			'fk_user_id' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => 'ユーザーIDを指定してください'),
					'numeric' => array(
							'rule' => array( 'numeric' ),
							'message' => 'ユーザーIDは数値で指定してください')),

			'albumName' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => 'アルバム名を指定してください')),

			'description' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => 'アルバム説明を指定してください')),

			'public' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => '公開/非公開を指定してください'),
					'numeric' => array(
							'rule' => array( 'numeric' ),
							'message' => '公開/非公開は数値で指定してください'))
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
		)
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'DatPhoto' => array(
				'className' => 'DatPhoto',
				'joinTable' => 'dat_album_photo_relations',
				//'with' => 'DatAlbumPhotoRelation',				// 指定テーブル名がCakePHPの自動Model名と異なる場合に設定
				'foreignKey' => 'fk_album_id',
				'associationForeignKey' => 'fk_photo_id',
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
	 * 会員に紐づくアルバム情報取得
	 * @param string $user_id
	 * @return Ambigous <multitype:, NULL, mixed>
	 */
	function getAlbumDataByUserId($user_id = null) {

		$this->recursive = 0;

		/* 検索項目 */
		$fields = array(
				'DatAlbum.album_id as id',
				'DatAlbum.albumName as albumName',
				'DatAlbum.description',
				'DatAlbum.public as public',
				'DatAlbum.status',
				'DatAlbum.create_datetime',
				'DatAlbum.update_timestamp',
		);

		/* 検索条件 */
		$conditions = array(
				'DatAlbum.fk_user_id' => $user_id,
				'DatAlbum.status' => 1,		// 有効
		);

		$option = array(
				'fields' => $fields,
				'conditions' => $conditions,
		);

		/* 検索実行 */
		return $this->find('all', $option);
	}

	/**
	 * 会員に紐づくアルバム情報取得(公開)
	 * @param string $user_id
	 * @return Ambigous <multitype:, NULL, mixed>
	 */
	function getPublicAlbumDataByUserId($user_id = null) {

		$this->recursive = 0;

		/* 検索項目 */
		$fields = array(
				'DatAlbum.album_id as id',
				'DatAlbum.albumName as albumName',
				'DatAlbum.description',
				'DatAlbum.public as public',
				'DatAlbum.status',
				'DatAlbum.create_datetime',
				'DatAlbum.update_timestamp',
		);

		/* 検索条件 */
		$conditions = array(
				'DatAlbum.fk_user_id'	=> $user_id,
				'DatAlbum.status'		=> 1,		// 有効
				'DatAlbum.public'			=> 1,		// 公開
		);
		$order = array(
				'DatAlbum.album_id DESC',
		);

		$option = array(
				'fields' => $fields,
				'conditions' => $conditions,
				'order' => $order,
		);

		/* 検索実行 */
		return $this->find('all', $option);
	}

// 	function getUserAlbumDataByUserName($username = null) {

// 		/* 検索光徳 */
// 		$fields = array(
// 				'DatAlbum.album_id as id',
// 				'DatAlbum.albumName as albumName',
// 				'DatAlbum.description',
// 				'DatAlbum.flg as public',
// 				'DatAlbum.status',
// 				'DatAlbum.create_datetime',
// 				'DatAlbum.update_timestamp',
// 				'DatUser.user_id as id',
// 				'DatUser.username',
// 				'DatUser.sitename',
// 				'DatUser.intro',
// 				'DatUser.status',
// 				'DatUser.create_datetime',
// 				'DatUser.update_timestamp',

// 		);

// 		/* contain */
// 		$contain = array(
// 				'DatUser' => array(
// 					'conditions' => array(
// 							'DatUser.status' => 1,
// 					),
// 				),
// 		);

// 		/* 検索条件 */
// 		$conditions = array(
// 				'DatUser.username'	=> $username,
// 				'DatUser.status'	=> 1,				// 有効
// 				'DatAlbum.status'	=> 1,				// 有効
// 				'DatAlbum.flg'		=> 1,				// 公開
// 		);
// 		$order = array(
// 				'DatAlbum.album_id DESC',
// 		);

// 		$option = array(
// 				'fields'		=> $fields,
// 				'contain'		=> $contain,
// 				'conditions'	=> $conditions,
// 				'order'			=> $order,
// 		);

// 		$this->Behaviors->attach('Containable');
// 		return $this->find('all', $option);
// 	}
}
