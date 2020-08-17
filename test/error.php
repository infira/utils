<?php


require_once 'config.php';
require_once '../src/Error.php';

try
{
	throw new \Infira\Utils\Error("blaah", ['extra' => 'data']);
}
catch (\Infira\Utils\Error $e)
{
	echo $e->getMessage();
}