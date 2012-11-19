<?php
App::uses('DatPhoto', 'Model');

/**
 * DatPhoto Test Case
 *
 */
class DatPhotoTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.dat_photo',
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
		$this->DatPhoto = ClassRegistry::init('DatPhoto');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->DatPhoto);

		parent::tearDown();
	}

}
