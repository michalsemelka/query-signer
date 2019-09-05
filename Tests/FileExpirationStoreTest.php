<?php

namespace THSCZ\QuerySigner\Tests;

use PHPUnit\Framework\TestCase;
use THSCZ\QuerySigner\Store\FileExpirationStore;

class FileExpirationStoreTest extends TestCase {

	/**
	 * @expectedException \RuntimeException
	 */
	public function testUnsuccessfullConstruct() {
		if (!getenv('USER') || 'root' === getenv('USER')) {
			$this->markTestSkipped('This test will fail if run under superuser');
		}

		new FileExpirationStore('/foo/bar');
	}

	public function testSetAndGet() {
		$store = new FileExpirationStore(sys_get_temp_dir());

		$timestamp = time();
		$hash = md5('randomhash');

		$store->set($hash, $timestamp);

		$this->assertEquals($timestamp, $store->get($hash));
		$this->assertEquals(null, $store->get(md5('foobar')));
	}

	public function testRevoke() {
		$store = new FileExpirationStore(sys_get_temp_dir());

		$timestamp = time();
		$hash = md5('randomhash');

		$store->set($hash, $timestamp);

		$this->assertEquals($timestamp, $store->get($hash));

		$store->revoke($hash);

		$this->assertEquals(null, $store->get($hash));
	}

}