<?php
declare(strict_types=1);
/**
 * @copyright 2019 Michal Semelka <m.semelka@gmail.com>
 */

namespace THSCZ\QuerySigner\Store;


use THSCZ\QuerySigner\Exception\ExpirationStoreException;

/**
 * ExpirationStoreInterface defines an interface to store expiraton for hash
 */
interface ExpirationStoreInterface {

	/**
	 * @param $hash string created by QuerySigner
	 * @param $timestamp integer UNIX timestamp value when hash expires
	 * @throws ExpirationStoreException
	 */
	public function set(string $hash, int $timestamp): void;

	/**
	 * @return integer|null UNIX timestamp value when hash expires
	 * @throws ExpirationStoreException
	 */
	public function get(string $hash): ?int;

	/**
	 * Deletes expiration information for hash
	 * @param $hash string created by QuerySigner
	 * @throws ExpirationStoreException
	 */
	public function revoke(string $hash): void;

}