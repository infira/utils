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
		return self::getInstance(static::getInstanceConfig())->$method(...$args);
	}
	
	protected static function getInstance(array $instanceConfig)
	{
		foreach ($instanceConfig as $property => $constructor)
		{
			return ClassFarm::instance($property, $constructor);
		}
	}
	
	static abstract protected function getInstanceConfig(): array;
}