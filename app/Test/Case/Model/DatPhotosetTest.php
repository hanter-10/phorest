<?php
App::uses('DatPhotoset', 'Model');

/**
 * DatPhotoset Test Case
 *
 */
class DatPhotosetTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.dat_photoset',
		'app.dat_user',
		'app.dat_photoset_photo_relation'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->DatPhotoset = ClassRegistry::init('DatPhotoset');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->DatPhotoset);

		parent::tearDown();
	}

}
