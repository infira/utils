<?php

namespace Infira\Utils;

use stdClass;

/**
 * This class handles date strings
 */
class Date
{
	/**
	 * convert to timestamp
	 *
	 * @param string|int $time
	 * @param string|int $now - use base time or string, defaults to now ($now is converted to time)
	 * @return int - converted timestamp
	 */
	public static function toTime(string $time, $now = null): int
	{
		if (preg_match('/\D/i', $time))
		{
			$now  = ($now === null) ? time() : self::toTime($now);
			$time = strtotime($time, $now);
		}
		else
		{
			$time = intval($time);
		}
		
		return $time;
	}
	
	/**
	 * Get date time from $time with $now as basetime,
	 * uses strtime
	 * Used of() instead
	 *
	 * @param string|int $time
	 * @param null       $now - use base time or string, defaults to now ($now is converted to time)
	 * @throws \Exception
	 * @return DateTime
	 * @deprecated
	 */
	public static function from(string $time, $now = null): DateTime
	{
		$dt = new DateTime();
		$dt->setTimestamp(self::toTime($time, $now));
		
		return $dt;
	}
	
	/**
	 * Constructs DateTime objet
	 *
	 * @param string             $datetime
	 * @param \DateTimeZone|null $timezone
	 * @throws \Exception
	 * @return DateTime
	 */
	public static function of(string $datetime = 'now', ?\DateTimeZone $timezone = null): DateTime
	{
		$ts = null;
		if (is_numeric($datetime))
		{
			$ts = intval($datetime);
			$datetime = 'now';
		}
		$tm = new DateTime($datetime, $timezone);
		if ($ts !== null)
		{
			$tm->setTimestamp($ts);
		}
		return $tm;
	}
	
	/**
	 * Get days,years,hours,minutes,seconds from time
	 *
	 * @param string|int $str
	 * @return stdClass
	 */
	public static function seconds2human(string $str): stdClass
	{
		$time           = self::toTime($str);
		$value          = new stdClass();
		$value->years   = 0;
		$value->days    = 0;
		$value->hours   = 0;
		$value->minutes = 0;
		$value->seconds = 0;
		if ($time >= 31556926)
		{
			$value->years = floor($time / 31556926);
			$time         = ($time % 31556926);
		}
		if ($time >= 86400)
		{
			$value->days = floor($time / 86400);
			$time        = ($time % 86400);
		}
		if ($time >= 3600)
		{
			$value->hours = floor($time / 3600);
			$time         = ($time % 3600);
		}
		if ($time >= 60)
		{
			$value->minutes = floor($time / 60);
			$time           = ($time % 60);
		}
		$value->seconds = floor($time);
		
		return $value;
	}
	
	/**
	 * Get last of the month date
	 *
	 * @param string|null $date - date to time
	 * @return int
	 */
	public static function lastDayOfMonth(string $date = null): int
	{
		$time = self::toTime($date);
		
		return self::toTime(date("Y-m-t", $time));
	}
	
	/**
	 * Count days between dates
	 * $ignore ignore day numbers like sunday = 7
	 *
	 * @param string|null $startDate - null means now
	 * @param string|null $endDate   - null means now
	 * @param array       $ignore
	 * @throws \Exception
	 * @return int
	 */
	public static function daysBetween(string $startDate = null, string $endDate = null, array $ignore = []): int
	{
		$result    = 0;
		$startDate = self::from($startDate)->toSqlDate();
		$endDate   = self::from($endDate)->toSqlDate();
		while ($startDate != $endDate)
		{
			$time = self::toTime($startDate);
			if (!in_array(strftime("%u", $time), $ignore))
			{
				$result++;
			}
			$startDate = self::from("+1 day", $time)->toSqlDate();
		}
		
		return $result;
	}
	
	/**
	 * Get array range with dates
	 *
	 * @param        $startDate - null means now
	 * @param        $endDate   - null means now
	 * @param string $step      - how many times to add each step
	 * @param string $format    - format range item
	 * @return array
	 */
	public static function range($startDate, $endDate, string $step = '+1 day', string $format = 'd.m.Y'): array
	{
		$dates     = [];
		$startDate = self::toTime($startDate);
		$endDate   = self::toTime($endDate);
		
		while ($startDate <= $endDate)
		{
			if (is_callable($format))
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
	 * @throws \Exception
	 * @return bool
	 */
	public static function is(string $date): bool
	{
		$dateTime = self::from($date)->toSqlDate();
		if ($dateTime == "1970-01-01")
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Check is variable valid timestamp
	 *
	 * @param string|int $timestamp
	 * @return bool
	 */
	public static function isValidTimestamp(string $timestamp): bool
	{
		return (ctype_digit($timestamp) && strtotime(date('Y-m-d H:i:s', $timestamp)) === (int)$timestamp);
	}
}