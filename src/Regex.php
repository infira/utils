<?php

namespace InfiraUtils;

class Regex
{
	/**
	 * @param string $regex - regular expression pattern
	 * @param string $subject - where to find matches
	 * @param bool $returnOnFalse - return that if not found
	 * @return mixed
	 */
	public static function getMatches(string $regex, string $subject, $returnOnFalse = FALSE)
	{
		if (preg_match($regex, $subject))
		{
			$matches = [];
			preg_match_all($regex, $subject, $matches);
			
			return $matches[0];
		}
		else
		{
			return $returnOnFalse;
		}
	}
	
	/**
	 * Get a single match
	 * @param string $regex - regular expression pattern
	 * @param string $subject - where to find matches
	 * @param int $nr
	 * @param bool $returnOnFalse
	 * @return mixed
	 */
	public static function getMatch(string $regex, string $subject, int $nr = 0, $returnOnFalse = FALSE)
	{
		$matches = self::getMatches($regex, $subject);
		if (is_array($matches) and count($matches) > 0)
		{
			if (isset($matches[$nr]))
			{
				return trim($matches[$nr]);
			}
		}
		
		return $returnOnFalse;
	}
	
	/**
	 * Function to check is given value match to given preg_ regeq
	 *
	 * @param string $regex - regular expression pattern
	 * @param string $subject - where to find matches
	 * @return bool
	 */
	public static function isMatch($regex, $subject)
	{
		$matches = self::getMatches($regex, $subject);
		if (is_array($matches) and count($matches) > 0)
		{
			if (isset($matches[0]))
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Is a regular expression
	 * @param $str
	 * @return bool
	 */
	public static function is(string $str): bool
	{
		$regex = "/^\/[\s\S]+\/$/";
		
		return (preg_match($regex, $str)) ? TRUE : FALSE;
	}
	
	/**
	 * string to pattern
	 * @param string $pattern
	 * @return string
	 */
	public static function fix(string $pattern): string
	{
		if (!self::is($pattern))
		{
			$pattern = '/' . preg_quote($pattern, "/") . '/';
		}
		
		return $pattern;
	}
}

?>