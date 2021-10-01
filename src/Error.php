<?php

namespace Infira\Utils;

use ErrorException;

class Error extends ErrorException
{
	public function __construct($message, $data = null)
	{
		if ($data)
		{
			$message .= '<br>';
			$message .= 'Trace:<pre style="padding:0;margin: 0">' . Variable::dump($data) . '</pre>';
		}
		parent::__construct($message);
	}
}