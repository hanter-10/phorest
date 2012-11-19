<?php
/**
 * DatUserFixture
 *
 */
class DatUserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => '??ID'),
		'login_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'key' => 'unique', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'login_pw' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_unicode_ci', 'comment' => '??', 'charset' => 'utf8'),
		'last_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_unicode_ci', 'comment' => '??', 'charset' => 'utf8'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '[?????]??/??'),
		'create_datetime' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '????'),
		'update_timestamp' => array('type' => 'timestamp', 'null' => true, 'default' => null, 'comment' => '????'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'user_id', 'unique' => 1),
			'login_user_id_UNIQUE' => array('column' => 'login_id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'user_id' => 1,
			'login_id' => 'Lorem ipsum dolor sit amet',
			'login_pw' => 'Lorem ipsum dolor sit amet',
			'first_name' => 'Lorem ipsum dolor sit amet',
			'last_name' => 'Lorem ipsum dolor sit amet',
			'status' => 1,
			'create_datetime' => '2012-11-01 09:42:39',
			'update_timestamp' => 1351730559
		),
	);

}
