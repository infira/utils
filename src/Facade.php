<?php

namespace Infira\Utils;

/**
 * Its like MagicClass but for static methods
 *
 * @see     test/facade.php
 * @package Infira\Utils
 */
abstract class Facade
{
	public static function __callStatic($method, $args)
	{
		$config = static::getInstanceConfig();
		
		return self::getInstance($config['name'], $config['constructor'])->$method(...$args);
	}
	
	/**
	 * @param string          $name
	 * @param string|callable $constructor - a class name or callable method what returns objects
	 * @return object|null
	 */
	protected static function getInstance(string $name, $constructor): ?object
	{
		return ClassFarm::instance($name, $constructor);
	}
	
	static abstract protected function getInstanceConfig(): array;
	
}