<?php

namespace Infira\Utils;

class Vat
{
	private static $percent = 20;
	
	public static function setPercent(float $percent)
	{
		self::$percent = $percent;
	}
	
	/**
	 * Get vat percent calculation nr
	 *
	 * @param float|null $vatPercent
	 * @return float
	 */
	private static function getCalcPercent(float $vatPercent = null)
	{
		$vatP = $vatPercent == null ? self::$percent : $vatPercent;
		
		return ($vatP / 100) + 1;
	}
	
	/**
	 * Get vat amount
	 *
	 * @param int|float      $amount            - amount without vat
	 * @param bool           $amountContainsVat - amount without vat
	 * @param int|float|null $vatPercent        - percent of vat, if null then default is used
	 * @return float|int
	 */
	public static function get($amount, bool $amountContainsVat, $vatPercent = null)
	{
		$amount = floatval($amount);
		if ($amountContainsVat == true)
		{
			$output = $amount - ($amount / self::getCalcPercent($vatPercent));
		}
		else
		{
			$output = ($amount * self::getCalcPercent($vatPercent)) - $amount;
		}
		
		return $output;
	}
	
	
	/**
	 * Add vat to amount
	 *
	 * @param int|float      $net        - amount without vat
	 * @param int|float|null $vatPercent - percent of vat, if null then default is used
	 * @return int|float
	 */
	public static function add($net, float $vatPercent = null)
	{
		return $net + self::get($net, false, $vatPercent);
	}
	
	/**
	 * Remove vat from amount
	 *
	 * @param int|float      $gross      - amount with vat
	 * @param int|float|null $vatPercent - percent of vat, if null then default is used
	 * @return int|float
	 */
	public static function remove(float $gross, float $vatPercent = null)
	{
		return $gross - self::get($gross, true, $vatPercent);
	}
}