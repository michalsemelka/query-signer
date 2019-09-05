<?php
declare(strict_types=1);
/**
 * @copyright 2019 Michal Semelka <m.semelka@gmail.com>
 */

namespace THSCZ\QuerySigner;


use THSCZ\QuerySigner\Store\ExpirationStoreInterface;
use THSCZ\QuerySigner\ValueObject\Query;

class QuerySigner {

	/**
	 * @var string
	 */
	private $passphrase;
	/**
	 * @var ExpirationStoreInterface|null
	 */
	private $expirationStore;

	public function __construct(string $passphrase, ?ExpirationStoreInterface $expirationStore = null) {
		$this->passphrase = $passphrase;
		$this->expirationStore = $expirationStore;
	}

	public function getExpirationStore(): ?ExpirationStoreInterface {
		return $this->expirationStore;
	}

	public function sign(array $params, int $ttl = 0): string {
		$expiration = null;
		if ($this->expirationStore !== null && $ttl > 0) {
			$expiration = (time() + $ttl);
		}

		array_push($params, $expiration);
		array_push($params, $this->passphrase);

		$hash = password_hash((new Query($params))->getQuery(), PASSWORD_BCRYPT);

		if ($this->expirationStore !== null && $ttl > 0) {
			$this->expirationStore->set($hash, $expiration);
		}

		return $hash;
	}

	public function validate(array $params, string $hash): bool {
		$expiration = null;
		if ($this->expirationStore !== null) {
			$expiration = $this->expirationStore->get($hash);
			if (time() > $expiration) {
				return false;
			}
		}

		array_push($params, $expiration);
		array_push($params, $this->passphrase);

		return password_verify((new Query($params))->getQuery(), $hash);
	}
}