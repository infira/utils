<?php

namespace Infira\Utils;

class Regex
{
	public static function getMatches(string $pattern, string $subject): ?string
	{
		$is = preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
		if ($is)
		{
			return $matches[0];
		}
		
		return null;
	}
	
	public static function getMatch(string $pattern, string $subject): ?string
	{
		$is = preg_match($pattern, $subject, $matches);
		if ($is)
		{
			return $matches[0];
		}
		
		return null;
	}
	
	/**
	 * has matches
	 *
	 * @param string $pattern
	 * @param string $subject
	 * @return bool
	 */
	public static function isMatch(string $pattern, string $subject): bool
	{
		return (bool)preg_match($pattern, $subject);
	}
	
	/**
	 * Is a regular expression
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function is(string $str): bool
	{
		return (bool)preg_match('/^\/.+?\/[a-z]*$/i', $str);
	}
	
	/**
	 * string to pattern
	 *
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
