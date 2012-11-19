<?php
App::uses('MstImageServer', 'Model');

/**
 * MstImageServer Test Case
 *
 */
class MstImageServerTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.mst_image_server',
		'app.dat_photo',
		'app.dat_user',
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
		$this->MstImageServer = ClassRegistry::init('MstImageServer');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MstImageServer);

		parent::tearDown();
	}

}
