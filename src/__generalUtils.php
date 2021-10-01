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

