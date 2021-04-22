<?php

namespace Infira\Utils;

use stdClass;

/**
 * A collection of static methods to manipulate with variables
 */
class Variable
{
	
	/**
	 * Apply some values to ibject
	 *
	 * @param array|object|string|null $toVariable - The receiver of the properties
	 * @param array|object             $values     - The source of the properties
	 * @param array|object             $default    - A different object that will also be applied for default values
	 * @return array|stdClass
	 */
	public static function apply($toVariable = null, $values = [], $default = [])
	{
		if ($toVariable === null or $toVariable == "new" or $toVariable == "empty" or $toVariable == "{}")
		{
			$toVariable = new stdClass();
		}
		$returnAsObject = false;
		if (is_object($toVariable))
		{
			$returnAsObject = true;
		}
		
		$toVariable = self::toArray($toVariable);
		$values     = self::toArray($values);
		$default    = self::toArray($default);
		
		if (checkArray($values))
		{
			foreach ($values as $name => $value)
			{
				$toVariable[$name] = $value;
			}
		}
		if (checkArray($default))
		{
			foreach ($default as $name => $value)
			{
				if (!isset($toVariable[$name]))
				{
					$toVariable[$name] = $value;
				}
			}
		}
		if ($returnAsObject)
		{
			return self::toObject($toVariable);
		}
		else
		{
			return $toVariable;
		}
	}
	
	/**
	 * Get string last characr
	 *
	 * @param string
	 * @return null|string
	 */
	public static function lastChar(string $str): ?string
	{
		$len = strlen($str);
		if ($len > 0)
		{
			$ll = $len - 1;
			
			return $str[$ll];
		}
		
		return null;
	}
	
	/**
	 * Assigns string variables to tekst
	 *
	 * @param array      $vars
	 * @param string     $string
	 * @param array|null $defaultVars
	 * @return mixed|string|string[] $string
	 */
	public static function assign(array $vars, string $string, array $defaultVars = null): string
	{
		if (is_string($vars))
		{
			$vars = parseStr($vars);
		}
		if (checkArray($vars))
		{
			foreach ($vars as $name => $value)
			{
				$string = str_replace(['%' . $name . '%', '[' . $name . ']'], $value, $string);
			}
		}
		if ($defaultVars)
		{
			$string = self::assign($defaultVars, $string, null);
		}
		
		return $string;
	}
	
	/**
	 * Convert variable valut to array
	 *
	 * @param mixed  $var
	 * @param bool   $recursive
	 * @param string $caseStringExplodeDelim - if the $var type is string then string is exploded to this param delimiter
	 * @return array
	 */
	public static function toArray($var, bool $recursive = false, string $caseStringExplodeDelim = ","): array
	{
		if ($recursive == false)
		{
			if (is_object($var))
			{
				return get_object_vars($var);
			}
			elseif (is_string($var) or is_int($var) or is_numeric($var))
			{
				$ex = explode($caseStringExplodeDelim, "$var");
				$r  = [];
				if (checkArray($ex))
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
			elseif (is_array($var))
			{
				return $var;
			}
			else
			{
				return [];
			}
		}
		else
		{
			if (is_array($var) and count($var) > 0)
			{
				$hadNotArray = false;
				foreach ($var as $key => $val)
				{
					if (is_object($val))
					{
						$var[$key] = self::toArray($val, $recursive, $caseStringExplodeDelim);
					}
				}
				
				return $var;
			}
			elseif (is_object($var))
			{
				$var = get_object_vars($var);
				
				return self::toArray($var, $recursive, $caseStringExplodeDelim);
			}
			elseif (is_string($var) or is_int($var) or is_numeric($var))
			{
				return self::toArray($var, false, $caseStringExplodeDelim);
			}
			else
			{
				return [];
			}
		}
	}
	
	public static function toIntArray($var, bool $recursive = false, string $caseStringExplodeDelim = ","): array
	{
		$arr = self::toArray($var, $recursive, $caseStringExplodeDelim);
		array_walk($arr, function (&$value, $key) use (&$newArr)
		{
			$value = intval($value);
		});
		
		return $arr;
	}
	
	public static function removeEmptyArrayItems(array $arr): array
	{
		$arr = array_filter($arr, function ($item)
		{
			return !empty($item);
		});
	}
	
	/**
	 * Parse array and add surrounding char to each element
	 *
	 * @param array  $array
	 * @param string $surrounding
	 * @return array
	 */
	public static function addSurroundinsToArrayEl(array $array, string $surrounding = "'"): array
	{
		if (checkArray($array))
		{
			foreach ($array as $key => $val)
			{
				$array[$key] = $surrounding . $val . $surrounding;
			}
		}
		
		return $array;
	}
	
	/**
	 * Array to stc class object
	 *
	 * @param mixed $var
	 * @param bool  $recursive The array("name"=>"gen") result should be Object->name = "gen"
	 * @return stdClass
	 */
	public static function toObject($var, bool $recursive = false): stdClass
	{
		if (is_object($var))
		{
			if (Is::isClass($var, "stdClass") && $recursive)
			{
				$tmpVar = (array)$var;
				foreach ($tmpVar as $key => $value)
				{
					if (is_array($value))
					{
						$var->$key = self::toObject($value, $recursive);
					}
				}
			}
			
			return $var;
		}
		$Return = new stdClass();
		if (is_array($var) and count($var) > 0)
		{
			$var = (object)$var;
			
			return self::toObject($var, $recursive);
		}
		
		return $Return;
	}
	
	/**
	 * Convert data to string
	 *
	 * @param mixed $var
	 * @return string
	 */
	public static function toString($var): string
	{
		return (string)$var;
	}
	
	/**
	 * Convert data to string
	 *
	 * @param mixed $var
	 * @return string
	 */
	public static function boolToString($var): string
	{
		if ($var === true)
		{
			return "true";
		}
		elseif ($var === false)
		{
			return "false";
		}
		
		return "$var";
	}
	
	/**
	 * Coneverts variable to boolean value
	 *
	 * @param mixed $var
	 * @param bool  $parseAlsoString
	 * @return bool
	 */
	public static function toBool($var, bool $parseAlsoString = false): bool
	{
		if ($parseAlsoString == true)
		{
			$lowerVal = self::toLower($var);
			if ($lowerVal == "true")
			{
				return true;
			}
			elseif ($lowerVal == "false")
			{
				return false;
			}
		}
		
		return (boolean)$var;
	}
	
	/**
	 * Coneverts variable to boolean value
	 *
	 * @param mixed $var
	 * @return int
	 */
	public static function toBoolInt($var): int
	{
		return self::toBool($var) ? 1 : 0;
	}
	
	/**
	 * Converts variable value to numeric value
	 *
	 * @param mixed $val
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
	 * @param numeric $value
	 * @param numeric $modBase
	 * @return float|int
	 */
	public static function roundUpAny(float $value, float $modBase = 5)
	{
		return ceil($value / $modBase) * $modBase;
	}
	
	/**
	 * @param numeric $n
	 * @return numeric
	 */
	public static function roundUpTo5Cents(float $n)
	{
		return self::roundUpAny($n, 0.05);
	}
	
	/**
	 * @param numeric $number
	 * @param int     $precision
	 * @return float|int
	 */
	public static function truncateNumber(float $number, int $precision = 2)
	{
		// Zero causes issues, and no need to truncate
		if (0 == (int)$number)
		{
			return $number;
		}
		// Are we negative?
		$negative = $number / abs($number);
		// Cast the number to a positive to solve rounding
		$number = abs($number);
		// Calculate precision number for dividing / multiplying
		$precision = pow(10, $precision);
		
		// Run the math, re-applying the negative value to ensure returns correctly negative / positive
		return floor($number * $precision) / $precision * $negative;
	}
	
	/**
	 * Get digits from string
	 *
	 * @param string $string
	 * @return int
	 */
	public static function getDigitals(string $string)
	{
		$matches = Regex::getMatches('/[\\d]/', $string);
		$r       = "";
		if (checkArray($matches))
		{
			foreach ($matches as $digit)
			{
				$r .= $digit;
			}
		}
		
		return self::toNumber($r);
	}
	
	/**
	 * Conver number to negative
	 *
	 * @param numeric $number
	 * @return numeric
	 */
	public static function toNegative(float $number)
	{
		return self::toNumber("-" . abs($number));
	}
	
	/**
	 * Convert number to positive
	 *
	 * @param numeric $number
	 * @return numeric
	 */
	public static function toPositive(float $number)
	{
		return abs($number);
	}
	
	/**
	 * Converts value to unix timestamp
	 *
	 * @param numeric|string $var
	 * @return int
	 */
	public static function toTime($var): int
	{
		if (is_int($var))
		{
			return $var;
		}
		else
		{
			return strtotime($var);
		}
	}
	
	/**
	 * Create reqular exrpession for preg_ functon
	 *
	 * @param string $string
	 * @return string
	 */
	public static function toPrefex(string $string): string
	{
		return "/" . $string . "/";
	}
	
	public static function capitalize(string $string): string
	{
		$ex = explode(" ", $string);
		if (checkArray($ex))
		{
			foreach ($ex as $key => $val)
			{
				$ex[$key] = self::toUpper(substr($val, 0, 1)) . self::toLower(substr($val, 1));
			}
		}
		
		return join(" ", $ex);
	}
	
	/**
	 * Convert string to upper case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function toUpper(string $var): string
	{
		return mb_convert_case($var, MB_CASE_UPPER, "UTF-8");
	}
	
	/**
	 * Convert string to camel case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function toCamelCase(string $var): string
	{
		// -|_
		if (Regex::getMatch('/-|_/', $var))
		{
			$ex  = preg_split('/-|_/', $var);
			$var = "";
			foreach ($ex as $part)
			{
				$var .= ucfirst($part);
			}
		}
		
		return ucfirst($var);
	}
	
	/**
	 * Convert string to lower case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function toLower(string $var): string
	{
		return mb_convert_case($var, MB_CASE_LOWER, "UTF-8");
	}
	
	/**
	 * Convert string to lower case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function lcFirst(string $var): string
	{
		$r = self::toLower($var[0]);
		if (strlen($var) > 0)
		{
			$r .= substr($var, 1);
		}
		
		return $r;
	}
	
	/**
	 * Convert string to lower case
	 *
	 * @param string $string
	 * @param string $encoding
	 * @return string
	 */
	public static function ucFirst(string $string, $encoding = 'UTF-8'): string
	{
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then      = mb_substr($string, 1, mb_strlen($string, $encoding) - 1, $encoding);
		
		return mb_strtoupper($firstChar, $encoding) . $then;
	}
	
	/**
	 * COnvert string encondig to toUSASCII
	 *
	 * @param string $string
	 * @return string
	 */
	public static function toASCII(string $string): string
	{
		$string = trim($string);
		// return mb_convert_encoding($string, 'UTF-8');
		$encoding_list = 'UTF-8, ISO-8859-13, ISO-8859-1, ASCII, UTF-7';
		if (mb_detect_encoding($string, $encoding_list) == 'UTF-8')
		{
			return $string;
		}
		
		return mb_convert_encoding($string, 'ASCII', mb_detect_encoding($string, $encoding_list));
	}
	
	/**
	 * COnvert string encondig to UTF(
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
	 * Converts html to text
	 *
	 * @param string            $str
	 * @param null|string|array $voidTags
	 * @return string
	 */
	public static function htmlToText(string $str, $voidTags = null): string
	{
		if ($voidTags === false)
		{
			$voidTags = "";
		}
		if (is_string($voidTags) and strlen($voidTags) > 0 and $voidTags[0] != "<")
		{
			$voidTags = Variable::toArray($voidTags);
		}
		if (checkArray($voidTags))
		{
			array_walk($voidTags, function (&$item)
			{
				$item = '<' . $item . '>';
			});
			$voidTags = join("", $voidTags);
		}
		
		return trim(html_entity_decode(strip_tags($str, $voidTags), ENT_QUOTES, "UTF-8"));
	}
	
	/**
	 * Converts to megabytes
	 *
	 * @param numeric $var
	 * @return float|int
	 */
	public static function toMB(float $var)
	{
		return $var / 1048576;
	}
}

?>