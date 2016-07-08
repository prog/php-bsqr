<?php

namespace com\peterbodnar\bsqr\utils;

use com\peterbodnar\bsqr\Exception;
use com\peterbodnar\svg\Svg;



/**
 * Bysquare image renderer
 */
class BsqrRenderer {


	const LOGO_NONE = "NONE";
	const LOGO_PAY = "PAY";

	const LOGO_BOTTOM = "BOTTOM";
	const LOGO_RIGHT = "RIGHT";
	const LOGO_LEFT = "LEFT";
	const LOGO_TOP = "TOP";


	/** @var string */
	protected $unit = "";
	/** @var bool */
	protected $showBorder = TRUE;
	/** @var string */
	protected $logoPosition = self::LOGO_BOTTOM;
	/** @var string */
	protected $colorPrimary = "#6fa4d7";
	/** @var string */
	protected $colorSecondary = "#b0b3b8";
	/** @var string */
	protected $colorCode = "#000";

	/** @var string */
	protected $logo;
	/** @var Svg|null */
	protected $qrSvg;
	/** @var float|int */
	protected $qaRatio;


	/**
	 * Include svg image by specified name.
	 *
	 * @param string $name - Name of svg image
	 * @return string
	 */
	protected function includeSvg($name) {
		$res = preg_replace("~^<svg[^>]*?>(.*)</svg>$~", "\${1}", file_get_contents(__DIR__ . "/../../res/" . $name));
		$res = str_replace(["{primary}", "{secondary}"], [$this->colorPrimary, $this->colorSecondary], $res);
		return $res;
	}


	/**
	 * Set QR code svg image.
	 *
	 * @param Svg $qrCodeSvg - QR code svg
	 * @param float|null $qaRatio - Ratio of quite area size to qrcode square size
	 * @return void
	 */
	public function setQrCodeSvg(Svg $qrCodeSvg, $qaRatio = null) {
		$this->qrSvg = $qrCodeSvg;
		if (null !== $qaRatio) {
			$this->setQuiteAreaRatio($qaRatio);
		}
	}


	/**
	 * Set ratio of quite area size to qrcode square size.
	 *
	 * @param float $qaRatio - Quite area size ratio
	 * @return void
	 */
	public function setQuiteAreaRatio($qaRatio) {
		$this->qaRatio = $qaRatio;
	}


	/**
	 * Set logo type and position.
	 *
	 * @param string|LOGO_* $logo - Logo
	 * @param string|LOGO_*|null $logoPosition - Logo position
	 */
	public function setLogo($logo, $logoPosition = NULL) {
		$this->logo = $logo;
		if (NULL !== $logoPosition) {
			$this->logoPosition = $logoPosition;
		}
	}


	/**
	 * Set logo position.
	 *
	 * @param string $logoPosition
	 * @return void
	 */
	public function setLogoPosition($logoPosition) {
		$this->logoPosition = $logoPosition;
	}


	/**
	 * Set border.
	 *
	 * @param bool $showBorder - Set TRUE to render border.
	 * @return void
	 */
	public function setBorder($showBorder) {
		$this->showBorder = $showBorder;
	}


	/**
	 * Set colors.
	 *
	 * @param string $primaryColor - Primary color
	 * @param string $secondaryColor - Secondary color
	 * @return void
	 */
	public function setColors($primaryColor, $secondaryColor) {
		$this->colorPrimary = $primaryColor;
		$this->colorSecondary = $secondaryColor;
	}


	/**
	 * Render QR code.
	 *
	 * @param float[] $pos - Position
	 * @param float $size - Size
	 * @param float $rotate - Rotate (degrees)
	 * @param Svg $svg - Image to render.
	 * @return Svg
	 */
	protected function renderQrCode(array $pos, $size, $rotate, Svg $svg) {
		$transform = "translate(" . ($pos[0]) . "," . ($pos[1]) . ")";
		if (0 !== $rotate) {
			$transform .= " rotate(" . $rotate . "," . ($size / 2) . "," . ($size / 2) . ")";
		}
		return
			"<g transform=\"{$transform}\">" .
			((string) $svg->withSize($size, $size)) .
			"</g>";
	}


	/**
	 * Render border.
	 *
	 * @param float[] $pos - Position
	 * @param float $size - Size
	 * @param float $width - Line width
	 * @param bool $noLogo - Set true to keep no gap for logo
	 * @return string
	 */
	protected function renderBorder(array $pos, $size, $width, $noLogo) {
		$wh = $width * 0.5;
		$x1 = $pos[0] - $wh;
		$y1 = $pos[1] - $wh;
		$x2 = $pos[0] + $size + $wh;
		$y2 = $pos[1] + $size + $wh;
		$a = $wh + $size * 0.255;
		$b = $wh + $size * 0.045;

		if ($noLogo) {
			$path = "M{$x1} {$y1}V{$y2}H{$x2}V{$y1}z";
		} elseif (self::LOGO_LEFT === $this->logoPosition) {
			$path = "M{$x1} " . ($y1 + $a) . "V{$y2}H{$x2}V{$y1}H" . ($x1 + $b);
		} elseif (self::LOGO_RIGHT === $this->logoPosition) {
			$path = "M{$x2} " . ($y1 + $a) . "V{$y2}H{$x1}V{$y1}H" . ($x2 - $b);
		} elseif (self::LOGO_TOP === $this->logoPosition) {
			$path = "M". ($x2 - $a) . " {$y1}H{$x1}V{$y2}H{$x2}V" . ($y1 + $b);
		} else /* if (self::LOGO_BOTTOM === $this->logoPosition) */ {
			$path = "M". ($x2 - $a) . " {$y2}H{$x1}V{$y1}H{$x2}V" . ($y2 - $b);
		}
		return "<path d=\"{$path}\" style=\"fill:none;stroke:{$this->colorPrimary};stroke-width:{$width};stroke-linecap:round;stroke-linejoin:round\"/>";
	}


	/**
	 * Render logo.
	 *
	 * @param float[] $pos - Position
	 * @param float $size - Size
	 * @param bool $mirror - Set TRUE to render logo mirrored
	 * @param string $logo - Logo type
	 * @return string
	 * @throws BsqrRendererException
	 */
	protected function renderLogo(array $pos, $size, $mirror, $logo) {
		if (self::LOGO_PAY === $logo) {
			$resName = "pay-logo.svg";
		} else {
			throw new BsqrRendererException("not supported");
		}
		$scalex = $scaley = $size / 100.0;
		if ($mirror) {
			$pos[0] += $size;
			$scalex *= -1.0;
		}
		return
			"<g transform=\"translate({$pos[0]}, {$pos[1]}) scale({$scalex}, {$scaley})\">" .
			$this->includeSvg($resName) .
			"</g>";
	}


	/**
	 * Render logo caption.
	 *
	 * @param float[] $pos - Position
	 * @param float $size - Size
	 * @param string $logo - Logo type
	 * @return string
	 * @throws BsqrRendererException
	 */
	protected function renderCaption(array $pos, $size, $logo) {
		if (self::LOGO_PAY === $logo) {
			$resName = "pay-caption.svg";
		} else {
			throw new BsqrRendererException("not supported");
		}
		$scale = $size / 100.0;

		return
			"<g transform=\"translate({$pos[0]}, {$pos[1]}) scale({$scale})\">" .
			$this->includeSvg($resName) .
			"</g>";
	}


	/**
	 * Render svg image.
	 *
	 * @return Svg
	 * @throws BsqrRendererException
	 */
	public function render() {
		$noLogo = (self::LOGO_NONE === $this->logo);
		$baseSize = 1000.0;
		$logoSize = $baseSize * 0.213416;
		$captionSize = $baseSize * 0.790;
		$captionOffset = $baseSize * 0.053638;
		$bw = $baseSize * 0.0174;
		$bwh = $bw * 0.5;

		$size = [$baseSize, $baseSize];
		$basePos = [0.0, 0.0];
		$logoPos = null;
		$logoMirror = false;
		$captionPos = null;
		$qrRotate = 0;

		$logoOffset = $logoSize - $bwh;
		if ($this->showBorder) {
			$basePos[0] += $bw;
			$basePos[1] += $bw;
			$size[0] += 2 * $bw;
			$size[1] += 2 * $bw;
			$logoOffset -= $bw;
		}

		if ($noLogo) {
			//
		} elseif (self::LOGO_LEFT === $this->logoPosition) {
			$basePos[0] += $logoOffset;
			$logoPos = [0.0, $bwh];
			$logoMirror = true;
			$size[0] += $logoOffset;
			$qrRotate = 180;
		} elseif (self::LOGO_RIGHT === $this->logoPosition) {
			$logoPos = [$basePos[0] + $baseSize - $bwh, $bwh];
			$size[0] += $logoOffset;
			$qrRotate = 270;
		} elseif (self::LOGO_TOP === $this->logoPosition) {
			$basePos[1] += $logoOffset;
			$logoPos = [$basePos[0] + $baseSize - $logoSize + $bwh, $bwh];
			$size[1] += $logoOffset;
			$qrRotate = 270;
		} else /* if (self::LOGO_BOTTOM === $this->logoPosition) */ {
			$logoPos = [$basePos[0] + $baseSize - $logoSize + $bwh, $basePos[1] + $baseSize - $bwh];
			$captionPos = [$logoPos[0] - $captionSize, $logoPos[1] + $captionOffset]; // todo
			$size[1] += $logoOffset;
		}

		$content = "";
		if (NULL !== $this->qrSvg) {
			$qOffset = $baseSize * $this->qaRatio;
			$qrSize = $baseSize - 2 * $qOffset;
			$qrPos = [$basePos[0] + $qOffset, $basePos[1] + $qOffset];
			$content .= $this->renderQrCode($qrPos, $qrSize, $qrRotate, $this->qrSvg);
		}
		if ($this->showBorder) {
			$content .= $this->renderBorder($basePos, $baseSize, $bw, $noLogo);
		}
		if (!$noLogo) {
			$content .= $this->renderLogo($logoPos, $logoSize, $logoMirror, $this->logo);
			$content .= $this->renderCaption($captionPos, $captionSize, $this->logo);
		}
		return new Svg($content, ["viewBox" => "0 0 {$size[0]} {$size[1]}"]);
	}

}



/**
 * Exception thrown when
 */
class BsqrRendererException extends Exception { }
