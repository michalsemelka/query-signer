# Creates control hash for specified $_GET params and validate them

This tool was created as part of my learning and playing with 
PHP OOP, Composer and PHPUnit.

## Usage

```
composer require thscz/query-signer
```

Base methods of BaseQuerySigner/GETQuerySigner:
 *  create(...params)           ->  setter of values that should be signed with hash,
 *  sign(...params)             –>  sign values specified in create() and returns hash,
 *  createValidate(...params)   ->  setter of values that should validate,
 *  validate(...params)         ->  validate obtained hash with hash created from values setted by createValidate().

For params definitions please see examples next.

### Sign

e.g.: file orders.php - User wants to sign "id" value (45623).


```php
// ...
// <a href="/order.php?id=45623">Order detail</a>

$signer = new \THSCZ\QuerySigner\GETQuerySigner();

// create() accepts as first parameter array of values that should be signed. 
// sign() has optional parameter $salt - if omitted, class will use internal @see options.
echo '<a href="/order.php?id=45623&hash='.$signer->create([45623])->sign().'">Order detail</a>';
```

### Validate

On validation page:
```php
// /order.php?id=45623&hash=xxxx

$hash = filter_input(INPUT_GET, 'hash');

$signer = new \THSCZ\QuerySigner\GETQuerySigner();
// createValidate() accepts as first parameter query string. Has two optional params, see next example. 
// validate() has parameter $hash and optional parameter $salt.
if ($signer->createValidate($_SERVER['QUERY_STRING'])->validate($hash)) {
    // approved
} else {
    // access denied
}
```

### Specifying which params to validate
Method createValidate accepts as first parameter the whole $_SERVER['QUERY_STRING']. But what if user wants to
sign and validate only one param but url includes more? There is ...app, ehm option for that :).

On sign page, user signs only the params that he wants.

```php
// ...
// <a href="/order.php?id=424647&param=dt42">Order detail</a>

$signer = new \THSCZ\QuerySigner\GETQuerySigner();

// sign only "id" value
$signer->create([424647])->sign();
```

On validation page, user specifies params that should be validated.
```php
// /order.php?id=424647&&param=dt42&hash=xxxx
// <a href="/order.php?id=424647&param=dt42">Order detail</a>

$signer = new \THSCZ\QuerySigner\GETQuerySigner();

// user specifies to only load "id" value from the query string
// with flag as second parameter and array of names as third parameter.
$signer->createValidate($_SERVER['QUERY_STRING'], 
    $signer::SIGNER_HELPER_BUILD_SPECIFIC_QUERY, 
    ['id']);

if ($signer->validate()) {
    //...
}
```

### Options
In Options/Base.php user can defines salt and name of session where are stored information for query-signer.

```php
const SIGNER_SALT = 'prettyflyforsalting';

const SIGNER_SESSION_NAME = 'querysigner';
```

## To-Do
* Write more tests :-)
* Store hash in session

```
Idea for this little tool came to my mind when I was working on some 
3rd party exotic system, that was unable to validate that item belonged
really to signed user
```


