<?php

namespace InfiraUtils;

use stdClass as stdClass;

/**
 * This class handles date strings
 */
class Date
{
	
	/**
	 * Convert string to date by formation
	 *
	 * @param string $date
	 * @param string $dateFormat - defaults to d.m.Y
	 * @return string
	 */
	public static function toDate($date, $dateFormat = 'd.m.Y')
	{
		if (strpos($dateFormat, "%") !== FALSE)
		{
			return strftime($dateFormat, self::toTime($date));
		}
		else
		{
			return date($dateFormat, self::toTime($date));
		}
	}
	
	/**
	 * Get days,yeasr,hours,minutes,seconds from time
	 *
	 * @param string|int $str
	 * @return stdClass
	 */
	public static function seconds2human($str)
	{
		$time = self::toTime($str);
		$value = new stdClass();
		$value->years = 0;
		$value->days = 0;
		$value->hours = 0;
		$value->minutes = 0;
		$value->seconds = 0;
		if ($time >= 31556926)
		{
			$value->years = floor($time / 31556926);
			$time = ($time % 31556926);
		}
		if ($time >= 86400)
		{
			$value->days = floor($time / 86400);
			$time = ($time % 86400);
		}
		if ($time >= 3600)
		{
			$value->hours = floor($time / 3600);
			$time = ($time % 3600);
		}
		if ($time >= 60)
		{
			$value->minutes = floor($time / 60);
			$time = ($time % 60);
		}
		$value->seconds = floor($time);
		
		return $value;
	}
	
	/**
	 * Convert string to timestamp
	 *
	 * @param string|int $time
	 * @param string|int $now - use base time or string, defaults to now ($now is converted to time)
	 * @return int - converted timestamp
	 */
	public static function toTime($time, $now = NULL)
	{
		if (preg_match('/\D/i', $time))
		{
			$now = ($now === NULL) ? time() : self::toTime($now);
			$time = strtotime($time, $now);
		}
		else
		{
			$time = intval($time);
		}
		
		return $time;
	}
	
	/**
	 * Convert to time with format H:i
	 *
	 * @param string $date
	 * @return string - date H:i
	 */
	public static function toTimeNice($date)
	{
		return self::toDate($date, "H:i");
	}
	
	/**
	 * Convert to date with format d.m.Y H:i:s
	 *
	 * @param string $date
	 * @return string - date d.m.Y H:i:s
	 */
	public static function toDateTime($date)
	{
		return self::toDate($date, "d.m.Y H:i:s");
	}
	
	/**
	 * Convert to sql date with format Y-m-d
	 *
	 * @param string $date
	 * @return string - date Y-m-d
	 */
	public static function toSqlDate($date)
	{
		return self::toDate($date, "Y-m-d");
	}
	
	/**
	 * Convert to sql date&time with format Y-m-d H:i:s
	 *
	 * @param string $date
	 * @return string - date Y-m-d H:i:s
	 */
	public static function toSqlDateTime($date)
	{
		return self::toDate($date, "Y-m-d H:i:s");
	}
	
	/**
	 * Get now date with format d.m.Y
	 *
	 * @return string - date d.m.Y
	 */
	public static function nowDate()
	{
		return self::toDate(time(), "d.m.Y");
	}
	
	/**
	 * Get now date with format d.m.Y H:i:s
	 *
	 * @return string - date d.m.Y H:i:s
	 */
	public static function nowDateTime()
	{
		return self::toDateTime(time(), "d.m.Y H:i:s");
	}
	
	/**
	 * Get now date for mysql with format Y-m-d
	 *
	 * @return string - date Y-m-d
	 */
	public static function nowSqlDate()
	{
		return self::toSqlDate(time());
	}
	
	/**
	 * Get now date for mysql with format Y-m-d H:i:s
	 *
	 * @return string - date Y-m-d H:i:s
	 */
	public static function nowSqlDateTime()
	{
		return self::toSqlDateTime(time());
	}
	
	/**
	 * Is time/date in past
	 *
	 * @param string $date - date or time
	 * @return bool
	 */
	public static function isPast($date)
	{
		$now = time();
		$dateTime = self::toTime($date);
		if ($dateTime < $now)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Is time/date
	 *
	 * @param string $date - date or time
	 * @return bool
	 */
	public static function isFuture($date)
	{
		$now = time();
		$dateTime = self::toTime($date);
		if ($dateTime > $now)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Is date/time now
	 *
	 * @param string $date - date or time
	 * @return bool
	 */
	public static function isNow($date)
	{
		$dateTime = self::toTime($date);
		if ($dateTime == time())
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Get last of the month date
	 * @param string $date - date to time
	 * @return int
	 */
	public static function lastDayOfMonth($date = NULL)
	{
		$time = self::toTime($date);
		
		return self::toTime(date("Y-m-t", $time));
	}
	
	/**
	 * Count days between daates
	 * $ignore ignore day numbers like sunday = 7
	 * @param string|null $startDate - null means now
	 * @param string|null $endDate - null means now
	 * @param array $ignore
	 * @return int
	 */
	public static function daysBetwewen($startDate = NULL, $endDate = NULL, $ignore = [])
	{
		$result = 0;
		$startDate = self::toSqlDate($startDate);
		$endDate = self::toSqlDate($endDate);
		while ($startDate != $endDate)
		{
			$time = self::toTime($startDate);
			if (!in_array(strftime("%u", $time), $ignore))
			{
				$result++;
			}
			$startDate = self::toSqlDate(strtotime("+1 day", $time));
		}
		
		return $result;
	}
	
	/**
	 * Get array range with dates
	 * @param $startDate - null means now
	 * @param $endDate - null means now
	 * @param string $step - how many time to add each step
	 * @param string $format - format range item
	 * @return array
	 */
	public static function range($startDate, $endDate, $step = '+1 day', $format = 'd.m.Y')
	{
		$dates = [];
		$startDate = self::toTime($startDate);
		$endDate = self::toTime($endDate);
		
		while ($startDate <= $endDate)
		{
			if (is_object($format) && ($format instanceof Closure))
			{
				$val = $format($startDate);
				if (is_object($val))
				{
					$dates[$val->value] = $val->label;
				}
				else
				{
					$dates[] = $val;
				}
			}
			else
			{
				$dates[] = date($format, $startDate);
			}
			$startDate = strtotime($step, $startDate);
		}
		
		return $dates;
	}
	
	/**
	 * if $date is actual date
	 *
	 * @param string $date
	 * @return bool
	 */
	public static function is($date)
	{
		$dateTime = self::toSqlDate($date);
		if ($dateTime == "1970-01-01")
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Check is variable valid timestamp
	 * @param $timestamp
	 * @return bool
	 */
	public static function isValidTimestamp($timestamp)
	{
		return (ctype_digit($timestamp) && strtotime(date('Y-m-d H:i:s', $timestamp)) === (int)$timestamp);
	}
}

?>