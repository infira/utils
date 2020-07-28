<?php
/**
 * Parse str, converts name=value to array
 *
 * @param string $string
 * @return array
 */
function parseStr(string $string): array
{
	$data   = [];
	$string = trim($string);
	
	$string = str_replace("&amp;", "&", $string);
	$string = str_replace("&", "&", $string);
	$string = str_replace("\n", "&", $string);
	
	parse_str($string, $data);
	if (get_magic_quotes_gpc() == 1)
	{
		$data = stripslashes($data);
	}
	if (!is_array($data))
	{
		$data = [];
	}
	
	return $data;
}

function isAjaxRequest()
{
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
	{
		return true;
	}
	
	return false;
}


//https://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php
function getUserIP()
{
	if (isset($_SERVER['HTTP_CLIENT_IP']))
	{
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	elseif (isset($_SERVER['HTTP_X_FORWARDED']))
	{
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	}
	elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
	{
		$ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	}
	elseif (isset($_SERVER['HTTP_FORWARDED_FOR']))
	{
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	}
	elseif (isset($_SERVER['HTTP_FORWARDED']))
	{
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	}
	elseif (isset($_SERVER['REMOTE_ADDR']))
	{
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	}
	else
	{
		$ipaddress = 'UNKNOWN';
	}
	
	return $ipaddress;
}

function explodeJoin($explodeDelimiter, $str, $joinDelimiter, $joinKeys = false)
{
	$ex       = explode($explodeDelimiter, $str);
	$joinKeys = \Infira\Utils\Variable::toArray($joinKeys);
	$data     = [];
	foreach ($ex as $key => $val)
	{
		$val = trim($val);
		if ($joinKeys)
		{
			if ($val && in_array($key, $joinKeys))
			{
				$data[] = $val;
			}
		}
		else
		{
			if ($val)
			{
				$data[] = $val;
			}
		}
	}
	
	return join($joinDelimiter, $data);
}

function defaultValue($value, $defaultValue, $defaultOn = false)
{
	if ($value === $defaultOn)
	{
		return $defaultValue;
	}
	
	return $value;
}

if (!defined("EACH_ARRAY_DELETE"))
{
	define("EACH_ARRAY_DELETE", "__EACH_ARRAY_DELETE__");
}
function eachArray($array, $function)
{
	if (checkArray($array))
	{
		foreach ($array as $key => $item)
		{
			$newVal = $function($key, $item);
			if ($newVal === EACH_ARRAY_DELETE)
			{
				unset($array[$key]);
			}
			else
			{
				$array[$key] = $newVal;
			}
		}
		
		return $array;
	}
	
	return $array;
}

function is_closure($t)
{
	return is_object($t) && ($t instanceof Closure);
}

function callback($callback, $scope = false, $params = [])
{
	if (is_string($callback))
	{
		$callStatic = false;
		if (strpos($callback, '::') !== false)
		{
			$ex         = explode('::', $callback);
			$callStatic = true;
			$scope      = trim($ex[0]);
			$callback   = trim($ex[1]);
		}
		if ($scope)
		{
			if ($callStatic)
			{
				return $scope::$callback(...$params);
			}
			else
			{
				return $scope->$callback(...$params);
			}
		}
		else
		{
			return call_user_func_array($callback, $params);
		}
	}
	elseif (is_closure($callback))
	{
		if ($scope)
		{
			if (is_string($scope))
			{
				$scope = new $scope();
			}
			$callback = $callback->bindTo($scope);
		}
		
		return call_user_func_array($callback, $params);
	}
	else
	{
		addExtraErrorInfo("callback", gettype($callback));
		alert("unimplemented callback paramaterer");
	}
}

function checkArray($array)
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

$GLOBALS["__globalFlags__"] = false;
function flag($name = null, $value = null)
{
	if ($name !== null and $value === null)
	{
		if (!isset($GLOBALS["__globalFlags__"][$name]))
		{
			return false;
		}
	}
	else
	{
		$GLOBALS["__globalFlags__"][$name] = $value;
	}
	
	return $GLOBALS["__globalFlags__"][$name];
}

function pre($var)
{
	return "<pre>$var</pre>";
}

/**
 * Debug function is to debug
 *
 * @param mixed        $variable - can be all kind of type
 * @param unknown_type $trace
 */
$GLOBALS["debugIsActive"] = false;
function debug()
{
	$GLOBALS["debugIsActive"] = true;
	$args                     = func_get_args();
	$html                     = "";
	if (isAjaxRequest())
	{
		$html = "[DEBUG_MSG].\n";
	}
	
	if (count($args) == 1)
	{
		$html .= dump($args[0]);
	}
	else
	{
		$html .= dump($args);
	}
	$html = pre($html);
	
	if (isset($_GET["isViewSource"]))
	{
		$html = str_replace(["<br />", "<br>"], "\n", $html);
	}
	if (isset($_GET["traceDebug"]))
	{
		cleanOutput();
		echo($html);
		echo getTrace();
		exit;
	}
	echo($html);
	if (isAjaxRequest())
	{
		exit();
	}
	
	return true;
	
}

function isDebugActive()
{
	return $GLOBALS["debugIsActive"];
}

$GLOBALS["debugCollection"] = [];
function collect()
{
	$args                         = func_get_args();
	$GLOBALS["debugCollection"][] = (count($args) == 1) ? $args[0] : $args;
}

function debugCollection()
{
	debug($GLOBALS["debugCollection"]);
}

function cleanOutput($isRecursive = false)
{
	if (ob_get_contents())
	{
		ob_clean();
		ob_end_clean();
		if ($isRecursive)
		{
			cleanOutput(true);
		}
	}
}


function debugClean($v1, $v2 = null)
{
	$args = func_get_args();
	debug((count($args) == 1) ? $args[0] : $args);
}


function dump($variable, $echo = false)
{
	
	if (is_array($variable) or is_object($variable))
	{
		$html = print_r($variable, true);
	}
	else
	{
		ob_start();
		var_dump($variable);
		$html = ob_get_clean();
	}
	if ($echo == true)
	{
		exit($html);
	}
	
	return $html;
}

function getTrace($startAt = 0)
{
	$backTrace = debug_backtrace();
	$until     = 15;
	$trace     = "<br />";
	$start     = intval($startAt);
	$nr        = 1;
	for ($i = $start; $i <= $until; $i++)
	{
		if (isset($backTrace[$i]['file']))
		{
			$trace .= $nr . ') File ' . $backTrace[$i]['file'] . ' in line ' . $backTrace[$i]['line'] . '<br>';
			$nr++;
		}
	}
	$trace = str_replace(getcwd(), "", $trace);
	
	return $trace;
}


function trace($title = false)
{
	if (!empty($title))
	{
		echo "----------TRACE : $title --------------" . BR;
	}
	echo getTrace(true);
}

function isMobile()
{
	if (!isset($_SERVER['HTTP_USER_AGENT']))
	{
		return false;
	}
	
	return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
}

function explodeAt($delimiter = ",", $value, $at, $onNotFound = "")
{
	$ex = explode($delimiter, $value);
	if (array_key_exists($at, $ex))
	{
		return $ex[$at];
	}
	
	return $onNotFound;
}

function defineOrNot($name, $value)
{
	if (!defined($name))
	{
		define($name, $value);
	}
}

/**
 * Alias to throw new Error
 *
 * @param string $msg
 * @throws Exception
 */
function error(string $msg)
{
	throw new \Error($msg);
}

/**
 * Alias to throw new exeption
 *
 * @param $msg
 * @throws Exception
 */
function exception($msg)
{
	throw new \Exception($msg);
}

/**
 * @param string $name
 * @return Profiler
 */
function Prof(string $name = null)
{
	if ($name === null)
	{
		global $Profiler;
		
		return $Profiler;
	}
	if (!isset($GLOBALS["infira_profilers"][$name]))
	{
		$GLOBALS["infira_profilers"][$name] = new Profiler();
	}
	
	return $GLOBALS["infira_profilers"][$name];
}