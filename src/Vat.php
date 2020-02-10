<?php

namespace InfiraUtils;
class Vat
{
	/**
	 * Get vat percent calculation nr
	 *
	 * @param null $vatPercent
	 * @return float|int
	 */
	private static function getPercent($vatPercent = NULL)
	{
		$vatP = $vatPercent;
		if ($vatP === NULL)
		{
			$vatP = VatPercent::getVatPercent();
		}
		
		return ($vatP / 100) + 1;
	}
	
	
	/**
	 * Add or substract vat from $amount
	 *
	 * @param int|float      $pamount           - amount without vat
	 * @param bool           $amountContainsVat - amount without vat
	 * @param int|float|null $vatPercent        - percent of vat, if null then default is used
	 * @return float|int
	 */
	public static function get($pamount, $amountContainsVat, $vatPercent = NULL)
	{
		$pamount = floatval($pamount);
		if ($amountContainsVat == TRUE)
		{
			$output = $pamount - ($pamount / self::getPercent($vatPercent));
		}
		else
		{
			$output = ($pamount * self::getPercent($vatPercent)) - $pamount;
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
	public static function add($net, $vatPercent = NULL)
	{
		return $net + self::get($net, FALSE, $vatPercent);
	}
	
	/**
	 * Remove vat from amount
	 *
	 * @param int|float      $gross      - amount with vat
	 * @param int|float|null $vatPercent - percent of vat, if null then default is used
	 * @return int|float
	 */
	public static function remove($gross, $vatPercent = NULL)
	{
		return $gross - self::get($gross, TRUE, $vatPercent);
	}
}

?>