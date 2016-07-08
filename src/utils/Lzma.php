<?php

namespace com\peterbodnar\bsqr\utils;

use com\peterbodnar\bsqr\Exception;
use com\peterbodnar\cmd\Command;
use com\peterbodnar\cmd\CommandException;



/**
 * BySquare Lzma compressor / decompressor
 */
class Lzma {


	/** @var Command */
	protected $command;
	/** @var string[] */
	protected $xzArgs;


	/**
	 * @param string $xzPath - Path to xz executable
	 */
	public function __construct($xzPath = "/usr/bin/xz") {
		$this->command = new Command($xzPath);
		$this->xzArgs = [
			"--format" => "raw",
			"--lzma1" => "lc=3,lp=0,pb=2,dict=32KiB"
		];
	}


	/**
	 * Compress data.
	 *
	 * @param string $data - Data to compress
	 * @return string
	 * @throws LzmaException
	 */
	public function compress($data) {
		$errorMsg = "Data compression failed";
		$sizeBytesLE = pack("v", strlen($data));

		$args = array_merge($this->xzArgs, ["-c", "-"]);
		try {
			$result = $this->command->execute($args, $data);
		} catch (CommandException $ex) {
			throw new LzmaException($errorMsg . ": " . $ex->getMessage(), 0, $ex);
		}
		$errorMsg .= " [" . $result->exitCode . "]";
		if ("" !== $result->stdErr) {
			throw new LzmaException($errorMsg . ": " . $result->stdErr);
		}
		if (0 !== $result->exitCode) {
			throw new LzmaException($errorMsg);
		}
		return $sizeBytesLE . $result->stdOut;
	}


	/**
	 * Decompress data.
	 *
	 * @param string $data - Compressed data
	 * @return string
	 * @throws LzmaException
	 */
	public function decompress($data) {
		$errorMsg = "Data decompression failed";
		$sizeBytesLE = substr($data, 0, 2);
		$dataCompressed = substr($data, 2);
		$size = unpack("v", $sizeBytesLE)[1];

		$args = array_merge($this->xzArgs, ["--decompress", "-c", "-"]);
		try {
			$result = $this->command->execute($args, $dataCompressed);
		} catch (CommandException $ex) {
			throw new LzmaException($errorMsg . ": " . $ex->getMessage(), 0, $ex);
		}
		if (strlen($result->stdOut) === $size) {
			return $result->stdOut;
		}
		$errorMsg .= " [" . $result->exitCode . "]";
		if ("" !== $result->stdErr) {
			$errorMsg .= ": " . $result->stdErr;
		}
		throw new LzmaException($errorMsg);
	}

}



/**
 * Exception thrown when lzma compression / decompression error occures
 */
class LzmaException extends Exception { }
