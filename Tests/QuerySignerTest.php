<?php

namespace THSCZ\QuerySigner\Tests;


use PHPUnit\Framework\TestCase;
use THSCZ\QuerySigner\QuerySigner;

class QuerySignerTest extends TestCase {


    /**
     * @dataProvider provideValues
     */
    public function testBaseFunction($signParams, $validateParams, $result) {
    	$querySigner = new QuerySigner('supersecret');

    	$hash = $querySigner->sign($signParams);

    	$this->assertEquals($result, $querySigner->validate($validateParams, $hash));
    }

    public function provideValues() {
		return [
			[
				["foo", "bar"],
				["foo", "bar"],
				true
			],
			[
				["baz", "baz", 123],
				["baz", "123", 'baz'],
				true
			],
			[
				["foo", "bar", null, 'acme', ''],
				["foo", "bar", null, 'acme', ''],
				true
			],
			[
				['foo'],
				['baz'],
				false
			]
		];
	}

	public function testWithExpiration() {
		$expirationMock = $this->getMockBuilder(\THSCZ\QuerySigner\Store\ExpirationStoreInterface::class)
			->setMethods(['set', 'get', 'revoke'])
			->getMock()
		;

		$expirationMock->expects($this->at(1))->method('get')->willReturn(time() + 60);
		$expirationMock->expects($this->at(2))->method('get')->willReturn(time() - 60);

		$querySigner = new QuerySigner('supersecret', $expirationMock);

		$hash = $querySigner->sign(["foo", "bar"], 60);

		$this->assertEquals(true, $querySigner->validate(["foo", "bar"], $hash));
		$this->assertEquals(false, $querySigner->validate(["foo", "bar"], $hash));
	}
}