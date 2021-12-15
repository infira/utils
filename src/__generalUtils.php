<?php

use Infira\Utils\Variable;

if (!function_exists('debug'))
{
	function debug(...$moreVars)
	{
		if (count($moreVars) > 1)
		{
			$html = Variable::dump($moreVars);
		}
		else
		{
			$html = Variable::dump($moreVars[0]);
		}
		echo "<pre>$html</pre>";
	}
}


if (!function_exists('checkArray'))
{
	function checkArray($array): bool
	{
		if (is_array($array))
		{
			if (count($array) > 0)
			{
				return true;
			}
		}
		
		return false;
	}
}

