<?php

namespace Infira\Utils;
/**
 * TempController
 * @method static string getName() Get collection name
 * @method static bool exists(string $key) Does key exists
 * @method static void set(string $key, mixed $value) Set new item
 * @method static void add(mixed $value) Append new view
 * @method static void append(mixed $value) Append new view
 * @method static mixed get(string $key, mixed $returnOnNotFound = null) Get item, if not found $returnOnNotFound will be returned
 * @method static bool delete(string $key) delete item
 * @method static array getItems() get all values
 * @method static array getTree() get all items and sub collections
 * @method static void each(callable $callback) Call $callback for every item in current collection<br /> $callback($itemValue, $itemName)
 * @method static void eachTree(callable $callback) Call $callback for every collection, sub collection and every item<br />$callback($itemValue, $itemName, $collecitonName)
 * @method static void eachCollection(callable $callback) Call $callback for every collection<br />$callback($Colleciton, $collectionName)
 * @method static array getCollections() get all sub collections
 * @method static mixed once(string|array $key, callable $callback) delete item
 * @method static mixed onceForce(string|array $key, bool $forceset, callable $callback) delete item
 */
class RuntimeMemory
{
	private static $collections = [];
	
	/**
	 * @param string $key - collection name
	 * @return \Infira\Utils\RuntimeMemoryCollection
	 */
	public static function Collection(string $key): \Infira\Utils\RuntimeMemoryCollection
	{
		if (!isset(self::$collections[$key]))
		{
			self::$collections[$key] = new \Infira\Utils\RuntimeMemoryCollection($key);
		}
		
		return self::$collections[$key];
	}
	
	public static function __callStatic(string $method, array $args)
	{
		return self::Collection('general')->$method(...$args);
	}
}

final class RuntimeMemoryCollection
{
	private $collections = [];
	
	private $data = [];
	
	private $name;
	
	public function __construct(string $mainName)
	{
		$this->name = $mainName;
	}
	
	/**
	 * @param string $name - collection name
	 * @return \Infira\Utils\RuntimeMemoryCollection
	 */
	public function Collection(string $name): \Infira\Utils\RuntimeMemoryCollection
	{
		if (!isset($this->collections[$name]))
		{
			$this->collections[$name] = new \Infira\Utils\RuntimeMemoryCollection($name);
		}
		
		return $this->collections[$name];
	}
	
	/**
	 * Returns collection name
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
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
	 * Set new item
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
	 * Add new new item
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
	 * Get item, if not found $returnOnNotFound will be returned
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
	 * get all items
	 *
	 * @return array
	 */
	public function getItems(): array
	{
		return $this->data;
	}
	
	/**
	 * Get current collection tree
	 * array(
	 *  array
	 *  (
	 *      [collections] => array
	 *      (
	 *          [collection1] => array
	 *          (
	 *              [collections] => array(...)
	 *              [items] => array(...)
	 *          )
	 *      )
	 *      [items] => array
	 *      (
	 *          "item1"=>"item value",
	 *          "item2"=>"item2 value"
	 *      )
	 *      .....
	 *  )
	 */
	public function getTree(): array
	{
		$data = ['collections' => []];
		$this->eachCollection(function ($Collection, $collectionName) use (&$data)
		{
			$data['collections'][$collectionName] = ['collections' => [], 'items' => $Collection->getItems()];
			$tree                                 = $Collection->getTree();
			if ($tree['collections'])
			{
				$data['collections'][$collectionName]['collections'] = $tree['collections'];
			}
		});
		$data['items'] = $this->getItems();
		
		return $data;
	}
	
	/**
	 * Call $callback for every item in current collection<br />$callback($itemValue,$itemName)
	 *
	 * @param callable $callback
	 * @return void
	 */
	public function each(callable $callback): void
	{
		foreach ($this->data as $key => $value)
		{
			call_user_func_array($callback, [$value, $key]);
		}
	}
	
	/**
	 * Call $callback for every collection, sub collection and every item<br />$callback($itemValue,$itemName,$collecitonName)
	 *
	 * @param callable $callback
	 * @return void
	 */
	public function eachTree(callable $callback): void
	{
		foreach ($this->data as $name => $value)
		{
			call_user_func_array($callback, [$value, $name, $this->name]);
		}
		foreach ($this->collections as $name => $Collection)
		{
			$Collection->eachTree($callback);
		}
	}
	
	/**
	 * Call $callback for every collection<br />$callback($Colleciton,$collectionName)
	 *
	 * @param callable $callback
	 * @return void
	 */
	public function eachCollection(callable $callback): void
	{
		foreach ($this->collections as $name => $Collection)
		{
			call_user_func_array($callback, [$Collection, $name]);
		}
	}
	
	/**
	 * Get all current collections
	 *
	 * @return array
	 */
	public function getCollections(): array
	{
		return $this->collections;
	}
	
	/**
	 * Call $callback once per $key existence
	 * All arguments after  $callback will be passed to callable method
	 *
	 * @param string|array|int $key
	 * @param callable         $callback      method result will be setted to memory for later use
	 * @param mixed            $callbackArg1  -
	 * @param mixed            $callbackArg2  -
	 * @param mixed            $callbackArg3  -
	 * @param mixed            $callbackArg_n -
	 * @return mixed - $callback result
	 */
	public function once($key, callable $callback)
	{
		$CID = Gen::cacheID($key);
		if (!$this->exists($CID))
		{
			$this->set($CID, call_user_func_array($callback, array_slice(func_get_args(), 2)));
		}
		
		return $this->get($CID);
	}
	
	/**
	 * Call $callback once per $key existence or force it to call
	 * All arguments after  $forceSet will be passed to callable method
	 *
	 * @param string|array|int $key
	 * @param callable         $callback
	 * @param bool             $forceSet      - if its true then $callback will be called regardless of is the $key setted or not
	 * @param mixed            $callbackArg1  -
	 * @param mixed            $callbackArg2  -
	 * @param mixed            $callbackArg3  -
	 * @param mixed            $callbackArg_n -
	 * @return mixed|null - $callback result
	 */
	public function onceForce($key, callable $callback, bool $forceSet = false)
	{
		$CID = Gen::cacheID($key);
		if (!$this->exists($CID) or $forceSet == true)
		{
			$this->set($CID, call_user_func_array($callback, array_slice(func_get_args(), 3)));
		}
		
		return $this->get($CID);
	}
}

?>