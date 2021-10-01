<?php

namespace Infira\Utils;

class ClosureHash
{
	/**
	 * List of hashes
	 *
	 * @var \SplObjectStorage
	 */
	protected static $hashes = null;
	
	/**
	 * Returns a hash for closure
	 *
	 * @param \Closure $closure
	 * @throws \ReflectionException
	 * @return string
	 */
	public static function from(\Closure $closure): string
	{
		if (!self::$hashes)
		{
			self::$hashes = new \SplObjectStorage();
		}
		
		if (!isset(self::$hashes[$closure]))
		{
			$ref = new \ReflectionFunction($closure);
			
			self::$hashes[$closure] = md5(Gen::cacheString([$ref->__toString(), $ref->getStaticVariables()]));
		}
		
		return self::$hashes[$closure];
	}
}