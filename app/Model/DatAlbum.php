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

	public $updateClumn = array(
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
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'fk_user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				//'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'albumName' => array(
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
		'flg' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
}
