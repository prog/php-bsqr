<?php

namespace com\peterbodnar\bsqr\model;



/**
 * Pay Base
 */
abstract class PayBase extends Document {


	/** @var string|null - Invoice identification code. Only used when pay by square is part of the invoice. Otherwise this field is empty. */
	public $invoiceId;
	/** @var Payment[] - Lists one or more payments. */
	public $payments = [];

}
