<?php
/**
 * MstImageServerFixture
 *
 */
class MstImageServerFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'image_server_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => '??????ID'),
		'grobal_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_unicode_ci', 'comment' => '?????IP', 'charset' => 'utf8'),
		'remote_ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_unicode_ci', 'comment' => '????IP', 'charset' => 'utf8'),
		'file_path' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_unicode_ci', 'comment' => '??????', 'charset' => 'utf8'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '[?????]??/??'),
		'create_datetime' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '????'),
		'update_timestamp' => array('type' => 'timestamp', 'null' => true, 'default' => null, 'comment' => '????'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'image_server_id', 'unique' => 1)
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
			'image_server_id' => 1,
			'grobal_ip' => 'Lorem ipsum dolor ',
			'remote_ip' => 'Lorem ipsum dolor ',
			'file_path' => 'Lorem ipsum dolor sit amet',
			'status' => 1,
			'create_datetime' => '2012-11-01 09:38:05',
			'update_timestamp' => 1351730285
		),
	);

}
