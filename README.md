# Creates control hash from values and validate them

This tool was created as part of my learning and playing with 
PHP OOP, Composer and PHPUnit.

## Usage

```
composer require thscz/query-signer
```

### Sign

e.g.: file orders.php - User wants to sign "id" value (45623).


```php
require_once 'vendor/autoload.php';

// ...
// <a href="/order/45623">Order detail</a>

$querySigner = new \THSCZ\QuerySigner\QuerySigner('supersecrtet');
$hash = $querySigner->sign([45623]);

echo '<a href="/order/45623/&hash='. $hash .'">Order detail</a>';
```
### Validate

On validation page:
```php
// /order/45623/&hash=xxx
require_once 'vendor/autoload.php';

$hash = filter_input(INPUT_GET, 'hash');
$orderId = filter_input(INPUT_GET, 'orderId');

$querySigner = new \THSCZ\QuerySigner\QuerySigner('supersecrtet');

if ($querySigner->validate([$orderId]) {
    // approved
} else {
    // denied
}
```

### Usage with expiration store
You can create hash with TTL (time to live) in seconds. For this option you have to use Expiration Store thats implements ExpirationStoreInterface and stores information about which hash has which expiration.

```php
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
 ```
This package comes with very simple FileExpirationStore that stores information expiration value on file system. Expiration store is second
parameter of QuerySigner class.

```php
require_once 'vendor/autoload.php';

// ...
// <a href="/order/45623">Order detail</a>

$querySigner = new \THSCZ\QuerySigner\QuerySigner('supersecrtet', new \THSCZ\QuerySigner\Store\FileExpirationStore(__DIR__ . '/var/signs'));
// hash is now valid for current UNIX timestamp + 60 seconds
$hash = $querySigner->sign([45623], 60);

echo '<a href="/order/45623/&hash='. $hash .'">Order detail</a>';
```
```
Idea for this little tool came to my mind when I was working on some 
3rd party exotic system, that was unable to validate that item belonged
really to signed user
```


