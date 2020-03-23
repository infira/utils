<?php

namespace Infira\Utils;

/**
 * A collection of static methods to manipulate with variables
 */
class Variable
{
	
	/**
	 * Apply some values to ibject
	 *
	 * @param array /object $toVariable
	 *            The receiver of the properties
	 * @param array /object $values
	 *            The source of the properties
	 * @param array /object $default
	 *            A different object that will also be applied for default values
	 */
	public static function apply($toVariable = FALSE, $values = [], $default = [])
	{
		if ($toVariable === FALSE or $toVariable == "new" or $toVariable == "empty" or $toVariable == "{}")
		{
			$toVariable = new \stdClass();
		}
		$returnAsObject = FALSE;
		if (is_object($toVariable))
		{
			$returnAsObject = TRUE;
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
	 * @param unknown $str
	 */
	public static function lastChar($str)
	{
		$len = strlen($str);
		if ($len > 0)
		{
			$ll = $len - 1;
			
			return $str{$ll};
		}
		
		return FALSE;
	}
	
	/**
	 * Assigns string variables to tekst
	 *
	 * @param array  $vars
	 * @param string $string
	 * @return $string
	 */
	public static function assign($vars, $string, $defaultVars = FALSE)
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
		if ($defaultVars !== FALSE)
		{
			$string = self::assign($defaultVars, $string, FALSE);
		}
		
		return $string;
	}
	
	/**
	 * Convert variable valut to array
	 *
	 * @param mixed  $var
	 * @param string $caseStringExplodeDelim
	 *            - if the $var type is string then string is exploded to this param delimiter
	 * @return array
	 */
	public static function toArray($var, $recursive = FALSE, $caseStringExplodeDelim = ",")
	{
		if ($recursive == FALSE)
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
				$hadNotArray = FALSE;
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
				return self::toArray($var, FALSE, $caseStringExplodeDelim);
			}
			else
			{
				return [];
			}
		}
	}
	
	public static function toIntArray($var, $recursive = FALSE, $caseStringExplodeDelim = ",")
	{
		$arr    = self::toArray($var, $recursive, $caseStringExplodeDelim);
		$newArr = [];
		array_walk($arr, function (&$value, $key) use (&$newArr)
		{
			$newArr[$key] = intval($value);
		});
		
		return $newArr;
	}
	
	public static function toArrayHasValue($var, $recursive = FALSE, $caseStringExplodeDelim = ",")
	{
		$arr    = self::toArray($var, $recursive, $caseStringExplodeDelim);
		$newArr = [];
		array_walk($arr, function (&$value, $key) use (&$newArr)
		{
			if ($value)
			{
				$newArr[$key] = $value;
			}
		});
		
		return $newArr;
	}
	
	/**
	 * Parse array and add surrounding char to each element
	 *
	 * @param array  $array
	 * @param string $surrounding
	 */
	public static function addSurroundinsToArrayEl($array, $surrounding = "'")
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
	
	public static function __toArrayOld($var, $recursive = FALSE, $caseStringExplodeDelim = ",")
	{
		if (is_array($var))
		{
			$r = $var;
		}
		elseif (is_object($var))
		{
			$r = get_object_vars($var);
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
					if ($v)
					{
						$r[] = $v;
					}
				}
			}
		}
		else
		{
			$r = [];
		}
		if (count($r) > 0 and $recursive == TRUE)
		{
			foreach ($r as $key => $val)
			{
				if (is_object($val))
				{
					$r[$key] = self::__toArrayOld($val, TRUE);
				}
				elseif (is_array($val))
				{
					$r[$key] = [];
					foreach ($val as $k => $v)
					{
						if (is_object($v) or is_array($v))
						{
							$r[$key] = self::__toArrayOld($val, TRUE);
						}
						else
						{
							$r[$key][$k] = $v;
						}
					}
				}
				else
				{
					$r[$key] = $val;
				}
			}
		}
		
		return $r;
	}
	
	/**
	 * Array to stc class object
	 *
	 * @param mixed $var
	 * @param bool  $recursive
	 *            The array("name"=>"gen") result should be Object->name = "gen"
	 * @return stdClass
	 */
	public static function toObject($var, $recursive = FALSE)
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
		$Return = new \stdClass();
		if (is_array($var) and count($var) > 0)
		{
			$var = (object)$var;
			
			return self::toObject($var, $recursive);
		}
		
		return $Return;
	}
	
	/**
	 * Array to array containg std objects
	 *
	 * @param mixed $var
	 * @param bool  $recursive
	 *            The array("name"=>"gen") result should be Object->name = "gen"
	 * @return stdClass
	 */
	public static function toObjectArray($var, $recursive = FALSE)
	{
		foreach ($var as $key => $val)
		{
			$var[$key] = self::toObject($val, $recursive);
		}
		
		return $var;
	}
	
	/**
	 * Convert data to string
	 *
	 * @param mixed $var
	 * @return string
	 */
	public static function toString($var)
	{
		return (string)$var;
	}
	
	/**
	 * Convert data to string
	 *
	 * @param mixed $var
	 * @return string
	 */
	public static function boolToString($var)
	{
		if ($var === TRUE)
		{
			return "true";
		}
		elseif ($var === FALSE)
		{
			return "false";
		}
		
		return "$var";
	}
	
	/**
	 * Coneverts variable to boolean value
	 *
	 * @param mixed $var
	 * @return bool
	 */
	public static function toBool($var, $parseAlsoString = FALSE)
	{
		if ($parseAlsoString == TRUE)
		{
			$lowerVal = self::toLower($var);
			if ($lowerVal == "true")
			{
				return TRUE;
			}
			elseif ($lowerVal == "false")
			{
				return FALSE;
			}
		}
		
		return (boolean)$var;
	}
	
	/**
	 * Coneverts variable to boolean value
	 *
	 * @param mixed $var
	 * @return bool
	 */
	public static function toBoolInt($var)
	{
		$var = self::toBool($var);
		
		return ($var) ? 1 : 0;
	}
	
	/**
	 * Converts variable value to numeric value
	 *
	 * @param mixed $val
	 * @return unknown
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
	
	public static function roundUpAny($value, $modBase = 5)
	{
		return ceil($value / $modBase) * $modBase;
	}
	
	public static function roundUpTo5Cents($n)
	{
		return self::roundUpAny($n, 0.05);
	}
	
	public static function truncateNumber($number, $precision = 2)
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
	public static function getDigitals($string)
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
	 * @param mixed $val
	 * @return number
	 */
	public static function toNegative($number)
	{
		return self::toNumber("-" . abs($number));
	}
	
	/**
	 * Convert number to positive
	 *
	 * @param mixed $val
	 * @return number
	 */
	public static function toPositive($number)
	{
		return abs($number);
	}
	
	/**
	 * Converts value to unix timestamp
	 *
	 * @param mixed $str
	 * @return int
	 */
	public static function toTime($str)
	{
		if (is_int($str))
		{
			return $str;
		}
		else
		{
			return strtotime($str);
		}
	}
	
	/**
	 * Create reqular exrpession for preg_ functon
	 *
	 * @param string $string
	 * @return string
	 */
	public static function toPrefex($string)
	{
		return "/" . $string . "/";
	}
	
	public static function capitalize($string)
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
	public static function toUpper($var)
	{
		return mb_convert_case($var, MB_CASE_UPPER, "UTF-8");
	}
	
	/**
	 * Convert string to camel case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function toCamelCase($var)
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
	public static function toLower($var)
	{
		return mb_convert_case($var, MB_CASE_LOWER, "UTF-8");
	}
	
	/**
	 * Convert string to lower case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function lcFirst($var)
	{
		$r = self::toLower($var{0});
		if (strlen($var) > 0)
		{
			$r .= substr($var, 1);
		}
		
		return $r;
	}
	
	/**
	 * Convert string to lower case
	 *
	 * @param string $var
	 * @return string
	 */
	public static function ucFirst($string, $encoding = 'UTF-8')
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
	public static function toASCII($string)
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
	public static function toUTF8($string)
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
	 * @param string $str
	 * @param string/array $voidTags - html tags to void on stripping, see http://php.net/manual/en/function.strip-tags.php
	 * @return string
	 */
	public static function htmlToText($str, $voidTags = FALSE)
	{
		if ($voidTags === FALSE)
		{
			$voidTags = "";
		}
		if (is_string($voidTags) AND strlen($voidTags) > 0 AND $voidTags{0} != "<")
		{
			$voidTags = Variable::toArray($voidTags);
		}
		if (checkArray($voidTags))
		{
			$voidTags = eachArray($voidTags, function ($key, $item)
			{
				return '<' . $item . '>';
			});
			$voidTags = join("", $voidTags);
		}
		
		return trim(html_entity_decode(strip_tags($str, $voidTags), ENT_QUOTES, "UTF-8"));
	}
	
	/**
	 * Converts to megabytes
	 */
	public static function toMB($var)
	{
		return $var / 1048576;
	}
	
	public static function getObjArrValue($obj, $name, $error = FALSE)
	{
		if (is_array($obj))
		{
			if ($error !== FALSE and !isset($obj[$name]))
			{
				throw new \Error("Variable::getObjArrValue ei leitud '$name'");
			}
			
			return $obj[$name];
		}
		elseif (is_object($obj))
		{
			if ($error !== FALSE and !isset($obj->$name))
			{
				throw new \Error("Variable::getObjArrValue ei leitud '$name'");
			}
			
			return $obj->$name;
		}
		
		return $obj;
	}
	
	public static function urlEncode($string)
	{
		/*
		 * urlencode function and rawurlencode are mostly based on RFC 1738. However, since 2005 the current RFC in use for URIs standard is RFC 3986. Here is a function to encode URLs according to RFC 3986.
		 */
		$entities     = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D'];
		$replacements = ['!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]"];
		
		return str_replace($entities, $replacements, urlencode($string));
	}
	
	public static function urlDecode($string)
	{
		$replacements = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D'];
		$entities     = ['!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]"];
		
		return str_replace($entities, $replacements, urlencode($string));
	}
}

?>