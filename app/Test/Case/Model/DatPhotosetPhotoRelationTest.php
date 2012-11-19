<?php
App::uses('DatPhotosetPhotoRelation', 'Model');

/**
 * DatPhotosetPhotoRelation Test Case
 *
 */
class DatPhotosetPhotoRelationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.dat_photoset_photo_relation',
		'app.dat_photoset',
		'app.dat_user',
		'app.dat_photo'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->DatPhotosetPhotoRelation = ClassRegistry::init('DatPhotosetPhotoRelation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->DatPhotosetPhotoRelation);

		parent::tearDown();
	}

}
