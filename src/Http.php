<?php

namespace Infira\Utils;

use Infira\Utils\Variable as Variable;

/**
 * Class to handle $_GET, $_POST, _REQUEST, $_FILES , $_SERVER variables
 */
class Http
{
	const UNDEFINED = '__U-N-D-D-E-F-I-N-E-D__';
	
	/**
	 * Http constructor.
	 *
	 * @param bool $getParamsParser - add your own _GET params parser
	 */
	public static final function init(bool $getParamsParser = false)
	{
		if (is_callable($getParamsParser))
		{
			$_GET = $getParamsParser($_GET);
		}
	}
	
	/**
	 * Returns a $_POST OR $_GET variable value by $name
	 *
	 * @param string $name    - variable name, leave blank to to get all
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function get(string $name = self::UNDEFINED, $default = null)
	{
		if (self::existsPOST($name))
		{
			return self::getVar('post', $name, $default);
		}
		if (self::existsGET($name))
		{
			return self::getVar('get', $name, $default);
		}
		
		return $default;
	}
	
	/**
	 * Does variable exists in  either $_POST OR $_GET
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function exists(string $name): bool
	{
		return (self::existsPOST($name) or self::existsGET($name));
	}
	
	################################################################################ START of $_GET methods
	
	/**
	 * Does variable exists in in $_GET
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function existsGET(string $name): bool
	{
		return array_key_exists($name, $_GET);
	}
	
	/**
	 * Returns a $_GET[$name] value
	 * Leave all parametrs blank to get $_GET
	 *
	 * @param string $name    - variable name, leave blank to to get all
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function getGET(string $name = self::UNDEFINED, $default = null)
	{
		return self::getVar('get', $name, $default);
	}
	
	/**
	 * Set variable value into $_GET
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setGET(string $name, $value)
	{
		self::setVar($name, 'get', $value);
	}
	
	/**
	 * Delete variable from $_GET
	 *
	 * @param string $name
	 */
	public static function deleteGET(string $name)
	{
		self::deleteVar($name, 'get');
	}
	
	/**
	 * Flush $_GET values
	 *
	 * @param array $replace = [] - replace $_GET with
	 */
	public static function flushGET(array $replace = [])
	{
		$_GET = $replace;
	}
	################################################################################ END of $_GET methods
	
	################################################################################ START of $_POST methods
	/**
	 * Does variable exists in in $_POST
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function existsPOST(string $name): bool
	{
		return array_key_exists($name, $_POST);
	}
	
	/**
	 * Returns a $_POST[$name] value
	 * Leave all parametrs blank to get $_POST
	 *
	 * @param string $name    - variable name, leave blank to to get all
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function getPOST(string $name = self::UNDEFINED, $default = null)
	{
		return self::getVar('post', $name, $default);
	}
	
	/**
	 * Set variable value into $_POST
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setPOST(string $name, $value)
	{
		self::setVar($name, 'post', $value);
	}
	
	/**
	 * Delete variable from $_POST
	 *
	 * @param string $name
	 */
	public static function deletePOST(string $name)
	{
		self::deleteVar($name, 'post');
	}
	
	/**
	 * Flush $_POST values
	 *
	 * @param array $replace = [] - replace $_POST with
	 */
	public static function flushPOST(array $replace = [])
	{
		$_POST = $replace;
	}
	################################################################################ END of $_POST methods
	
	################################################################################ START of $_FILES methods
	/**
	 * Does variable exists in in $_FILES
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function existsFILE(string $name): bool
	{
		return array_key_exists($name, $_FILES);
	}
	
	/**
	 * Returns a $_FILES[$name] value
	 * Leave all parametrs blank to get $_FILES
	 *
	 * @param string $name    - variable name, leave blank to to get all
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function getFILE(string $name = self::UNDEFINED, $default = null)
	{
		return self::getVar('files', $name, $default);
	}
	
	/**
	 * Set variable value into $_FILES
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setFILE(string $name, $value)
	{
		self::setVar($name, 'files', $value);
	}
	
	/**
	 * Delete variable from $_FILES
	 *
	 * @param string $name
	 */
	public static function deleteFILE(string $name)
	{
		self::deleteVar($name, 'files');
	}
	
	/**
	 * Flush $_FILES values
	 *
	 * @param array $replace = [] - replace $_FILES with
	 */
	public static function flushFILE(array $replace = [])
	{
		$_FILES = $replace;
	}
	################################################################################ END of $_FILES methods
	
	################################################################################ START of $_REQUEST methods
	/**
	 * Does variable exists in in $_REQUEST
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function existsREQUEST(string $name): bool
	{
		return array_key_exists($name, $_REQUEST);
	}
	
	/**
	 * Returns a $_REQUEST[$name] value
	 * Leave all parametrs blank to get $_REQUEST
	 *
	 * @param string $name    - variable name, leave blank to to get all
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function getREQUEST(string $name = self::UNDEFINED, $default = null)
	{
		return self::getVar('request', $name, $default);
	}
	
	/**
	 * Set variable value into $_REQUEST
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setREQUEST(string $name, $value)
	{
		self::setVar($name, 'request', $value);
	}
	
	/**
	 * Delete variable from $_REQUEST
	 *
	 * @param string $name
	 */
	public static function deleteREQUEST(string $name)
	{
		self::deleteVar($name, 'request');
	}
	
	/**
	 * Flush $_REQUEST values
	 *
	 * @param array $replace = [] - replace $_REQUEST with
	 */
	public static function flushREQUEST(array $replace = [])
	{
		$_REQUEST = $replace;
	}
	################################################################################ END of $_REQUEST methods
	
	################################################################################ START of $_SERVER methods
	/**
	 * Does variable exists in in $_SERVER
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function existsSERVER(string $name): bool
	{
		return array_key_exists($name, $_SERVER);
	}
	
	/**
	 * Returns a $_SERVER[$name] value
	 * Leave all parametrs blank to get $_SERVER
	 *
	 * @param string $name    - variable name, leave blank to to get all
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	public static function getSERVER(string $name = self::UNDEFINED, $default = null)
	{
		return self::getVar('server', $name, $default);
	}
	
	/**
	 * Set variable value into $_SERVER
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setSERVER(string $name, $value)
	{
		self::setVar($name, 'server', $value);
	}
	################################################################################ END of $_SERVER methods
	
	/**
	 * Returns is the request method post
	 *
	 * @return bool
	 */
	public static function isPOST(): bool
	{
		return self::getRequestMethod() === 'post';
	}
	
	
	/**
	 * Get request method $_SERVER["REQUEST_METHOD"]
	 *
	 * @return string|bool IN LOWERCASE
	 */
	public static function getRequestMethod(): ?string
	{
		return isset($_SERVER["REQUEST_METHOD"]) ? strtolower($_SERVER["REQUEST_METHOD"]) : null;
	}
	
	/**
	 * is current request ajax type
	 *
	 * @return bool
	 */
	public static function isAjax(): bool
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Does current request accept json
	 *
	 * @return bool
	 */
	public static function acceptJSON(): bool
	{
		if (!isset($_SERVER['HTTP_ACCEPT']))
		{
			return false;
		}
		if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Got to link
	 *
	 * @param string $link        - where to go
	 * @param bool   $redirect301 - use header 301 to redirect
	 */
	public static function go(string $link = "", bool $redirect301 = false)
	{
		$link = str_replace('&amp;', '&', $link);
		$llen = strlen($link) - 1;
		if ($link[$llen] == "/")
		{
			$link = substr($link, 0, $llen);
		}
		if (!headers_sent())
		{
			if ($redirect301 == true)
			{
				Header("HTTP/1.1 301 Moved Permanently", true, 301);
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
		self::go($link, 0);
	}
	
	/**
	 * Redirect to referer url
	 *
	 * @param string $addExtraToRefLink - add extra params to link
	 */
	public static function goToReferer(string $addExtraToRefLink = '')
	{
		$link = self::getReferer() . $addExtraToRefLink;
		self::go($link);
	}
	
	/**
	 * Get referer url
	 *
	 * @return string
	 */
	public static function getReferer(): string
	{
		return (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : false;
	}
	
	/**
	 * Get Current request url
	 *
	 * @return string
	 */
	public static function getCurrentUrl(): string
	{
		$url = 'http';
		if (isset($_SERVER['HTTPS']))
		{
			$isHttps = strtolower($_SERVER['HTTPS']);
			if ($isHttps == 'on')
			{
				$url .= 's';
			}
		}
		
		return $url . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * Get current domain
	 *
	 * @return String
	 */
	public static function getHost(): string
	{
		return $_SERVER["HTTP_HOST"];
	}
	
	//############################################################################################################# SOF Helpers
	
	/**
	 * @param string $name does variable exists
	 * @param string $from (get, post, request, file, server)
	 * @return bool
	 */
	private static function existsVar(string $name, string $from): bool
	{
		if ($from == 'get')
		{
			return array_key_exists($name, $_GET);
		}
		elseif ($from === 'post')
		{
			return array_key_exists($name, $_POST);
		}
		elseif ($from == 'files')
		{
			return array_key_exists($name, $_FILES);
		}
		elseif ($from == 'request')
		{
			return array_key_exists($name, $_REQUEST);
		}
		elseif ($from == 'server')
		{
			return array_key_exists($name, $_SERVER);
		}
		
		return false;
	}
	
	/**
	 * Gets a http value
	 *
	 * @param string $from    (get,post,request)
	 * @param string $name    - leave blank to to get all
	 * @param mixed  $default - default value on not found
	 * @return mixed
	 */
	private static function getVar(string $from, string $name = self::UNDEFINED, $default = null)
	{
		if ($from == 'get')
		{
			return $name === self::UNDEFINED ? $_GET : (self::existsGET($name) ? $_GET[$name] : $default);
		}
		elseif ($from === 'post')
		{
			return $name === self::UNDEFINED ? $_POST : (self::existsPOST($name) ? $_POST[$name] : $default);
		}
		elseif ($from == 'files')
		{
			return $name === self::UNDEFINED ? $_FILES : (self::existsFILE($name) ? $_FILES[$name] : $default);
		}
		elseif ($from == 'request')
		{
			return $name === self::UNDEFINED ? $_REQUEST : (self::existsREQUEST($name) ? $_REQUEST[$name] : $default);
		}
		elseif ($from == 'server')
		{
			return $name === self::UNDEFINED ? $_SERVER : (self::existsSERVER($name) ? $_SERVER[$name] : $default);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Set variable and variable value to HTTP variable
	 *
	 * @param string $name
	 * @param string $to - (get,post,files,request)
	 * @param mixed  $value
	 * @return void
	 */
	private static function setVar(string $name, string $to, $value)
	{
		if ($to == 'get')
		{
			$_GET[$name] = $value;
		}
		elseif ($to == 'post')
		{
			$_POST[$name] = $value;
		}
		elseif ($to == 'files')
		{
			$_FILES[$name] = $value;
		}
		elseif ($to == 'request')
		{
			$_REQUEST[$name] = $value;
		}
		elseif ($to == 'server')
		{
			$_SERVER[$name] = $value;
		}
	}
	
	/**
	 * Delete variable from HTTP variable
	 *
	 * @param string $name
	 * @param string $from (get, post, request, file, server)
	 */
	private static function deleteVar(string $name, string $from)
	{
		if ($from == 'get')
		{
			if (array_key_exists($name, $_GET))
			{
				unset($_GET[$name]);
			}
		}
		elseif ($from === 'post')
		{
			if (array_key_exists($name, $_POST))
			{
				unset($_POST[$name]);
			}
		}
		elseif ($from == 'files')
		{
			if (array_key_exists($name, $_FILES))
			{
				unset($_FILES[$name]);
			}
		}
		elseif ($from == 'request')
		{
			if (array_key_exists($name, $_REQUEST))
			{
				unset($_REQUEST[$name]);
			}
		}
	}
}

?>