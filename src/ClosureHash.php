<?php

use Infira\Utils\Gen;

class ClosureHash
{
	/**
	 * List of hashes
	 *
	 * @var SplObjectStorage
	 */
	protected static $hashes = null;
	
	/**
	 * Returns a hash for closure
	 *
	 * @param Closure $closure
	 * @throws ReflectionException
	 * @return string
	 */
	public static function from(Closure $closure)
	{
		if (!self::$hashes)
		{
			self::$hashes = new SplObjectStorage();
		}
		
		if (!isset(self::$hashes[$closure]))
		{
			$ref  = new ReflectionFunction($closure);
			$file = new SplFileObject($ref->getFileName());
			$file->seek($ref->getStartLine() - 1);
			$content = '';
			while ($file->key() < $ref->getEndLine())
			{
				$content .= $file->current();
				$file->next();
			}
			self::$hashes[$closure] = Gen::cacheID([$content, $ref->getStaticVariables()]);
		}
		
		return self::$hashes[$closure];
	}
}