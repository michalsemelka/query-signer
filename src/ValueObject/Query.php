<?php
declare(strict_types=1);
/**
 * @copyright 2019 Michal Semelka <m.semelka@gmail.com>
 */

namespace THSCZ\QuerySigner\ValueObject;


class Query {

	/**
	 * @var string
	 */
	private $query;

	public function __construct(array $params) {
		$query = array_values(array_filter($params));
		sort($query, SORT_STRING);
		$this->query = implode('[.]', $query);
	}

	public function getQuery(): string {
		return $this->query;
	}

}