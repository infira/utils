<?php

namespace Infira\Utils;

class Vat
{
	/**
	 * Get vat percent calculation nr
	 *
	 * @param null $vatPercent
	 * @return float|int
	 */
	private static function getPercent(float $vatPercent = null)
	{
		$vatP = $vatPercent;
		if ($vatP === null)
		{
			if (defined('GLOBAL_VAT_PERCENT'))
			{
				$vatP = GLOBAL_VAT_PERCENT;
			}
			else
			{
				$vatP = 20;
			}
		}
		
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
			$output = $amount - ($amount / self::getPercent($vatPercent));
		}
		else
		{
			$output = ($amount * self::getPercent($vatPercent)) - $amount;
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

?>