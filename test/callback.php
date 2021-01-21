<?php


require_once 'config.php';
require_once '../src/MagicClass.php';
require_once '../src/ClassFarm.php';

function test($argument = null)
{
	debug("simple test function", ["arguments" => $argument]);
}

callback('test', null, [1]);
callback(function ($argument = null)
{
	debug("closure function ", ["arguments" => $argument]);
}, null, [1]);


class foo
{
	function bar($arg, $arg2)
	{
		echo __METHOD__, " got $arg and $arg2\n";
	}
}


// Call the foobar() function with 2 arguments
$foo = new foo;
//call_user_func_array([$foo, "bar"], ["three", "four"]);
callback([$foo, "bar"], null, ["three", "four"]);