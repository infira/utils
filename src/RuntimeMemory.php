<?php

namespace Infira\Utils;
/**
 * TempController
 * @method static bool exists(string $key) Does key exists
 * @method static void set(string $key, mixed $value) Set new value
 * @method static void add(mixed $value) Append new view
 * @method static void append(mixed $value) Append new view
 * @method static mixed get(string $key, mixed $returnOnNotFound = null) get value
 * @method static array getAll() get all values
 * @method static void delete(string $key) delete item
 * @method static mixed once(string|array $key, callable $callback) delete item
 * @method static mixed onceForce(string|array $key, bool $forceset, callable $callback) delete item
 */
class RuntimeMemory
{
	private static $collections = [];
	
	/**
	 * @param string $key - collection name
	 * @return __RuntimeMemoryData
	 */
	public static function Collection(string $key): __RuntimeMemoryData
	{
		if (!isset(self::$collections[$key]))
		{
			self::$collections[$key] = new __RuntimeMemoryData();
		}
		
		return self::$collections[$key];
	}
	
	public static function __callStatic(string $method, array $args)
	{
		return self::Collection("genedral")->$method(...$args);
	}
}

final class __RuntimeMemoryData
{
	private static $collections = [];
	
	private $data = [];
	
	/**
	 * @param string $key - collection name
	 * @return __RuntimeMemoryData
	 */
	public static function Collection(string $key): __RuntimeMemoryData
	{
		if (!isset(self::$collections[$key]))
		{
			self::$collections[$key] = new __RuntimeMemoryData();
		}
		
		return self::$collections[$key];
	}
	
	/**
	 * Checks if the item exists by key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function exists(string $key): bool
	{
		return array_key_exists($key, $this->data);
	}
	
	/**
	 * Set by key
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return void
	 */
	public function set(string $key, $value): void
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * Add new new value
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function add($value): void
	{
		$this->data[] = $value;
	}
	
	/**
	 * Alias to $this->add
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function append($value): void
	{
		$this->data[] = $value;
	}
	
	/**
	 * Get item by key
	 *
	 * @param string $key
	 * @param mixed  $returnOnNotFound - if not found then that is returned
	 * @return mixed/bool
	 */
	public function get(string $key, $returnOnNotFound = null)
	{
		if (!$this->exists($key))
		{
			return $returnOnNotFound;
		}
		
		return $this->data[$key];
	}
	
	/**
	 * get all items
	 *
	 * @return array
	 */
	public function getAll(): array
	{
		return $this->data;
	}
	
	/**
	 * delete bye key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete(string $key): bool
	{
		if ($this->exists($key))
		{
			unset($this->data[$key]);
		}
		
		return true;
	}
	
	/**
	 * Get all current collections
	 *
	 * @return array
	 */
	public static function getCollections(): array
	{
		return self::$collections;
	}
	
	private static function multiImplode($glue, $array)
	{
		$ret = '';
		
		foreach ($array as $item)
		{
			if (is_array($item))
			{
				$ret .= self::multiImplode($glue, $item) . $glue;
			}
			else
			{
				$ret .= $item . $glue;
			}
		}
		
		$ret = substr($ret, 0, 0 - strlen($glue));
		
		return $ret;
	}
	
	/**
	 * Call $callback once per cache existance, result will be seted to cache
	 * all argumetns expect last is used to generate cacheID
	 * last argument must be callable $callback
	 *
	 * @return string - cacheID
	 */
	public function once()
	{
		$args = func_get_args();
		if (count($args) < 2)
		{
			throw new \Error("at least 2 arguments ms be defined");
		}
		$callback = end($args);
		if (!is_callable($callback))
		{
			throw new \Error("Last argument must be callable");
		}
		$params = array_slice($args, 0, -1);
		$CID    = hash("crc32b", self::multiImplode(";", $params));
		if (!$this->exists($CID))
		{
			$this->set($CID, call_user_func_array($callback, $params));
		}
		
		return $this->get($CID);
	}
	
	/**
	 * Call $callback once per cache existance, result will be seted to cache
	 * all argumetns expect 2 last is used to generate cacheID
	 * last argument must be callable $callback
	 * 1 before last is bool $forceSet to force call $callback either exists or not in the cache
	 *
	 * @return string - cacheID
	 */
	public function onceForce()
	{
		$args  = func_get_args();
		$count = count($args);
		if ($count < 3)
		{
			throw new \Error("at least 3 arguments ms be defined");
		}
		$i        = $count - 1;
		$callback = $args[$i];
		if (!is_callable($callback))
		{
			throw new \Error("Last argument must be callable");
		}
		$i--;
		$forceSet = $args[$i];
		if (!is_bool($forceSet))
		{
			throw new \Error('Argument before last($forceSet) must be boolean');
		}
		$params = array_slice($args, 0, -1);
		$CID    = hash("crc32b", self::multiImplode(";", $params));
		if (!$this->exists($CID))
		{
			$this->set($CID, call_user_func_array($callback, $params));
		}
		
		return $this->get($CID);
	}
}

?>