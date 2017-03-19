<?php

namespace com\peterbodnar\bsqr;

use com\peterbodnar\bsqr\model;
use com\peterbodnar\bsqr\utils\BsqrCoder;
use com\peterbodnar\bsqr\utils\BsqrCoderException;
use com\peterbodnar\bsqr\utils\BsqrRenderer;
use com\peterbodnar\mx2svg\MxToSvg;
use com\peterbodnar\qrcoder\QrCoder;
use com\peterbodnar\qrcoder\QrCoderException;
use com\peterbodnar\svg\Svg;



/**
 * Bysquare facade to encode and render bysqr document
 */
class BySquare {


	const LOGO_BOTTOM = BsqrRenderer::LOGO_BOTTOM;
	const LOGO_RIGHT = BsqrRenderer::LOGO_RIGHT;
	const LOGO_TOP = BsqrRenderer::LOGO_TOP;
	const LOGO_LEFT = BsqrRenderer::LOGO_LEFT;


	/** @var BsqrCoder */
	protected $bsqrCoder;
	/** @var QrCoder */
	protected $qrCoder;
	/** @var MxToSvg */
	protected $mx2svg;
	/** @var BsqrRenderer */
	protected $bsqrRenderer;


	public function __construct() {
		$this->bsqrCoder = new BsqrCoder();
		$this->qrCoder = new QrCoder();
		$this->mx2svg = new MxToSvg();
		$this->bsqrRenderer = new BsqrRenderer();
	}


	/**
	 * Set logo position {@see self::LOGO_*}.
	 *
	 * @param string $logoPosition - Logo position
	 */
	public function setLogoPosition($logoPosition) {
		$this->bsqrRenderer->setLogoPosition($logoPosition);
	}


	/**
	 * Render by square document to svg image.
	 *
	 * @param model\Document $document - By Square Document
	 * @return Svg
	 * @throws BySquareException
	 */
	public function render(model\Document $document) {
		try {
			$bsqrData = $this->bsqrCoder->encode($document);
			$qrMatrix = $this->qrCoder->encode($bsqrData);
			$qrSvg = $this->mx2svg->render($qrMatrix);
		} catch (BsqrCoderException $ex) {
			throw new BySquareException("Error while encoding bsqr document: " . $ex->getMessage(), 0, $ex);
		} catch (QrCoderException $ex) {
			throw new BySquareException("Error while encoding data to qr-code matrix: " . $ex->getMessage(), 0, $ex);
		}
		$this->bsqrRenderer->setQrCodeSvg($qrSvg);
		$this->bsqrRenderer->setQuiteAreaRatio(4 / $qrMatrix->getRows());
		if ($document instanceof model\Pay) {
			$this->bsqrRenderer->setLogo(BsqrRenderer::LOGO_PAY);
		} else {
			throw new BySquareException("Not supported");
		}
		try {
			return $this->bsqrRenderer->render();
		} catch (BySquareException $ex) {
			throw new BySquareException("Error while rendering bysquare image: " . $ex->getMessage(), 0, $ex);
		}
	}

}



/**
 * Exception thrown when encoding / rendering error occures.
 */
class BySquareException extends Exception { }
