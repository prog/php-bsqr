# BySquare

By Square document encoding, rendering and parsing utilitis


## Instalation

`composer require peterbodnar.com/bsqr`


## Define a By Square document

```php
<?php

use com\peterbodnar\bsqr;

$document = new bsqr\model\Pay();
$document->payments[] = call_user_func(function() {
	$payment = new bsqr\model\Payment();
	$payment->dueDate = "0000-00-00";
	$payment->amount = 123.45;
	$payment->currencyCode = "EUR";
	$payment->variableSymbol = "1234567890";
	$payment->constantSymbol = "308";
	$payment->bankAccounts[] = call_user_func(function() {
		$bankAccount = new bsqr\model\BankAccount();
		$bankAccount->iban = "SK3112000000198742637541";
		$bankAccount->bic = "XXXXXXXXXXX";
		return $bankAccount;
	});
	return $payment;
});
```


## Render to svg including logo, caption and border

```php
<?php

use com\peterbodnar\bsqr;

$bysquare = new bsqr\BySquare();

$svg = (string) $bysquare->render($document);
```


## Get bsqr data only

```php
<?php

use com\peterbodnar\bsqr;

$bsqrCoder = new bsqr\utils\BsqrCoder();

$bsqrData = $bsqrCoder->encode($document);
```
Use any qr-code library to encode/render data to qr matrix/image.


## Parse bsqr data

```php
<?php

use com\peterbodnar\bsqr;

$bsqrCoder = new bsqr\utils\coder();

$document = $bsqrCoder->parse($bsqrData);
```


## Links

http://www.sbaonline.sk/sk/projekty/qr-platby/podmienky-pouzitia-specifikacia-standardu-pay-square.html
http://www.bysquare.com/
