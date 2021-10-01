<?php

namespace Infira\Utils;

class Globals
{
	/**
	 * generate nice debug backtrace
	 *
	 * @param int $startAt
	 * @return string
	 */
	public static function getTrace(int $startAt = 0): string
	{
		$backTrace = debug_backtrace();
		$until     = 15;
		$trace     = "<br />";
		$start     = intval($startAt);
		$nr        = 1;
		for ($i = $start; $i <= $until; $i++)
		{
			if (isset($backTrace[$i]['file']))
			{
				$trace .= $nr . ') File ' . $backTrace[$i]['file'] . ' in line ' . $backTrace[$i]['line'] . '<br>';
				$nr++;
			}
		}
		
		return str_replace(getcwd(), "", $trace);
	}
}