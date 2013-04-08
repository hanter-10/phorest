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
		if ( isset( $this->data[$this->alias]['password'] ) ) {
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
			'username' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => 'ユーザIDを入力してください'),
					'minlength' => array(
							'rule' => array('minLength', '5'),
							'message' => 'ユーザー名は5文字以上で入力してください。',
							'required' => true),
					'alphaNumeric' => array(
							'rule' => array( 'alphaNumeric' ),
							'message' => 'ユーザーIDを確認してください。記号は使用できません。')),

			'email' => array(
					'email' => array(
							'rule' => array('email'),
							'message' => 'Eメールアドレスを確認してください'),
					'notempty' => array(
							'rule' => array( 'notEmpty' ),
							'message' => 'Eメールアドレスを入力してください')),

			'password' => array(
					'notempty' => array(
							'rule' => array('notempty'),
							'message' => 'パスワードを入力してください'),
					'minlength' => array(
							'rule' => array('minLength', '7'),
							'message' => 'パスワードは7文字以上で入力してください。',
							'required' => true)),
			'sitename' => array(
					'notempty' => array(
							'rule' => array('notEmpty'),
							'message' => 'サイト名を入力してください') ),
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

// 		$db = $this->getDataSource();
// 		$datUser = $db->fetchAll(
// <<<EOF
// 			SELECT
// 				count(user_id) as cnt
// 			FROM
// 				dat_users
// 			WHERE
// 				email = ?
// 			AND
// 				status = ?
// EOF
// 			,array($email, $status)
// 		);

		$this->recursive = 0;

		$condition = array(
				'conditions' => array(
						'DatUser.email' => $email,
						'DatUser.status' => $status,
						)
				);
		return $this->find('count', $condition);
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
