<?php

namespace Infira\Utils;

use Infira\Utils\ClassFarm;

/**
 * Using magic magic overload to populate class property with usable object of classes
 *
 * @see     test/magicClass.php
 * @package Infira\Utils
 */
class MagicClass
{
	private $chainParent = false;
	
	protected final function setChainName($classMemberName = "")
	{
		if (!empty($classMemberName))
		{
			$this->chainParent = $classMemberName;
		}
	}
	
	/**
	 * Register a array of classes [$propertyName => string|callable $constructor]
	 *
	 * @param array $members
	 * @throws Error
	 */
	protected final function registerProperties(array $members)
	{
		foreach ($members as $ns => $constructor)
		{
			$this->registerPropertyClass($ns, $constructor);
		}
	}
	
	/**
	 * Register a class for property
	 *
	 * @param string          $property
	 * @param string|callable $constructor
	 * @throws Error
	 */
	protected final function registerPropertyClass(string $property, $constructor)
	{
		if (property_exists($this, $property))
		{
			throw new Error('Cant use magicClass cause property already exists');
		}
		
		return ClassFarm::add($property, $constructor);
	}
	
	/**
	 * Called on a missing property
	 *
	 * @see https://www.php.net/manual/en/language.oop5.magic.php
	 * @param string $name
	 * @return mixed
	 * @throws Error
	 */
	public final function __get(string $name)
	{
		$nameSpace = $name;
		$r         = false;
		if ($this->chainParent != false)
		{
			$parentName = $this->chainParent . "->" . $name;
			$r          = ClassFarm::get($parentName, $this);
		}
		if (!$r)
		{
			$r = ClassFarm::get($nameSpace, $this);
		}
		
		if ($r)
		{
			$this->$name = $r; //to avoid next __get call
			
			return $r;
		}
		elseif (method_exists($this, "__getter"))
		{
			
			return $this->__getter($name);
		}
		else
		{
			throw new Error('ClassFarm->__get : You are tring to get variable <B>"' . $name . '</B>" but it doesn\'t exits in ' . get_class($this) . ' class', ['classNamespace' => $nameSpace]);
		}
	}
	
	/**
	 * Set a missing property
	 *
	 * @param string $name
	 * @param mixed  $value
	 * @throws \ErrorException
	 */
	public final function __set(string $name, $value)
	{
		if (ClassFarm::exists($name) or ClassFarm::exists($this->chainParent . "->" . $name))
		{
			$this->$name = $value;
		}
		elseif (method_exists($this, "__setter"))
		{
			$this->__setter($name, $value);
		}
		else
		{
			throw new Error('Genera->__set : You are tring to set variable <B>"' . $name . '</B>" but it doesn\'t exits in ' . get_class($this) . ' class');
		}
	}
	
	/**
	 * Call a magic method
	 *
	 * @param string $name
	 * @param array  $arguments
	 * @throws \ErrorException
	 * @return mixed
	 */
	public final function __call(string $name, array $arguments)
	{
		if (method_exists($this, "__caller"))
		{
			return $this->__caller($name, $arguments);
		}
		else
		{
			throw new Error('ClassFarm->__call : You are tring to call method <B>"' . $name . '</B>" but it doesn\'t exits in ' . get_class($this) . ' class');
		}
	}
}

?>