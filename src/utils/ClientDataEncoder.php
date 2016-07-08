<?php

namespace com\peterbodnar\bsqr\utils;

use com\peterbodnar\bsqr\model;



/**
 * BySquare client data encoder
 */
class ClientDataEncoder {


	/** @var string */
	protected $separator = "\t";


	/**
	 * Encode string value.
	 *
	 * @param string - Value
	 * @return string
	 */
	protected function encodeValue($value) {
		return str_replace($this->separator, " ", $value);
	}


	/**
	 * Encode date.
	 *
	 * @param string - Date
	 * @return string
	 */
	protected function encodeDate($date) {
		return $this->encodeValue(str_replace("-", "", $date));
	}


	/**
	 * Encode bank account.
	 *
	 * @param model\BankAccount $bankAccount - Bank account
	 * @return string
	 */
	protected function encodeBankAccount(model\BankAccount $bankAccount) {
		return implode($this->separator, [
			$this->encodeValue($bankAccount->iban),
			$this->encodeValue($bankAccount->bic),
		]);
	}


	/**
	 * Encode standing order extension.
	 *
	 * @param model\StandingOrderExt $standingOrderExt - Standing order extension
	 * @return string
	 */
	protected function encodeStandingOrderExt(model\StandingOrderExt $standingOrderExt) {
		$d = $standingOrderExt->day; // todo
		$m = $standingOrderExt->month; // todo
		$p = $standingOrderExt->periodicity; // todo

		return implode($this->separator, [
			$this->encodeValue($d),
			$this->encodeValue($m),
			$this->encodeValue($p),
			$this->encodeDate($standingOrderExt->lastDate),
		]);
	}


	/**
	 * Encode direct debit extension element.
	 *
	 * @param model\DirectDebitExt $directDebitExt - Direct debit extension
	 * @return string
	 */
	protected function encodeDirectDebitExt(model\DirectDebitExt $directDebitExt) {
		return implode($this->separator, [
			$this->encodeValue($directDebitExt->scheme),
			$this->encodeValue($directDebitExt->type),
			$this->encodeValue($directDebitExt->variableSymbol),
			$this->encodeValue($directDebitExt->specificSymbol),
			$this->encodeValue($directDebitExt->originatorsReferenceInformation),
			$this->encodeValue($directDebitExt->mandateId),
			$this->encodeValue($directDebitExt->creditorId),
			$this->encodeValue($directDebitExt->contractId),
			$this->encodeValue($directDebitExt->maxAmount),
			$this->encodeDate($directDebitExt->validTillDate),
		]);
	}


	/**
	 * Encode payment.
	 *
	 * @param model\Payment $payment - Payment
	 * @return string
	 */
	protected function encodePayment(model\Payment $payment) {
		$options = 0;
		if ($payment->paymentOrderOption) {
			$options |= 1;
		}
		if ($payment->standingOrderExt) {
			$options |= 2;
		}
		if ($payment->directDebitExt) {
			$options |= 4;
		}
		$values = [
			$this->encodeValue($options),
			$this->encodeValue($payment->amount),
			$this->encodeValue($payment->currencyCode),
			$this->encodeDate($payment->dueDate),
			$this->encodeValue($payment->variableSymbol),
			$this->encodeValue($payment->constantSymbol),
			$this->encodeValue($payment->specificSymbol),
			$this->encodeValue($payment->originatorsReferenceInformation),
			$this->encodeValue($payment->note),
			$this->encodeValue(count($payment->bankAccounts)),
		];
		foreach ($payment->bankAccounts as $bankAccount) {
			$values[] = $this->encodeBankAccount($bankAccount);
		}
		$values[] = $this->encodeValue($payment->standingOrderExt ? 1 : 0);
		if ($payment->standingOrderExt) {
			$values[] = $this->encodeStandingOrderExt($payment->standingOrderExt);
		}
		$values[] = $this->encodeValue($payment->directDebitExt ? 1 : 0);
		if ($payment->directDebitExt) {
			$values[] = $this->encodeDirectDebitExt($payment->directDebitExt);
		}
		return implode($this->separator, $values);
	}


	/**
	 * Encode pay document.
	 *
	 * @param model\Pay $pay - Pay document
	 * @return string
	 */
	public function encodePay(model\Pay $pay) {
		$values = [
			$this->encodeValue($pay->invoiceId),
			$this->encodeValue(count($pay->payments)),
		];
		foreach ($pay->payments as $payment) {
			$values[] = $this->encodePayment($payment);
		}
		return implode($this->separator, $values);
	}

}
