<?php

namespace com\peterbodnar\bsqr\utils;

use com\peterbodnar\base32\Base32;
use com\peterbodnar\base32\Base32Exception;
use com\peterbodnar\bsqr\Exception;
use com\peterbodnar\bsqr\model;



/**
 * Bysquare data encoder / parser
 */
class BsqrCoder {


	/** @var Base32 - Base 32 encoder / decoder. */
	protected $base32;
	/** @var ClientDataEncoder - BySquare serializer. */
	protected $cldEncoder;
	/** @var ClientDataParser - BySquare parser. */
	protected $cldParser;
	/** @var Lzma - Lzma compressor / decompressor. */
	protected $lzma;


	/**
	 * CRC32 hash.
	 *
	 * @param string $data
	 * @return string
	 */
	protected function crc32hash($data) {
		return strrev(hash("crc32b", $data, TRUE));
	}


	public function __construct() {
		$this->base32 = new Base32("0123456789ABCDEFGHIJKLMNOPQRSTUV");
		$this->cldEncoder = new ClientDataEncoder();
		$this->cldParser = new ClientDataParser();
		$this->lzma = new Lzma();
	}


	/**
	 * Encode document.
	 *
	 * @param model\Document $document - Document to encode.
	 * @return string
	 * @throws BsqrCoderException
	 */
	public function encode(model\Document $document) {
		if ($document instanceof model\Pay) {
			$head = "\x00\x00";
			$clientData = $this->cldEncoder->encodePay($document);
		} else {
			throw new BsqrCoderException("Not supported");
		}

		$clDataHash = $this->crc32hash($clientData);
		try {
			$lzmEncoded = $this->lzma->compress($clDataHash . $clientData);
		} catch (LzmaException $ex) {
			throw new BsqrCoderException("LZMA compression failed: " . $ex->getMessage(), 0, $ex);
		}
		$b32encoded = $this->base32->encode($head . $lzmEncoded);

		return $b32encoded;
	}


	/**
	 * Parse document data.
	 *
	 * @param string $input - Data.
	 * @return model\Document
	 * @throws BsqrCoderException
	 */
	public function parse($input) {
		try {
			$b32decoded = $this->base32->decode($input);
		} catch (Base32Exception $ex) {
			throw new BsqrCoderException("Base 32 decoding failed: " . $ex->getMessage(), 0, $ex);
		}

		$head = substr($b32decoded, 0, 2);
		$body = substr($b32decoded, 2);
		if ("\x00\x00" === $head) {
			$documentClass = model\Pay::class;
		} else {
			throw new BsqrCoderException("Unknown document type (0x" . bin2hex($head) . ").");
		}

		try {
			$lzmDecoded = $this->lzma->decompress($body);
		} catch (LzmaException $ex) {
			throw new BsqrCoderException("LZMA decompression failed: " . $ex->getMessage(), 0, $ex);
		}

		$clDataHash = substr($lzmDecoded, 0, 4);
		$clientData = substr($lzmDecoded, 4);
		if ($this->crc32hash($clientData) !== $clDataHash) {
			throw new BsqrCoderException("CRC32 hash does not match.");
		}

		try {
			$document = $this->cldParser->parse($documentClass, $clientData);
		} catch (ParserException $ex) {
			throw new BsqrCoderException("Client data parsing failed.", 0, $ex);
		}
		return $document;
	}

}



/**
 * Exception thrown when document encoding / parsing error occures
 */
class BsqrCoderException extends Exception { }
