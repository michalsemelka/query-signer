<?php
declare(strict_types=1);
/**
 * @copyright 2019 Michal Semelka <m.semelka@gmail.com>
 */

namespace THSCZ\QuerySigner\Store;


use THSCZ\QuerySigner\Exception\ExpirationStoreException;

class FileExpirationStore implements ExpirationStoreInterface {

	/**
	 * @var string
	 */
	private $dir;

	public function __construct(string $dir) {
		if (!\is_dir($dir) || !\is_writable($dir)) {
			throw new \RuntimeException(sprintf('"%s" is not writable.', $dir));
		}

		$this->dir = rtrim($dir, '/') . '/';
	}


	/**
	 * {@inheritDoc}
	 */
	public function set(string $hash, int $timestamp): void {
		$result = file_put_contents(sprintf('%s%s.txt', $this->dir, $hash), $timestamp);

		if ($result === false) {
			throw new ExpirationStoreException(sprintf('Unable to create expiration entry for hash "%s" with timestamp "%d"', $hash, $timestamp));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(string $hash): ?int {
		$expiration = @file_get_contents(sprintf('%s%s.txt', $this->dir, $hash));

		return $expiration === false ? null : (int) $expiration;
	}

	/**
	 * {@inheritDoc}
	 */
	public function revoke(string $hash): void {
		$result = unlink(sprintf('%s%s.txt', $this->dir, $hash));

		if ($result === false) {
			throw new ExpirationStoreException(sprintf('Unable to revoke hash "%s"', $hash));
		}
	}

}