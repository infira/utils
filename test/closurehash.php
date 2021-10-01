<?php


use Infira\Utils\ClosureHash;

require_once 'config.php';
require_once '../src/Variable.php';
require_once '../src/Is.php';
require_once '../src/Gen.php';
require_once '../src/ClosureHash.php';

$testArg   = 'hello world';
$argument2 = 'hello to me';
debug(ClosureHash::from(function ()
{
	return "test 1";
}));
debug(ClosureHash::from(function ($arg1)
{
	return "test 2";
}));
debug(ClosureHash::from(function ($arg1 = null) use ($testArg, $argument2)
{
	return "test 3";
}));