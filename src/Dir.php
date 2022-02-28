<?php declare(strict_types=1);

namespace Infira\File;

class Dir
{
	/**
	 * Flush folder content
	 *
	 * @param string $path
	 * @param bool   $removePath
	 * @param array  $exclude array including regular expression patterns
	 */
	private static function doFlush(string $path, bool $removePath, array $exclude = [])
	{
		if (is_dir($path))
		{
			foreach (glob($path . '/*') as $file)
			{
				$excluded = false;
				foreach ($exclude as $pattern)
				{
					if (Regex::is($pattern))
					{
						if (preg_match($pattern, $file))
						{
							$excluded = true;
							break;
						}
					}
					else
					{
						if (strpos($file, $pattern) !== false)
						{
							$excluded = true;
							break;
						}
					}
				}
				if (!$excluded)
				{
					if (is_dir($file))
					{
						self::doFlush($file, true, $exclude);
					}
					else
					{
						unlink($file);
					}
				}
			}
			if ($removePath)
			{
				rmdir($path);
			}
		}
	}
	
	/**
	 * Delete a folder
	 *
	 * @param string $path
	 */
	public static function delete(string $path)
	{
		self::doFlush($path, true);
	}
	
	/**
	 * Flush folder content
	 *
	 * @param string $path
	 */
	public static function flush(string $path)
	{
		self::doFlush($path, false);
	}
	
	/**
	 * Flush folder content excluding some
	 *
	 * @param string $path
	 * @param array  $exclude array including regular expression patterns
	 */
	public static function flushExcept(string $path, array $exclude = [])
	{
		self::doFlush($path, false, $exclude);
	}
	
	/**
	 * If folder doest no exists make it
	 *
	 * @param string $path
	 * @param int    $chmod - chmod it
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
	
	/**
	 * @throws \Exception
	 */
	private static function scan(string $_path, bool $recursive, bool $includeFolders, bool $getAbsolutePaths, array $exclude = [], array $filterExtensions = []): array
	{
		$output = [];
		$path   = realpath($_path);
		if (is_bool($path))
		{
			throw new \Exception("$path folder does not exists");
		}
		if (!is_dir($path))
		{
			throw new \Exception("$path folder does not exists");
		}
		$pattern = $filterExtensions ? '*.{' . join(',', $filterExtensions) . '}' : '*';
		foreach (glob($path . '/' . $pattern, GLOB_BRACE) as $file)
		{
			$file     = realpath($file);
			$excluded = false;
			foreach ($exclude as $pattern)
			{
				if (Regex::is($pattern))
				{
					if (preg_match($pattern, $file))
					{
						$excluded = true;
						break;
					}
				}
				else
				{
					if (strpos($file, $pattern) !== false)
					{
						$excluded = true;
						break;
					}
				}
			}
			
			if (!$excluded)
			{
				$oFile = $getAbsolutePaths ? $file : basename($file);
				if (is_dir($file))
				{
					if ($includeFolders)
					{
						$output[] = $oFile . '/';
					}
					if ($recursive)
					{
						$output = array_merge($output, self::scan($file, $recursive, $includeFolders, $getAbsolutePaths, $exclude));
					}
				}
				else
				{
					$output[] = $oFile;
				}
			}
		}
		
		return $output;
	}
	
	/**
	 * Get files with absolute paths from folder
	 *
	 * @param string $path
	 * @param array  $filterExtensions get files only with these extensions
	 * @param array  $exclude
	 * @throws \Exception
	 * @return array
	 */
	public static function getFiles(string $path, array $filterExtensions = [], array $exclude = []): array
	{
		return self::scan($path, false, false, true, $exclude, $filterExtensions);
	}
	
	/**
	 * Get file names from folder
	 *
	 * @param string $path
	 * @param array  $filterExtensions get files only with these extensions
	 * @param array  $exclude
	 * @throws \Exception
	 * @return array
	 */
	public static function getFileNames(string $path, array $filterExtensions = [], array $exclude = []): array
	{
		return self::scan($path, false, false, false, $exclude, $filterExtensions);
	}
	
	/**
	 * Get files and sub folders inside path
	 *
	 * @param string $path
	 * @param array  $filterExtensions
	 * @param array  $exclude
	 * @throws \Exception
	 * @return array - array with absolute paths
	 */
	public static function getContent(string $path, array $filterExtensions = [], array $exclude = []): array
	{
		return self::scan($path, false, true, true, $exclude, $filterExtensions);
	}
	
	/**
	 * Get recursive files with absolute paths from folder
	 *
	 * @param string $path
	 * @param array  $filterExtensions get files only with these extensions
	 * @param array  $exclude
	 * @throws \Exception
	 * @return array
	 */
	public static function getFilesRecursive(string $path, array $filterExtensions = [], array $exclude = []): array
	{
		return self::scan($path, true, false, true, $exclude, $filterExtensions);
	}
	
	/**
	 * Get recursive file names from folder
	 *
	 * @param string $path
	 * @param array  $filterExtensions get files only with these extensions
	 * @param array  $exclude
	 * @throws \Exception
	 * @return array
	 */
	public static function getFileNamesRecursive(string $path, array $filterExtensions = [], array $exclude = []): array
	{
		return self::scan($path, true, false, false, $exclude, $filterExtensions);
	}
	
	/**
	 * Get recursive files and sub folders inside path
	 *
	 * @param string $path
	 * @param array  $filterExtensions
	 * @param array  $exclude
	 * @throws \Exception
	 * @return array - array with absolute paths
	 */
	public static function getContentRecursive(string $path, array $filterExtensions = [], array $exclude = []): array
	{
		return self::scan($path, true, true, true, $exclude, $filterExtensions);
	}
	
	/**
	 * Add DIRECTORY_SEPARATOR to end of folder path
	 *
	 * @param string $path
	 * @return array|string|string[]
	 */
	public static function fixPath(string $path)
	{
		if (empty($path) or is_file($path))
		{
			return $path;
		}
		$path = str_replace("/", DIRECTORY_SEPARATOR, $path);
		$len  = strlen($path) - 1;
		if ($path[$len] != DIRECTORY_SEPARATOR and !is_file($path))
		{
			$path .= DIRECTORY_SEPARATOR;
		}
		
		return $path;
	}
}

?>