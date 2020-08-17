<?php

namespace Infira\Utils;

use ErrorException;

class Error extends ErrorException
{
	public function __construct($message, $data)
	{
		if ($data)
		{
			$message .= '<br>';
			$message .= 'Trace:<pre style="padding:0;margin: 0">' . dump($data) . '</pre>';
		}
		//$message .= '<br> Trace:' . pre(getTrace(1));
		parent::__construct($message);
	}
}