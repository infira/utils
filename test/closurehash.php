<?php


require_once 'config.php';
require_once '../src/Gen.php';
require_once '../src/ClosureHash.php';
require_once '../src/ClassFarm.php';

$testArg = 'hello world';

debug(ClosureHash::from(function ()
{

}));
debug(ClosureHash::from($c2 = function ($arg1)
{

}));
debug(ClosureHash::from($c2 = function ($arg1 = null)
{

}));