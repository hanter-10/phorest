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

	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['tmp_email'])) {
			$hash_string = $this->data[$this->alias]['tmp_email'];
			$this->data[$this->alias]['hash_string'] = Security::rijndael($hash_string, Configure::read('Security.key'), 'encrypt');
		}
		return true;
	}

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
}
