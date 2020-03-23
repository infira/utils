<?php

namespace Infira\Utils;

use Infira\Utils\Variable as Variable;
use Infira\Utils\URLBuilder as URLBuilder;

/**
 * Class to handle $_GET, $_POST, _REQUEST, $_FILES server variable
 */
class Http
{
	private static $mainVar = "route";
	
	/**
	 * Http constructor.
	 *
	 * @param string   $mainVar         - var name in $_GET what defines the page, defaults to route
	 * @param callable $getParamsParser - add your own _GET params parser
	 */
	public static final function init(string $mainVar = "route", $getParamsParser = FALSE)
	{
		self::$mainVar = $mainVar;
		if (is_callable($getParamsParser))
		{
			$_GET = $getParamsParser($_GET);
		}
	}
	
	/**
	 * Returns a $_POST OR $_GET variable value
	 *
	 * @param string $name    in case of NULL returns all $_GET||$_POST
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function get($name = NULL, $default = NULL)
	{
		if (self::existsPOST($name))
		{
			return self::getVar($name, "post", $default);
		}
		if (self::existsGET($name))
		{
			return self::getVar($name, "get", $default);
		}
		
		return $default;
	}
	
	/**
	 * Returns a $_GET variable value
	 *
	 * @param string $name    in case of NULL returns all $_GET
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function getGET($name = NULL, $default = NULL)
	{
		return self::getVar($name, "get", $default);
	}
	
	/**
	 * Returns a $_POST variable value
	 *
	 * @param string $name    in case of NULL returns all $_GET
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function getPOST($name = NULL, $default = NULL)
	{
		return self::getVar($name, "post", $default);
	}
	
	/**
	 * Returns a $_FILES variable value
	 *
	 * @param string $name    in case of NULL returns all $_GET
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function getFILE($name = NULL)
	{
		return self::getVar($name, 'files');
	}
	
	/**
	 * Returns is the request type post
	 */
	public static function isPOST()
	{
		if (isset($_SERVER["REQUEST_METHOD"]))
		{
			if (self::isRequestMethod("post"))
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Checks is variable exists either $_POST OR $_GET
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function exists($name)
	{
		return (self::existsPOST($name) OR self::existsGET($name));
	}
	
	/**
	 * Checks is variable exists in $_POST
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function existsPOST($name)
	{
		if (!isset($_POST))
		{
			return FALSE;
		}
		
		return array_key_exists($name, $_POST);
	}
	
	/**
	 * Checks is variable exists in $_GET
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function existsGET($name)
	{
		if (!isset($_GET))
		{
			return FALSE;
		}
		
		return array_key_exists($name, $_GET);
	}
	
	/**
	 * Set a new $_GET var and value
	 *
	 * @param       $name
	 * @param mixed $value
	 * @return bool
	 */
	public static function setGET($name, $value = NULL)
	{
		return self::setVar($name, "get", $value);
	}
	
	/**
	 * Set var and var value to $_POST
	 *
	 * @param bool  $name
	 * @param mixed $value
	 * @return bool
	 */
	public static function setPOST($name = FALSE, $value = NULL)
	{
		return self::setVar($name, "post", $value);
	}
	
	/**
	 * Delete var from $_GET
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function deleteGET($name)
	{
		return self::delete($name, "get");
	}
	
	/**
	 * Delete var from $_POST
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function deletePOST($name)
	{
		return self::delete($name, "post");
	}
	
	/**
	 * Flush $_POST values
	 *
	 * @return bool
	 */
	public static function flushPOST()
	{
		return self::doFlush("post");
	}
	
	/**
	 * Flush $_FILES values
	 *
	 * @return bool
	 */
	public static function flushFILES()
	{
		return self::doFlush("files");
	}
	
	/**
	 * Flush $_GET values
	 *
	 * @return bool
	 */
	public static function flushGET()
	{
		return self::doFlush("get");
	}
	
	/**
	 * Get request method $_SERVER["REQUEST_METHOD"]
	 *
	 * @return string IN LOWERCASE
	 */
	public static function getRequestMethod()
	{
		return strtolower($_SERVER["REQUEST_METHOD"]);
	}
	
	/**
	 * Check is $_SERVER["REQUEST_METHOD"]
	 *
	 * @param string $method
	 * @return bool
	 */
	public static function isRequestMethod(string $method)
	{
		return in_array(strtolower($_SERVER["REQUEST_METHOD"]), Variable::toArray($method));
	}
	
	/**
	 * Returns is the current request is ajax
	 */
	public static function isAjax()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Got to link
	 *
	 * @param string $link        - where to go
	 * @param bool   $redirect301 - use header 301 to redirect
	 */
	public static function go(string $link = "", bool $redirect301 = FALSE)
	{
		if ($link == '')
		{
			$link = URLBuilder::get([self::$mainVar => 1]);
		}
		elseif (checkArray($link))
		{
			$link = URLBuilder::get($link, FALSE);
		}
		elseif (Is::number($link))
		{
			$link = URLBuilder::get([self::$mainVar => $link]);
		}
		$link = str_replace('&amp;', '&', $link);
		$llen = strlen($link) - 1;
		if ($link{$llen} == "/")
		{
			$link = substr($link, 0, $llen);
		}
		if (!headers_sent())
		{
			if ($redirect301 == TRUE)
			{
				Header("HTTP/1.1 301 Moved Permanently", TRUE, 301);
			}
			header('Location: ' . $link);
		}
		else
		{
			echo "<script type=\"text/javascript\">document.location.href='" . $link . "'</script>";
		}
		exit();
	}
	
	/**
	 * Redirect page using 301 header
	 *
	 * @param string $link - where to go
	 */
	public static function go301(string $link)
	{
		self::go($link, 0, TRUE);
	}
	
	/**
	 * Redirect to referer url
	 *
	 * @param string|array $extra - add extra params to link
	 */
	public static function goToReferer($extra = FALSE)
	{
		$link = self::getReferer() . URLBuilder::buildParams($extra);
		self::go($link);
	}
	
	/**
	 * Get referer url
	 *
	 * @return string
	 */
	public static function getReferer(): string
	{
		return (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : FALSE;
	}
	
	/**
	 * Get Current request url
	 *
	 * @return string
	 */
	public static function getCurrentUrl(): string
	{
		$protocol = FALSE;
		if (!$protocol)
		{
			$SiteUrl = 'http';
			if (isset($_SERVER['HTTPS']))
			{
				$isHttps = strtolower($_SERVER['HTTPS']);
				if ($isHttps == 'on')
				{
					$SiteUrl .= 's';
				}
			}
			$SiteUrl .= '://';
		}
		else
		{
			$SiteUrl = $protocol;
		}
		$SiteUrl .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		return $SiteUrl;
	}
	
	/**
	 * Get current domain
	 *
	 * @return String
	 */
	public static function getDomain()
	{
		return $_SERVER["HTTP_HOST"];
	}
	
	//############################################################################################################# SOF Helpers
	private static function getServerData($from)
	{
		if ($from == "get")
		{
			return $_GET;
		}
		elseif ($from == "post")
		{
			return $_POST;
		}
		elseif ($from == "file")
		{
			return $_FILES;
		}
		elseif ($from == "request")
		{
			return $_REQUEST;
		}
		throw new \Error("undefeind server data = " . $from);
	}
	
	/**
	 * Delete var from HTTP variable ($_GET,$_POST, $_REQUEST, $_FILES)
	 *
	 * @param string $name
	 * @param string $from (get,post,request,file)
	 */
	private static function delete($name, $from)
	{
		$globVarName = self::getServerData($from);
		if (isset($globVarName[$name]))
		{
			unset($globVarName[$name]);
		}
	}
	
	/**
	 * Gets a http value
	 *
	 * @param string $name
	 * @param string $from    (get,post,request)
	 * @param mixed  $default - default value on not found
	 * @return variable on success, return false on unsuccess
	 */
	private static function getVar($name = NULL, $from, $default = NULL)
	{
		$value = "__UND_DEF__";
		$data  = self::getServerData($from);
		if ($name === NULL)
		{
			$value = $data;
		}
		else
		{
			if (isset($data[$name]))
			{
				$value = $data[$name];
			}
		}
		if ($value === "__UND_DEF__")
		{
			$value = $default;
		}
		
		return $value;
	}
	
	/**
	 * Set var and var value to HTTP variable
	 *
	 * @param $name
	 * @param $value
	 * @param $to - (get,post,files,request)
	 * @return bool
	 */
	private static function setVar($name, $to, $value = NULL)
	{
		if ($name AND $value === NULL)
		{
			if ($to == "get")
			{
				$_GET = $name;
			}
			elseif ($to == "post")
			{
				$_POST = $name;
			}
			elseif ($to == "file")
			{
				$_FILES = $name;
			}
			elseif ($to == "request")
			{
				$_REQUEST = $name;
			}
		}
		else
		{
			if ($to == "get")
			{
				$_GET[$name] = $value;
			}
			elseif ($to == "post")
			{
				$_POST[$name] = $value;
			}
			elseif ($to == "file")
			{
				$_FILES[$name] = $value;
			}
			elseif ($to == "request")
			{
				$_REQUEST[$name] = $value;
			}
		}
	}
	
	private static function doFlush(string $from)
	{
		self::setVar(FALSE, $from, NULL);
	}
}

?>