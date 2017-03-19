<?php

namespace com\peterbodnar\bsqr\model;



/**
 * Single bank account.
 */
class BankAccount extends Element {


	/** @var string - IBAN code. */
	public $iban;
	/** @var string - SWIFT code. */
	public $bic;


	/**
	 * @param string|null $iban
	 * @param string|null $bic
	 */
	public function __construct($iban = NULL, $bic = NULL) {
		$this->iban = $iban;
		$this->bic = $bic;
	}

}
