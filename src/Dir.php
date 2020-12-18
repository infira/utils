<?php declare(strict_types=1);

namespace Infira\Utils;

use Infira\Utils\Variable as Variable;
use Infira\Utils\File as File;

class Dir
{
	
	/**
	 * Delete a folder/directory
	 *
	 * @param string $path
	 * @return bool
	 */
	public static function delete(string $path): bool
	{
		if (is_dir($path))
		{
			$openDir = scandir($path);
			if (is_array($openDir) && count($openDir) > 0)
			{
				self::flush($path);
			}
			rmdir($path);
		}
		
		return true;
	}
	
	/**
	 * Flush folder content
	 *
	 * @param string $path
	 * @param array  $exlude
	 * @return bool
	 */
	public static function flush(string $path, array $exlude = []): bool
	{
		if (is_dir($path))
		{
			$openDir = self::getContents($path, $exlude);
			if (is_array($openDir) && count($openDir) > 0)
			{
				foreach ($openDir as $file)
				{
					if (!in_array($file, [".", ".."]))
					{
						if (substr($path, -1) != '/')
						{
							$path .= '/';
						}
						if (is_dir($path . $file))
						{
							self::delete($path . $file);
						}
						else
						{
							File::delete($path . $file);
						}
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * If folder doest no exists make it
	 *
	 * @param string $path
	 * @param int    $chmod - chod it
	 * @return string created dir path
	 */
	public static function make(string $path, int $chmod = 0777): string
	{
		if (!file_exists($path))
		{
			mkdir($path, $chmod, true);
		}
		
		return $path;
	}
	
	/** get path content
	 *
	 * @param string $path
	 * @param array  $exlude
	 * @param bool   $recursive
	 * @param bool   $absolutePath - get absolute paths
	 * @return array
	 */
	public static function getContents(string $path, $exlude = [], bool $recursive = false, bool $absolutePath = false): array
	{
		$exlude   = Variable::toArray($exlude);
		$exlude[] = ".";
		$exlude[] = "..";
		if (is_dir($path))
		{
			if ($recursive)
			{
				$getRecursive = function ($path, $mainPrefix = "") use (&$exlude, &$getRecursive)
				{
					$r  = scandir($path);
					$nr = [];
					foreach ($r as $p)
					{
						
						if ($mainPrefix)
						{
							$prefix = $mainPrefix . DIRECTORY_SEPARATOR . $p;
						}
						else
						{
							$prefix = $p;
						}
						if (!in_array($p, [".", ".."]))
						{
							$nr[] = $prefix;
							$pp   = $path . DIRECTORY_SEPARATOR . $p;
							if (is_dir($pp))
							{
								$nr = array_merge($nr, $getRecursive($pp, $prefix));
							}
						}
					}
					
					return $nr;
				};
				$r            = $getRecursive($path);
			}
			else
			{
				$r = scandir($path);
			}
			if (checkArray($exlude))
			{
				foreach ($exlude as $ex)
				{
					if ($ex[0] == "/")
					{
						$gr = preg_grep($ex, $r);
						if (checkArray($gr))
						{
							foreach ($gr as $gk => $gv)
							{
								unset($r[$gk]);
							}
						}
					}
					else
					{
						$key = array_search($ex, $r);
						if ($key >= 0)
						{
							unset($r[$key]);
						}
					}
				}
			}
			
			$r = array_values($r);
			if ($absolutePath)
			{
				array_walk($r, function (&$item, $key) use (&$path)
				{
					$item = realpath($path) . DIRECTORY_SEPARATOR . $item;
				});
			}
			
			return $r;
		}
		
		return [];
	}
}

?>