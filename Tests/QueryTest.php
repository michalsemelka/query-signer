<?php

namespace THSCZ\QuerySigner\Tests;

use PHPUnit\Framework\TestCase;
use \THSCZ\QuerySigner\ValueObject\Query;

class QueryTest extends TestCase {

	/**
	 * @dataProvider provideQueryParams
	 */
	public function testCreate($params, $result) {
		$query = new Query($params);

		$this->assertEquals($result, $query->getQuery());
	}

	public function provideQueryParams() {
		return [
			[
				["foo", "bar"],
				'bar[.]foo'
			],
			[
				["baz", "baz", 123],
				'123[.]baz[.]baz'
			],
			[
				["foo", "bar", null, 'acme', ''],
				'acme[.]bar[.]foo'
			],
			[
				[],
				''
			]
		];
	}

}