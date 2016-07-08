<?php

namespace com\peterbodnar\bsqr\model;



/**
 * Direct debit extension.
 * Extends basic payment information with information required for identification and setup of direct debit.
 */
class DirectDebitExt extends Element {


	const SCHEME_SEPA = "SEPA";
	const SCHEME_OTHER = "other";

	const TYPE_ONEOFF = "one-off";
	const TYPE_RECURRENT = "recurrent";


	/** @var string|self::SCHEME_ - Direct debit scheme, can be "SEPA" or "other". Use "SEPA" if direct debit complies with SEPA direct debit scheme. */
	public $scheme = "";
	/** @var string|self::TYPE_ - Type of direct debit, can be "one-off" or "recurrent" */
	public $type = "";
	/** @var string|null - Variable symbol. */
	public $variableSymbol;
	/** @var string|null - Specific symbol. */
	public $specificSymbol;
	/** @var string|null - Reference information. */
	public $originatorsReferenceInformation;
	/** @var string - Identification of the mandate between creditor and debtor. */
	public $mandateId = "";
	/** @var string - Identification of the creditor. */
	public $creditorId = "";
	/** @var string|null - Identification of the contract between creditor and debtor. */
	public $contractId;
	/** @var float|null - Maximum amount that can be debited. */
	public $maxAmount;
	/** @var string|null - Direct debit valid till date. */
	public $validTillDate;

}
