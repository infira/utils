<?php


use Infira\Utils\RuntimeMemory as Rm;

require_once 'config.php';
require_once '../src/Gen.php';
require_once '../src/ClosureHash.php';
require_once '../src/RuntimeMemory.php';

$testArg   = 'hello world';
$argument2 = 'hello to me';

$db = function ()
{
	echo "magic is done" . '<br>';
};

$test       = new stdClass();
$test->test = "tere";

$c = function ($arg1 = null) use ($testArg, $db)
{
	$db();
	
	return "test 3";
};
echo Rm::magic($c) . '<br>';
echo Rm::magic($c) . '<br>';
