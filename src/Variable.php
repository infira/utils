<?php

namespace Infira\Utils;

/**
 * A collection of static methods to manipulate with variables
 */
class Variable
{
	/**
	 * convert variable to array
	 *
	 * @param string|object|array|numeric $var
	 * @param string                      $caseStringExplodeDelim - if the $var type is string then string is exploded to this param delimiter
	 * @return array
	 */
	public static function toArray($var, string $caseStringExplodeDelim = ","): array
	{
		if (is_object($var))
		{
			return get_object_vars($var);
		}
		if (is_string($var) or is_numeric($var))
		{
			$ex = explode($caseStringExplodeDelim, "$var");
			$r  = [];
			if (is_array($ex))
			{
				foreach ($ex as $v)
				{
					$v = trim($v);
					if ($v != "")
					{
						$r[] = $v;
					}
				}
			}
			
			return $r;
		}
		if (is_array($var))
		{
			return $var;
		}
		
		return [];
	}
	
	//region number manipulations
	
	/**
	 * converts variable value to numeric value
	 *
	 * @param string|numeric $val
	 * @return float|int
	 */
	public static function toNumber($val)
	{
		$val = trim((string)$val);
		$val = str_replace(",", ".", $val);
		if (strpos($val, ".") > 0)
		{
			return floatval(str_replace(",", ".", $val));
		}
		else
		{
			return intval($val);
		}
	}
	
	/**
	 * @param float $value
	 * @param float $modBase
	 * @return float|int
	 */
	public static function roundUpAny(float $value, float $modBase = 5)
	{
		return ceil($value / $modBase) * $modBase;
	}
	
	/**
	 * @param float $n
	 * @return float
	 */
	public static function roundUpTo5Cents(float $n)
	{
		return self::roundUpAny($n, 0.05);
	}
	
	/**
	 * convert number to negative
	 *
	 * @param float $number
	 * @return float
	 */
	public static function toNegative(float $number): float
	{
		return self::toNumber("-" . abs($number));
	}
	
	/**
	 * convert number to positive
	 *
	 * @param float $number
	 * @return float
	 */
	public static function toPositive(float $number): float
	{
		return abs($number);
	}
	//endregion
	
	//region string manipulations
	/**
	 * Simple string templating
	 *
	 * @param array      $vars
	 * @param string     $string
	 * @param array|null $defaultVars
	 * @return mixed|string|string[] $string
	 */
	public static function assign(array $vars, string $string, array $defaultVars = null): string
	{
		foreach ($vars as $name => $value)
		{
			$string = str_replace(['%' . $name . '%', '[' . $name . ']'], $value, $string);
		}
		if ($defaultVars)
		{
			$string = self::assign($defaultVars, $string, null);
		}
		
		return $string;
	}
	
	/**
	 * convert string encoding to UTF8
	 *
	 * @param string $string
	 * @return string
	 */
	public static function toUTF8(string $string): string
	{
		$string = trim($string);
		// return mb_convert_encoding($string, 'UTF-8');
		$encoding_list = 'UTF-8, ISO-8859-13, ISO-8859-1, ASCII, UTF-7';
		if (mb_detect_encoding($string, $encoding_list) == 'UTF-8')
		{
			return $string;
		}
		
		return mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string, $encoding_list));
	}
	
	/**
	 * convert string to lower case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function toLower(string $var): string
	{
		return mb_convert_case($var, MB_CASE_LOWER, "UTF-8");
	}
	
	/**
	 * convert string to upper case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function toUpper(string $var): string
	{
		return mb_convert_case($var, MB_CASE_UPPER, "UTF-8");
	}
	
	/**
	 * convert string to url usable string
	 *
	 * @param string $str
	 * @return string
	 */
	public static function toSlug(string $str): string
	{
		// $str = preg_replace('/\\(|\\)|\\?|,|:|;|\\.|>|<|&|\'|"|!|\\+|’|\\|/', '', $str);
		// $str = str_replace(array('"', "'", "“", "„", "”", "@", "™", "®", "»", "«", "%","*"), '', $str);
		
		// pl
		$str = str_replace(['Ą', 'ą'], 'a', $str);
		$str = str_replace(['Ć', 'ć'], 'c', $str);
		$str = str_replace(['Ę', 'ę'], 'e', $str);
		$str = str_replace(['Ł', 'ł'], 'l', $str);
		$str = str_replace(['Ń', 'ń'], 'n', $str);
		$str = str_replace(['Ó', 'ó'], 'o', $str);
		$str = str_replace(['Ś', 'ś'], 's', $str);
		$str = str_replace(['Ź', 'ź'], 'z', $str);
		$str = str_replace(['Ż', 'ż'], 'z', $str);
		
		// ru
		$str = str_replace(['А', 'а'], 'a', $str);
		$str = str_replace(['Б', 'б'], 'b', $str);
		$str = str_replace(['В', 'в'], 'v', $str);
		$str = str_replace(['Г', 'г'], 'g', $str);
		$str = str_replace(['Д', 'д'], 'd', $str);
		$str = str_replace(['Е', 'е'], 'e', $str);
		$str = str_replace(['Ё', 'ё'], 'jo', $str);
		$str = str_replace(['Ж', 'ж'], 'z', $str);
		$str = str_replace(['З', 'з'], 'z', $str);
		$str = str_replace(['И', 'и'], 'i', $str);
		$str = str_replace(['Й', 'й'], 'i', $str);
		$str = str_replace(['К', 'к'], 'k', $str);
		$str = str_replace(['Л', 'л'], 'l', $str);
		$str = str_replace(['М', 'м'], 'm', $str);
		$str = str_replace(['Н', 'н'], 'n', $str);
		$str = str_replace(['О', 'о'], 'o', $str);
		$str = str_replace(['П', 'п'], 'p', $str);
		$str = str_replace(['Р', 'р'], 'p', $str);
		$str = str_replace(['С', 'с'], 's', $str);
		$str = str_replace(['Т', 'т'], 't', $str);
		$str = str_replace(['У', 'у'], 'u', $str);
		$str = str_replace(['Ф', 'ф'], 'f', $str);
		$str = str_replace(['Х', 'х'], 'h', $str);
		$str = str_replace(['Ц', 'ц'], 'c', $str);
		$str = str_replace(['Ч', 'ч'], 'ch', $str);
		$str = str_replace(['Ш', 'ш'], 'sh', $str);
		$str = str_replace(['Щ', 'щ'], '', $str);
		$str = str_replace(['Ъ', 'ъ'], '', $str);
		$str = str_replace(['Ы', 'ы'], 'õ', $str);
		$str = str_replace(['Ь', 'ь'], '', $str);
		$str = str_replace(['Э', 'э'], 'e', $str);
		$str = str_replace(['Ю', 'ю'], 'ju', $str);
		$str = str_replace(['Я', 'я'], 'ja', $str);
		
		
		// lt stuff
		$str = str_replace(['Į', 'į', "į"], 'i', $str);
		$str = str_replace(['Ė', 'ė', "ė"], 'e', $str);
		$str = str_replace(['Ą', 'ą'], 'a', $str);
		$str = str_replace(['Č', 'č'], 'c', $str);
		$str = str_replace(['Ę', 'ę'], 'e', $str);
		$str = str_replace(['Ė', 'ė'], 'e', $str);
		
		// lv stuff
		$str = str_replace(['Ā', 'ā'], 'a', $str);
		$str = str_replace(['Č', 'č'], 'c', $str);
		$str = str_replace(['Ē', 'ē'], 'e', $str);
		$str = str_replace(['Ģ', 'ģ'], 'g', $str);
		$str = str_replace(['Ī', 'ī'], 'i', $str);
		$str = str_replace(['Ķ', 'ķ'], 'k', $str);
		$str = str_replace(['Ļ', 'ļ'], 'l', $str);
		$str = str_replace(['Ņ', 'ņ'], 'n', $str);
		$str = str_replace(['Š', 'š'], 's', $str);
		$str = str_replace(['Ž', 'ž'], 'z', $str);
		$str = str_replace(['Ū', 'ū'], 'u', $str);
		$str = str_replace(['Š', 'š', "š"], 's', $str);
		$str = str_replace(['J', 'j', "j"], 'j', $str);
		$str = str_replace(['Ā', 'ā', "ā"], 'a', $str);
		$str = str_replace(['Ā', 'ā', "ā"], 'a', $str);
		$str = str_replace(['Ī', 'ī', "ī"], 'i', $str);
		$str = str_replace(['Ģ', 'ģ', "ģ"], 'g', $str);
		$str = str_replace(['Ļ', 'ļ', "ļ"], 'i', $str);
		$str = str_replace(['Ņ', 'ņ', "ņ"], 'n', $str);
		$str = str_replace(['Ę', 'ę', "ę"], 'e', $str);
		
		
		// eestikeelsed tähed
		
		$str = str_replace(['Ä', 'ä'], 'a', $str);
		$str = str_replace(['Ö', 'ö'], 'o', $str);
		$str = str_replace(['Õ', 'õ'], 'o', $str);
		$str = str_replace(['Ü', 'ü'], 'u', $str);
		
		$str = str_replace('\'', '/', $str);
		
		
		$str = str_replace('`', "", $str);
		
		
		// rootsi
		$str = str_replace(['Å', 'å', 'Ä', 'ä'], 'a', $str);
		$str = str_replace(['Ö', 'ö'], 'a', $str);
		// saksa
		$str = str_replace(['ß'], '', $str);
		
		// norrway
		$str = str_replace(['Æ', 'æ'], 'ae', $str);
		$str = str_replace(['Ø', 'ø'], 'o', $str);
		$str = str_replace(['Å', 'å'], 'a', $str);
		
		// muu
		$str = str_replace(["Ô", "ô"], "o", $str);
		
		$str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
		
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[_| -]+/", '-', $clean);
		
		
		return $clean;
	}
	//endregion
	
	//region else
	/**
	 * Dump variable into printable string
	 *
	 * @param mixed $variable
	 * @return string
	 */
	public static function dump($variable): string
	{
		if (is_array($variable) or is_object($variable))
		{
			return print_r($variable, true);
		}
		else
		{
			ob_start();
			var_dump($variable);
			
			return ob_get_clean();
		}
	}
	//endregion
}