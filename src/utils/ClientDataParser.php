<?php

namespace com\peterbodnar\bsqr\utils;

use com\peterbodnar\bsqr\Exception;
use com\peterbodnar\bsqr\model;



/**
 * BySquare client data parser
 */
class ClientDataParser {


	/** @var string */
	protected $separator = "\t";


	/**
	 * Parse value.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return string
	 * @throws ParserException
	 */
	protected function parseValue($input, &$pos = 0) {
		$len = strlen($input);
		if ($pos >= $len) {
			throw new ParserException("Unexpected end of input");
		}
		$nextSepPos = strpos($input, $this->separator, $pos);
		if (FALSE === $nextSepPos) {
			$result = substr($input, $pos);
			$pos = $len;
		} else {
			$result = substr($input, $pos, $nextSepPos - $pos);
			$pos = $nextSepPos + 1;
		}
		return $result;
	}


	/**
	 * Parse integer.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return int|null
	 * @throws ParserException
	 */
	protected function parseInt($input, &$pos = 0) {
		$value = $this->parseValue($input, $pos);
		if (NULL === $value) {
			return NULL;
		}
		$int = (int) $value;
		if ((string) $int !== $value) {
			throw new ParserException();
		}
		return $int;
	}


	/**
	 * Parse count (integer >= 0).
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return int
	 * @throws ParserException
	 */
	protected function parseCount($input, &$pos = 0) {
		$int = $this->parseInt($input, $pos);
		if (NULL === $int || $int < 0) {
			throw new ParserException();
		}
		return $int;
	}


	/**
	 * Parse boolean.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return bool
	 * @throws ParserException
	 */
	protected function parseBool($input, &$pos = 0) {
		$val = $this->parseValue($input, $pos);
		if ("0" !== $val && "1" !== $val) {
			throw new ParserException();
		}
		return (bool) $val;
	}


	/**
	 * Parse float.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return float|null
	 * @throws ParserException
	 */
	protected function parseFloat($input, &$pos = 0) {
		$value = $this->parseValue($input, $pos);
		if (NULL === $value) {
			return NULL;
		}
		$float = (float) $value;
		if ($float != $value) {
			throw new ParserException();
		}
		return $float;
	}


	/**
	 * Parse date.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return string|null
	 * @throws ParserException
	 */
	protected function parseDate($input, &$pos = 0) {
		$value = $this->parseValue($input, $pos);
		if (NULL === $value) {
			return NULL;
		} elseif (preg_match("~^([0-9]{4})([0-9]{2})([0-9]{2})$~", $value, $m)) {
			return "{$m[1]}-{$m[2]}-{$m[3]}";
		}
		throw new ParserException();
	}


	/**
	 * Parse bank account.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return model\BankAccount
	 * @throws ParserException
	 */
	protected function parseBankAccount($input, &$pos = 0) {
		$bankAccount = new model\BankAccount($input, $pos);
		$bankAccount->iban = $this->parseValue($input, $pos);
		$bankAccount->bic = $this->parseValue($input, $pos);
		return $bankAccount;
	}


	/**
	 * Parse standing order extension.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return model\StandingOrderExt
	 * @throws ParserException
	 */
	protected function parseStandingOrderExt($input, &$pos = 0) {

		// todo

		throw new ParserException("Not implemented.");
	}


	/**
	 * Parse direct debit extension.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return model\DirectDebitExt
	 * @throws ParserException
	 */
	protected function parseDirectDebitExt($input, &$pos = 0) {

		// todo

		throw new ParserException("Not implemented.");
	}


	/**
	 * Parse payment.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return model\Payment
	 * @throws ParserException
	 */
	protected function parsePayment($input, &$pos = 0) {
		$payment = new model\Payment();

		$options = $this->parseInt($input, $pos);
		if (NULL === $options) {
			throw new ParserException("Unexpected empty value");
		}

		// $hasDirectDebitExt = (bool) ($options & 4);
		// $hasStandingOrderExt = (bool) ($options & 2);
		$payment->paymentOrderOption = (bool) ($options & 1);
		$payment->amount = $this->parseFloat($input, $pos);
		$payment->currencyCode = $this->parseValue($input, $pos);
		$payment->dueDate = $this->parseDate($input, $pos);
		$payment->variableSymbol = $this->parseValue($input, $pos);
		$payment->constantSymbol = $this->parseValue($input, $pos);
		$payment->specificSymbol = $this->parseValue($input, $pos);
		$payment->originatorsReferenceInformation = $this->parseValue($input, $pos);
		$payment->note = $this->parseValue($input, $pos);

		$bankAccountsCount = $this->parseCount($input, $pos);
		for ($i=0; $i<$bankAccountsCount; $i++) {
			$payment->bankAccounts[] = $this->parseBankAccount($input, $pos);
		}
		if ($this->parseBool($input, $pos)) {
			$payment->standingOrderExt = $this->parseStandingOrderExt($input, $pos);
		}
		if ($this->parseBool($input, $pos)) {
			$payment->directDebitExt = $this->parseDirectDebitExt($input, $pos);
		}
		return $payment;
	}


	/**
	 * Parse pay document.
	 *
	 * @param string $input - Input string
	 * @param int& $pos - Cursor position
	 * @return model\Pay
	 * @throws ParserException
	 */
	protected function parsePay($input, &$pos = 0) {
		$pay = new model\Pay();

		$pay->invoiceId = $this->parseValue($input, $pos);
		$paymentsCount = $this->parseCount($input, $pos);
		for ($i=0; $i<$paymentsCount; $i++) {
			$pay->payments[] = $this->parsePayment($input, $pos);
		}
		return $pay;
	}


	/**
	 * Parse document of specified class.
	 *
	 * @param string $documentClass - Document class
	 * @param string $data - Input data
	 * @return model\Document
	 * @throws ParserException
	 */
	public function parse($documentClass, $data) {
		$pos = 0;
		if (model\Pay::class === $documentClass) {
			$result = $this->parsePay($data, $pos);
		} else {
			throw new ParserException("Not supported document class: " . $documentClass);
		}
		if (strlen($data) !== $pos) {
			throw new ParserException("Extraneous data after parsed document.");
		}
		return $result;
	}

}



/**
 * Exception thrown when client data parsing error occures
 */
class ParserException extends Exception { }
