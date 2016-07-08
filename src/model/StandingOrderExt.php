<?php

namespace com\peterbodnar\bsqr\model;



/**
 * Standing order extension.
 * Extends basic payment information with information required for standing order setup.
 */
class StandingOrderExt extends Element {


	/** @var int|null - This is the payment day. It‘s meaning depends on the periodicity, meaning either day of the month (number between 1 and 31) or day of the week (1=Monday, 2=Tuesday, …, 7=Sunday). */
	public $day;
	/** @var  - Selection of months on which payment occurs. If used, set periodicity to "Annually". If payment occurs every month or every other month consider setting periodicity to "Monthly" or "Bimonthly" instead. */
	public $month;
	/** @var  - Periodicity of the standing order. */
	public $periodicity;
	/** @var string|null - Date of the last payment of the standing order */
	public $lastDate;

}
