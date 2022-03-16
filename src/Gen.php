<?php

namespace Infira\Utils;

use Exception;

class Gen
{
	
	/**
	 * generate document no from ID
	 *
	 * @param int $documentID
	 * @param int $length
	 * @return string
	 */
	public static function docNo(int $documentID, int $length = 6): string
	{
		return str_repeat("0", ($length - strlen($documentID))) . "" . $documentID;
	}
	
	public static function UUID(): string
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',        // 32 bits for
			// "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			
			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),
			
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,
			
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,
			
			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
	}
	
	/**
	 * Generate random string
	 *
	 * @param int  $len
	 * @param bool $norepeat - characates cannot be repeated
	 * @return string
	 */
	public static function randomString(int $len, bool $norepeat = true): string
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$max   = strlen($chars) - 1;
		
		if ($norepeat && $len > $max + 1)
		{
			throw new Exception("Non repetitive random string can't be longer than charset");
		}
		
		$rand_chars = [];
		
		while ($len)
		{
			$picked = $chars[mt_rand(0, $max)];
			
			if ($norepeat)
			{
				if (!array_key_exists($picked, $rand_chars))
				{
					$rand_chars[$picked] = true;
					$len--;
				}
			}
			else
			{
				$rand_chars[] = $picked;
				$len--;
			}
		}
		
		return implode('', $norepeat ? array_keys($rand_chars) : $rand_chars);
	}
	
	/**
	 * Generate 32bit string from method argumetns, use for cache string
	 *
	 * @throws \ReflectionException
	 * @return string
	 */
	public static function cacheID(...$vars)
	{
		//return md5(self::cacheString(func_get_args()));
		return hash("crc32b", self::cacheString(...$vars));
	}
	
	/**
	 * Generate cache string form any variable
	 *
	 * @param mixed $key
	 * @throws \ReflectionException
	 * @return string
	 */
	public static function cacheString(...$hashable): string
	{
		$output = [];
		foreach ($hashable as $value) {
			if ($value instanceof \Closure) {
				$value = (new \ReflectionFunction($value))->__toString();
				$value = preg_replace('/\@\@.+/', '', $value);//remove file location
				$value = self::hashable($value);
			}
			elseif (is_object($value)) {
				$value = serialize($value);
			}
			elseif (is_array($value)) {
				$arr   = $value;
				$value = [];
				foreach ($arr as $key => $v) {
					$value[] = self::hashable($key) . '-' . self::hashable($v);
				}
				$value = join('-', $value);
			}
			elseif (!is_string($value)) {
				$value = var_export($value, true);
			}
			$output[] = preg_replace('![\s]+!u', '', $value);
		}
		
		return join('-', $output);
	}
	
	/**
	 * Generate reference number for banks
	 *
	 * @param int $number
	 * @return int
	 */
	public static function referenceNumber(int $number): int
	{
		$svn     = "$number";
		$weights = [7, 3, 1];
		$count   = 0;
		$sum     = 0;
		for ($i = strlen($svn) - 1; $i >= 0; $i--)
		{
			$sum += $weights[$count % 3] * $svn[$i];
			$count++;
		}
		$check = (10 - ($sum % 10)) % 10;
		
		return "$number$check";
	}
}