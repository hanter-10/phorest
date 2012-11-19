<?php
/**
 * DatPhotosetPhotoRelationFixture
 *
 */
class DatPhotosetPhotoRelationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'photoset_photo_relation_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => '?????????????ID'),
		'fk_photoset_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index', 'comment' => '?????ID'),
		'fk_photo_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index', 'comment' => '??ID'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'comment' => '[?????]??/??'),
		'create_datetime' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '????'),
		'update_timestamp' => array('type' => 'timestamp', 'null' => true, 'default' => null, 'comment' => '????'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'photoset_photo_relation_id', 'unique' => 1),
			'fk_fhotoset_id_idx' => array('column' => 'fk_photoset_id', 'unique' => 0),
			'fk_fhoto_id_idx' => array('column' => 'fk_photo_id', 'unique' => 0)
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
			'photoset_photo_relation_id' => 1,
			'fk_photoset_id' => 1,
			'fk_photo_id' => 1,
			'status' => 1,
			'create_datetime' => '2012-11-01 09:19:26',
			'update_timestamp' => 1351729166
		),
	);

}
