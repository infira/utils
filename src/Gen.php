<?php

namespace Infira\Utils;

use Infira\Utils\Is as Is;

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
	
	public static function UUID($hashIT = false)
	{
		$uid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',        // 32 bits for
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
		if ($hashIT)
		{
			return md5($uid);
		}
		
		return $uid;
	}
	
	/**
	 * Generate random string
	 *
	 * @param int  $len
	 * @param bool $norepeat - characates cannot be repeated
	 * @return string
	 */
	public static function randomString(int $len, bool $norepeat = true)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$max   = strlen($chars) - 1;
		
		if ($norepeat && $len > $max + 1)
		{
			throw new Error("Non repetitive random string can't be longer than charset");
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
	 * @return string
	 */
	public static function cacheID()
	{
		//return md5(self::cacheString(func_get_args()));
		return hash("crc32b", self::cacheString(func_get_args()));
	}
	
	/**
	 * Generate cache string form aruments
	 *
	 * @param string|array $key
	 * @return string
	 */
	public static function cacheString($key, $arg1 = null): string
	{
		if ($arg1)
		{
			throw new Error("Cannot use multiple arguments");//cause get_func_args() causes performance issues
		}
		if (is_object($key))
		{
			if (!$key instanceof \stdClass)
			{
				throw new Error("cannot make cache ID from non stdClass object, its impact for performance");
			}
			$key = serialize($key);
		}
		elseif (is_array($key))
		{
			$key = serialize($key);
		}
		
		return $key;
	}
	
	public static function htmlParams($string)
	{
		return parseStr($string);
		
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

?>