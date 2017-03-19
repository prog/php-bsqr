# BySquare

By Square document encoding, rendering and parsing utilities


## Instalation

`composer require peterbodnar.com/bsqr`


## Define a By Square document

```php
<?php

use com\peterbodnar\bsqr;

$document = (new bsqr\model\Payment())
	->setPaymentOrderOption()
	->setDueDate("0000-00-00")
	->setAmount(123.45, "EUR")
	->setSymbols("1234567890", "308")
	->addBankAccount("SK3112000000198742637541", "XXXXXXXXXXX")
	->createPayDocument();
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

$bsqrCoder = new bsqr\utils\BsqrCoder();

$document = $bsqrCoder->parse($bsqrData);
```


## Links

http://www.sbaonline.sk/sk/projekty/qr-platby/podmienky-pouzitia-specifikacia-standardu-pay-square.html
http://www.bysquare.com/
