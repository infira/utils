<?php


require_once 'config.php';
require_once '../src/ClassFarm.php';
require_once '../src/Facade.php';

class TestFacadeInstance
{
	public function __construct()
	{
		echo 'TestFacadeInstance constructed<br />';
	}
	
	public function sayHello()
	{
		echo "Hello world!";
	}
}

/**
 * Class TestFacade
 * @method void sayHello
 */
class TestFacade extends \Infira\Utils\Facade
{
	static protected function getInstanceConfig(): array
	{
		return ['name' => 'TestFacadeInstance', 'constructor' => function ()
		{
			return new TestFacadeInstance();
		}];
	}
}


TestFacade::sayHello();

