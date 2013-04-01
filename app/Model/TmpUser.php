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

			'temp_email' => array(
					'email' => array(
							'rule' => array('email'),
							'message' => 'Eメールアドレスを確認してください'),
					'notempty' => array(
							'rule' => array( 'notEmpty' ),
							'message' => 'Eメールアドレスを入力してください'))
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
