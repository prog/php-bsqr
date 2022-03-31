<?php

namespace com\peterbodnar\bsqr\model;



/**
 * Payment order definition
 */
class Payment extends Element {


	/** @var bool */
	public $paymentOrderOption = TRUE;
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
	/** @var string|NULL - Beneficiary name  */
	public $beneficiaryName;
	/** @var string|NULL - Beneficiary address line 1 */
	public $beneficiaryAddressLine1;
	/** @var string|NULL - Beneficiary address line 2 */
	public $beneficiaryAddressLine2;


	/**
	 * Set order option.
	 *
	 * @param bool $paymentOrderOption
	 * @return static
	 */
	public function setPaymentOrderOption($paymentOrderOption = TRUE) {
		$this->paymentOrderOption = $paymentOrderOption;
		return $this;
	}


	/**
	 * Set ammount.
	 *
	 * @param int|float|null $amount
	 * @param string|NULL $currencyCode
	 * @return static
	 */
	public function setAmount($amount, $currencyCode = NULL) {
		$this->amount = $amount;
		if (NULL !== $currencyCode) {
			$this->setCurrencyCode($currencyCode);
		}
		return $this;
	}


	/**
	 * Payment currency code, 3 letter ISO4217 code.
	 *
	 * @param string $currencyCode
	 * @return static
	 */
	public function setCurrencyCode($currencyCode) {
		$this->currencyCode = $currencyCode;
		return $this;
	}


	/**
	 * Set payment due date. Used also as first payment date for standing order.
	 *
	 * @param string|null $dueDate
	 * @return static
	 */
	public function setDueDate($dueDate) {
		$this->dueDate = $dueDate;
		return $this;
	}


	/**
	 * Set variable symbol.
	 *
	 * @param string|null $variableSymbol
	 * @return static
	 */
	public function setVariableSymbol($variableSymbol) {
		$this->variableSymbol = $variableSymbol;
		return $this;
	}


	/**
	 * Set constant symbol.
	 *
	 * @param string|null $constantSymbol
	 * @return static
	 */
	public function setConstantSymbol($constantSymbol) {
		$this->constantSymbol = $constantSymbol;
		return $this;
	}


	/**
	 * Set specific symbol.
	 *
	 * @param string|null $specificSymbol
	 * @return static
	 */
	public function setSpecificSymbol($specificSymbol) {
		$this->specificSymbol = $specificSymbol;
		return $this;
	}


	/**
	 * Set variable, constant and specific symbols.
	 *
	 * @param string|null $variableSymbol
	 * @param string|null $constantSymbol
	 * @param string|null $specificSymbol
	 * @return static
	 */
	public function setSymbols($variableSymbol, $constantSymbol, $specificSymbol = NULL) {
		$this->variableSymbol = $variableSymbol;
		$this->constantSymbol = $constantSymbol;
		if (func_num_args() > 2) {
			$this->specificSymbol = $specificSymbol;
		}
		return $this;
	}


	/**
	 * Set originators reference information.
	 *
	 * @param string|null $originatorsReferenceInformation
	 * @return static
	 */
	public function setOriginatorsReferenceInformation($originatorsReferenceInformation) {
		$this->originatorsReferenceInformation = $originatorsReferenceInformation;
		return $this;
	}


	/**
	 * Set payment note.
	 *
	 * @param string|null $note
	 * @return static
	 */
	public function setNote($note) {
		$this->note = $note;
		return $this;
	}


	/**
	 * Add bank account.
	 *
	 * @param BankAccount|string $bankAccount
	 * @param string|null $bicOrNull
	 * @return static
	 */
	public function addBankAccount($accountOrIban, $bicOrNull = NULL) {
		if ($accountOrIban instanceof BankAccount) {
			$this->bankAccounts[] = $accountOrIban;
		} else {
			$this->bankAccounts[] = new BankAccount($accountOrIban, $bicOrNull);
		}
		return $this;
	}


	/**
	 * Set standing order extension.
	 * Extends basic payment information with information required for standing order setup.
	 *
	 * @param StandingOrderExt|null $standingOrderExt
	 * @return static
	 */
	public function setStandingOrderExt($standingOrderExt) {
		$this->standingOrderExt = $standingOrderExt;
		return $this;
	}


	/**
	 * Set direct debit extension.
	 * Extends basic payment information with information required for identification and setup of direct debit.
	 *
	 * @param DirectDebitExt|null $directDebitExt
	 * @return static
	 */
	public function setDirectDebitExt($directDebitExt) {
		$this->directDebitExt = $directDebitExt;
		return $this;
	}

	/**
	 * Set beneficiary name
	 *
	 * @param string|null $beneficiaryName
	 * @return static
	 */
	public function setBeneficiaryName($beneficiaryName): self
	{
		$this->beneficiaryName = $beneficiaryName;
		return $this;
	}

	/**
	 * Set beneficiary address line 1
	 *
	 * @param string|null $beneficiaryAddressLine1
	 * @return static
	 */
	public function setBeneficiaryAddressLine1($beneficiaryAddressLine1): self
	{
		$this->beneficiaryAddressLine1 = $beneficiaryAddressLine1;
		return $this;
	}

	/**
	 * Set beneficiary address line 2
	 * @param string|null $beneficiaryAddressLine2
	 * @return static
	 */
	public function setBeneficiaryAddressLine2($beneficiaryAddressLine2): self
	{
		$this->beneficiaryAddressLine2 = $beneficiaryAddressLine2;
		return $this;
	}


	/**
	 * Create Pay document from this payment.
	 *
	 * @return Pay
	 */
	public function createPayDocument() {
		return (new Pay())->addPayment($this);
	}

}
