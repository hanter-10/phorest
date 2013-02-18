<?php
App::uses('AppModel', 'Model');
/**
 * DatUser Model
 *
 * @property DatPhoto $DatPhoto
 * @property DatPhotoset $DatPhotoset
 */
class DatUser extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'user_id';

	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'username' => array(
			'notempty' => array(
				'rule' => array('alphaNumeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'notempty' => array(
					'rule' => array('email'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'create_datetime' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'update_timestamp' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'DatPhoto' => array(
			'className' => 'DatPhoto',
			'foreignKey' => 'fk_user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'DatAlbum' => array(
			'className' => 'DatAlbum',
			'foreignKey' => 'fk_user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	/**
	 * Emailがすでに登録済みかチェックする
	 *
	 * @param unknown $email
	 * @param unknown $status
	 * @return unknown
	 */
	function checkUserDataByEmail($email, $status) {

		$db = $this->getDataSource();
		$datUser = $db->fetchAll(
<<<EOF
			SELECT
				count(user_id) as cnt
			FROM
				dat_users
			WHERE
				email = ?
			AND
				status = ?
EOF
			,array($email, $status)
		);

		return $datUser;
	}

	/**
	 * User名で会員情報を検索
	 *
	 * @param string $username
	 * @return Ambigous <multitype:, NULL, mixed>
	 */
	function getUserDataByUserName($username = null) {

		$this->recursive = 0;

		/* 検索光徳 */
		$fields = array(
			'DatUser.user_id as id',
			'DatUser.username',
			'DatUser.sitename',
			'DatUser.intro',
			'DatUser.status',
			'DatUser.create_datetime',
			'DatUser.update_timestamp',
		);

		/* 検索条件 */
		$conditions = array(
			'DatUser.username'	=> $username,
			'DatUser.status'	=> 1,				// 有効
		);

		$option = array(
			'fields'		=> $fields,
			'conditions'	=> $conditions,
		);

		return $this->find('first', $option);
	}
}
