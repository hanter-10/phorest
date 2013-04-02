<?php
App::uses('AppModel', 'Model');
/**
 * MstImageServer Model
 *
 * @property DatPhoto $DatPhoto
 */
class MstImageServer extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'image_server_id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'image_server_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
			'foreignKey' => 'fk_image_server_id',
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
	 * ランダムに画像サーバを選択する
	 */
	public function getSelectImageServer() {

		$this->recursive = 0;

		/* 検索条件 */
		$conditions = array(
				'MstImageServer.status' => STATUS_ON,		// 有効
				);

		$option = array(
				'conditions' => $conditions,
				);

		$servers = $this->find( 'all', $option );

		return $servers[array_rand( $servers, 1 )]['MstImageServer']['image_server_id'];
	}

	/**
	 * 画像パスを取得する
	 *
	 * @param unknown_type $id
	 * @param unknown_type $username
	 * @param unknown_type $filename
	 */
	public function getImageServerPathByUser( $id, $username, $filename ) {

		$this->recursive = 0;

		/* バーチャルフィールドを定義 */
		$this->virtualFields = array(
				'imgUrl' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,'$username','/','$filename')",
				'thumUrl' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,'$username','/thumbnail/','$filename')",
				'thumUrl_square' 	=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,'$username','/square/','$filename')",
				'imgUrl_m' 			=> "CONCAT('http://',MstImageServer.grobal_ip,MstImageServer.file_path,'$username','/medium/','$filename')",
				);

		/* 検索条件 */
		$conditions = array(
				'MstImageServer.image_server_id' => $id,
				'MstImageServer.status' => STATUS_ON,		// 有効
				);

		$option = array(
				'conditions'	=> $conditions,
				);

		return $this->find( 'first', $option );
	}

}
