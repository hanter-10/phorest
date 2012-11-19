<?php
/**
 * DatPhotoFixture
 *
 */
class DatPhotoFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'photo_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => '??ID'),
		'fk_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index', 'comment' => '??ID'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '???', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 1000, 'collate' => 'utf8_unicode_ci', 'comment' => '????', 'charset' => 'utf8'),
		'file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_unicode_ci', 'comment' => '???????', 'charset' => 'utf8'),
		'size' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '???????'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'comment' => '???????', 'charset' => 'utf8'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '[?????]??/??'),
		'create_datetime' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '????'),
		'update_timestamp' => array('type' => 'timestamp', 'null' => true, 'default' => null, 'comment' => '????'),
		'dat_photoscol' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 45, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'photo_id', 'unique' => 1),
			'fk_user_id_idx' => array('column' => 'fk_user_id', 'unique' => 0)
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
			'photo_id' => 1,
			'fk_user_id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'description' => 'Lorem ipsum dolor sit amet',
			'file_name' => 'Lorem ipsum dolor sit amet',
			'size' => 1,
			'type' => 'Lorem ipsum dolor sit amet',
			'status' => 1,
			'create_datetime' => '2012-11-01 09:08:32',
			'update_timestamp' => 1351728512,
			'dat_photoscol' => 'Lorem ipsum dolor sit amet'
		),
	);

}
