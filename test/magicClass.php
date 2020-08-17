<?php


require_once 'config.php';
require_once '../src/MagicClass.php';
require_once '../src/ClassFarm.php';

class EmailUtil
{
	public function send()
	{
		echo get_class($this) . " did stuff<br />";
	}
}

class SmsUtil
{
	public function __construct(string $welcome)
	{
		echo "Sms class constructed and says:" . $welcome . "<br />";
	}
	
	public function send()
	{
		echo get_class($this) . " did stuff<br />";
	}
}

/**
 * Class myTestClass
 *
 * @property EmailUtil $Email;
 * @property SmsUtil   $Sms  ;
 */
class UtilsGeneral extends \Infira\Utils\MagicClass
{
	public function __construct()
	{
		parent::setChainName("Utils");
		$subClasses          = [];
		$subClasses["Email"] = "EmailUtil";
		$subClasses["Sms"]   = function ()
		{
			return new SmsUtil("hellow");
		};
		parent::registerProperties($subClasses);
	}
}

class RandomSubClass
{
	public function doStuff()
	{
		echo "just did stuff";
	}
}

/**
 * Class ApplicationController
 *
 * @property UtilsGeneral   $Utils
 * @property RandomSubClass $Random
 */
class MyApp extends \Infira\Utils\MagicClass
{
	public function __construct()
	{
		parent::registerPropertyClass("Utils", "UtilsGeneral");
		parent::registerPropertyClass("Random", "RandomSubClass");
	}
}

$App = new MyApp();
$App->Utils->Email->send();
$App->Utils->Sms->send();
$App->Random->doStuff();

