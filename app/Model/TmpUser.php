<?php
App::uses('AppModel', 'Model');
/**
 * DatUser Model
 *
 * @property TmpUser $TmpUser
 */
class TmpUser extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'temp_email' => array(
			'notempty' => array(
				'rule' => array('email'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
// 		'hash_string' => array(
// 			'notempty' => array(
// 					'rule' => array('notempty'),
// 					//'message' => 'Your custom message here',
// 					//'allowEmpty' => false,
// 					//'required' => false,
// 					//'last' => false, // Stop validation after this rule
// 					//'on' => 'create', // Limit validation to 'create' or 'update' operations
// 			),
// 		),
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
	);

	/**
	 * 該当のメールアドレスのステータスを変更する
	 *
	 * @param unknown $email
	 * @param unknown $status
	 * @return boolean
	 */
	function updateTmpUserStatus($email, $status) {
		return $this->updateAll(
			array(
				'TmpUser.status'	=> $status,
			),
			// Where
			array(
				array(
					'TmpUser.status'	=> 0,
					'TmpUser.temp_email'=> $email,
				)
			)
		);
	}

	/**
	 * 該当のHash値で一時Userデータを検索
	 *
	 * @param unknown $hash
	 * @param unknown $status
	 * @param unknown $fromDate
	 * @return unknown
	 */
	function getTmpUserDataByHash($hash, $status, $fromDate) {

		$db = $this->getDataSource();
		$tmpUser = $db->fetchAll(
<<<EOF
			SELECT
				id,
				temp_email,
				hash_string,
				status,
				create_datetime
			FROM
				tmp_users
			WHERE
				hash_string = ?
			AND
				status = ?
			AND
				create_datetime >= ?
EOF
			,array($hash, 0, $fromDate)
		);

		return $tmpUser;
	}
}
