<?php

namespace Infira\Utils;

class Is
{
	/**
	 * Deletes file
	 *
	 * @param $email
	 * @return bool
	 */
	public static function email($email)
	{
		// First, we check that there's one @ symbol, and that the lengths are right
		if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email))
		{
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++)
		{
			if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i]))
			{
				return false;
			}
		}
		if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1]))
		{ // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2)
			{
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++)
			{
				if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i]))
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	
	/**
	 * Is $val float or int
	 *
	 * @param mixed $val
	 * @return bool
	 */
	public static function number($val)
	{
		if (is_int($val) or is_numeric($val))
		{
			return true;
		}
		else
		{
			//^\d+?(\.|,)\d+?$
			if (Regex::isMatch('/^\d+?(\.|,)\d+?$/m', (string)$val))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Function to check is the given value a date
	 *
	 * @param mixed $val
	 * @return bool
	 */
	public static function date($val)
	{
		if (empty($val))
		{
			return false;
		}
		$time = trim(strtotime($val));
		if (empty($time))
		{
			return false;
		}
		$date = date("d.m.Y", strtotime($val));
		if (Is::match('/\\d{2}.\\d{2}.\\d{4}/', $date))
		{
			$ex = explode(".", $date);
			if (checkdate($ex[1], $ex[0], $ex[2]))
			{
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * Function to check is the given value a date
	 *
	 * @param mixed $val
	 * @return bool
	 */
	public static function time($val)
	{
		$val = trim($val);
		if (empty($val))
		{
			return false;
		}
		
		return preg_match('/\\d\\d:\\d\\d/', $val);
	}
	
	
	/**
	 * Function to check is given value match to given preg_ regeq
	 *
	 * @param string $regex
	 * @param mixed  $val
	 * @return bool
	 */
	public static function match(string $regex, $val)
	{
		return Regex::isMatch($regex, $val);
	}
	
	
	/**
	 * Check if the @param mixed $var
	 *
	 * @param string $className
	 * @return boolean
	 */
	public static function isClass($var, string $className)
	{
		if (!is_object($var))
		{
			return false;
		}
		
		return $var instanceof $className;
	}
	
	
	/**
	 * Check if the $nr is between $from AND $to
	 *
	 * @param number $nr
	 *            - nr to check
	 * @param number $from
	 *            - between start
	 * @param number $to
	 *            - between end
	 * @return boolean
	 */
	public static function between($nr, $from, $to)
	{
		return ($nr >= $from and $nr <= $to);
	}
	
	/**
	 * Check if is json
	 *
	 * @param $string
	 * @return bool
	 */
	public static function json($string)
	{
		json_decode($string);
		
		return (json_last_error() == JSON_ERROR_NONE);
	}
}

?>