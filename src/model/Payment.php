<?php

namespace com\peterbodnar\bsqr\model;



/**
 * Payment order definition
 */
class Payment extends Element {


	/** @var bool */
	public $paymentOrderOption = FALSE;
	/** @var int|null - Payment amount. */
	public $amount;
	/** @var string - Payment currency code, 3 letter ISO4217 code. */
	public $currencyCode = "XXX";
	/** @var string|null - Payment due date. Used also as first payment date for standing order. */
	public $dueDate;
	/** @var string|null - Variable symbol. */
	public $variableSymbol;
	/** @var string|null - Constant symbol. */
	public $constantSymbol;
	/** @var string|null - Specific symbol. */
	public $specificSymbol;
	/** @var string|null - Reference information. */
	public $originatorsReferenceInformation;
	/** @var string|null - Payment note. */
	public $note;
	/** @var BankAccount[] - List of bank accounts. */
	public $bankAccounts = [];
	/** @var StandingOrderExt|null - Standing order extension. Extends basic payment information with information required for standing order setup. */
	public $standingOrderExt;
	/** @var DirectDebitExt|null - Direct debit extension. Extends basic payment information with information required for identification and setup of direct debit. */
	public $directDebitExt;

}
