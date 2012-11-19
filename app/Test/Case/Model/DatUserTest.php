<?php
App::uses('DatUser', 'Model');

/**
 * DatUser Test Case
 *
 */
class DatUserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.dat_user',
		'app.dat_photo',
		'app.dat_photoset_photo_relation',
		'app.dat_photoset'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->DatUser = ClassRegistry::init('DatUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->DatUser);

		parent::tearDown();
	}

}
