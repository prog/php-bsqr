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


	/**
	 * Set invoice ID.
	 *
	 * @param string|null $invoiceId
	 * @return static
	 */
	public function setInvoiceId($invoiceId) {
		$this->invoiceId = $invoiceId;
		return $this;
	}


	/**
	 * Add payment.
	 *
	 * @param Payment $payment
	 * @return static
	 */
	public function addPayment(Payment $payment) {
		$this->payments[] = $payment;
		return $this;
	}

}
