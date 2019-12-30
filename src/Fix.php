<?php

namespace InfiraUtils;
/**
 * A class to fix values
 */
class Fix
{
	/**
	 * Remoes unneccesery characers from flename
	 *
	 * @param string $fileName
	 * @return string
	 */
	public static function fileName($fileName)
	{
		$ex = explode("?", $fileName);
		
		return trim($ex[0]);
	}
	
	/**
	 * Fixes <br> to <br />
	 *
	 * @param string $string
	 * @return string
	 */
	public static function br($string, $isXHTML = TRUE)
	{
		$br = ($isXHTML) ? "<br />" : "<br>";
		
		return preg_replace('/<[< ]*br[ \\\\\/]*>/i', $br, $string);
	}
	
	/**
	 * New line to br
	 *
	 * @param string $string
	 * @param bool $isXHTML
	 *            - is XHTML "<br />" used only in PHP 5.3.x
	 * @return string
	 */
	public static function nl2br($string, $isXHTML = TRUE, $fixBr = NULL)
	{
		if ($fixBr !== NULL)
		{
			$string = self::br($string, $isXHTML);
		}
		if (PHP_MAJOR_VERSION < 5 or (PHP_MAJOR_VERSION == 5 and PHP_MINOR_VERSION < 3))
		{
			$br = ($isXHTML) ? "<br />" : "<br>";
			
			return str_replace(["\r\n", "\n\r", "\n", "\r"], $br, $string);
		}
		else
		{
			return nl2br($string, $isXHTML);
		}
	}
	
	/**
	 * Fixes <br> to <br />
	 *
	 * @param string $string
	 * @return string
	 */
	public static function nl($string)
	{
		return str_replace('<br />', "\n", self::br($string, "<br />"));
	}
	
	/**
	 * Fix array, if $var is not array, then empty array is returned
	 */
	public static function arr($arr)
	{
		if (!is_array($arr))
		{
			return [];
		}
		
		return $arr;
	}
	
	/**
	 * Fix value with
	 *
	 * @param mixed $value
	 * @param string $with
	 *            mysql - mysqli_escape_string int - intval float - floatval bool - ($value) ? true : false string - "$value"; arratint - fix array values with intval
	 */
	public static function valueWith($value, $with = FALSE)
	{
		if ($with)
		{
			$ex = explode(",", $with);
			foreach ($ex as $w)
			{
				$w = trim(Variable::toLower($w));
				if (!is_array($value) and !is_object($value))
				{
					switch ($w)
					{
						case "trim" :
							$value = trim($value);
							break;
						case "mysql" :
							$value = Db::escape($value);
							break;
						case "int" :
							$value = intval($value);
							break;
						case "float" :
							$value = floatval(str_replace(",", ".", $value));
							break;
						case "excelnumber" :
							$value = str_replace(".", ",", Variable::toString(Variable::toNumber($value)));
							break;
						case "number" :
							$value = Variable::toNumber($value);
							break;
						case "bool" :
							$value = ($value) ? TRUE : FALSE;
							break;
						case "boolInt" :
							$value = ($value) ? 1 : 0;
							break;
						case "string" :
							$value = "$value";
						case "e" :
							$value = "$value";
							break;
						case "urlname" :
							$value = self::urlName($value);
							break;
						case "array" :
							$value = (is_array($value)) ? $value : [];
							break;
						case "date" :
							$value = Date::toDate($value);
							break;
						case "datetime" :
							$value = Date::toDateTime($value);
							break;
						case "sqldate" :
							$value = Date::toSqlDate($value);
							break;
						case "sqldatetime" :
							$value = Date::toSqlDateTime($value);
							break;
						case "arrayint" :
							if (checkArray($value))
							{
								foreach ($value as $k => $v)
								{
									$value[$k] = intval($v);
								}
							}
							break;
					}
				}
				else
				{
					switch ($w)
					{
						case "array" :
							$value = (is_array($value)) ? $value : [];
							break;
						case "arrayint" :
							if (checkArray($value))
							{
								foreach ($value as $k => $v)
								{
									$value[$k] = intval($v);
								}
							}
							break;
					}
				}
			}
		}
		
		return $value;
	}
	
	public static function urlName($str)
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
	
	/**
	 * Fix link
	 *
	 * @param string $string
	 * @return string
	 */
	public static function link($string)
	{
		$string = trim($string);
		if (substr($string, 0, 4) != "http" and substr($string, 0, 3) != "ftp" and substr($string, 0, 6) != "mailto")
		{
			$s = "";
			if (defined("HTTPS_ENABLED"))
			{
				if (HTTPS_ENABLED == TRUE)
				{
					$s = "s";
				}
			}
			
			return "http$s://" . $string;
		}
		
		return $string;
	}
	
	/**
	 * Fix and format price
	 *
	 * @param numeric $price
	 * @param bool $removeTenth
	 */
	public static function price($price, $removeTenth = TRUE, $removeZeros = FALSE)
	{
		$price = number_format($price, 2, ',', '');
		if ($removeZeros)
		{
			$price = str_replace(",00", "", $price);
		}
		
		return $price;
	}
	
	/**
	 * Fix date and time format,
	 *
	 * @param string $date
	 * @param string $time
	 */
	public static function dateTime($date, $time)
	{
		if (trim($date))
		{
			$return = $date;
			if (trim($time))
			{
				if (strpos($time, ':'))
				{
					$explodeTime = explode(':', $time);
					if (trim($explodeTime[0]) and $explodeTime[1])
					{
						if (strlen($explodeTime[0]) == 1)
						{
							$explodeTime[0] = '0' . $explodeTime[0];
						}
						if (strlen($explodeTime[1]) == 1)
						{
							$explodeTime[1] .= '0';
						}
						$return .= ' ' . $explodeTime[0] . ':' . $explodeTime[1] . ':00';
					}
					else
					{
						$return .= ' 00:00:00';
					}
				}
				else
				{
					$return .= ' 00:00:00';
				}
			}
			else
			{
				$return .= ' 00:00:00';
			}
		}
		else
		{
			$return = 'NULL';
		}
		
		return $return;
	}
	
	/**
	 * Add slash to end if neccessary
	 */
	public static function dirPath($path)
	{
		if ($path) //if empty reutrn empty
		{
			if (is_file($path))
			{
				return $path;
			}
			$path = str_replace("/", DIRECTORY_SEPARATOR, $path);
			$len = strlen($path) - 1;
			if ($path{$len} != DIRECTORY_SEPARATOR and !is_file($path))
			{
				$path .= DIRECTORY_SEPARATOR;
			}
		}
		
		return $path;
	}
	
	/**
	 * Add slash to end if neccessary
	 */
	public static function phone($phone, $prefix = "")
	{
		$phone = trim((string)$phone);
		$prefix = trim((string)$prefix);
		if (strlen($phone) > 0)
		{
			$phone = preg_replace('/\s+/', '', $phone);
			$phone = preg_replace("/[^0-9]/", "", $phone);
			if ($prefix)
			{
				if ($prefix{0} == "+")
				{
					$prefix = substr($prefix, 1);
				}
				if (substr($phone, 0, strlen($prefix)) == $prefix)
				{
					return "+" . $phone;
				}
				else
				{
					$phone = "+" . $prefix . $phone;
				}
			}
		}
		
		return $phone;
	}
}

?>