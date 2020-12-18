<?php

namespace Infira\Utils;
/**
 * A class store global re usable class objects
 */
final class ClassFarm
{
	public static $farm = [];
	
	/**
	 * Does farmer exists
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function exists(string $name): bool
	{
		return array_key_exists($name, self::$farm);
	}
	
	/**
	 * Delete farmer
	 *
	 * @param string $name
	 * @return void
	 */
	public static function remove(string $name): void
	{
		unset(self::$farm[$name]);
	}
	
	/**
	 * Is farmer class constructed
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function isConstructed(string $name): bool
	{
		if (!self::exists($name))
		{
			return false;
		}
		
		return self::$farm[$name]->constructed;
	}
	
	/**
	 * get constructed farmer class object
	 *
	 * @param string      $name
	 * @param object|null $scope
	 * @throws Error
	 * @return null|object
	 */
	public static function get(string $name, object $scope = null)
	{
		if (self::exists($name))
		{
			if (self::isConstructed($name))
			{
				return self::$farm[$name]->classObject;
			}
			else
			{
				$S           = self::$farm[$name];
				$constructor = $S->constructor;
				if (is_string($constructor))
				{
					$c = new $constructor();
				}
				elseif (is_object($constructor))
				{
					if ($constructor instanceof \Closure && $scope)
					{
						if (method_exists($constructor, "call"))
						{
							$c = $constructor->call($scope);
						}
						else
						{
							$c = call_user_func($constructor->bindTo($scope));
						}
						
					}
					else
					{
						$c = $constructor();
					}
				}
				else
				{
					throw new Error('Un implemented constructor type (' . gettype($constructor) . ')');
				}
				self::$farm[$name]->constructed = true;
				self::$farm[$name]->classObject = $c;
				
				return $c;
			}
		}
		
		return null;
	}
	
	/**
	 * Adds a new farmer and return constructed farmer class
	 *
	 * @param string          $name
	 * @param callable|string $constructor
	 * @throws Error
	 * @return object|null
	 */
	public static function instance(string $name, $constructor)
	{
		self::add($name, $constructor);
		
		return self::get($name);
	}
	
	/**
	 * @param string          $name
	 * @param string|callable $constructor
	 */
	public static function add(string $name, $constructor)
	{
		if (!self::exists($name))
		{
			$path                  = preg_split('/->|>/', $name);
			$instance              = new \stdClass();
			$instance->constructed = false;
			$instance->constructor = $constructor;
			$instance->namespace   = end($path);
			self::$farm[$name]     = $instance;
		}
	}
	
	/**
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	public function __construct() { }
	
	/**
	 * Private clone method to prevent cloning of the instance of the
	 * *Singleton* instance.
	 *
	 * @return void
	 */
	public function __clone() { }
	
	/**
	 * Private unserialize method to prevent unserializing of the *Singleton*
	 * instance.
	 *
	 * @return void
	 */
	public function __wakeup() { }
}

?>