<?php

namespace Infira\Utils;

class Session
{
	protected static $SID = false;
	
	/**
	 * Is session expired
	 *
	 * @var boolean
	 */
	private static $isExpired = false;
	
	/**
	 * Is php session started withd session_start()
	 *
	 * @var bool
	 */
	public static $isStarted = false;
	
	private static $sessionName;
	private static $timeout;
	private static $sessionTime = 31536000;//365 * 24 * 60 * 60  - year
	
	/**
	 * Config sessions
	 *
	 * @param string $sessionName - name of the PHP session
	 * @param int    $timeout     timeout in seconds
	 */
	public static function init(string $sessionName = "PHPSESSID", $timeout = 86400)
	{
		if ($sessionName !== 'PHPSESSID')
		{
			$sessionName = "PHPSESSID_$sessionName";
		}
		self::$sessionName = $sessionName;
		self::$timeout     = $timeout;
		if (self::$isStarted == false)
		{
			self::$isStarted = true;
			if (ini_get('session.auto_start') == 0)
			{
				if (headers_sent())
				{
					debug_print_backtrace();
				}
				self::start();
			}
		}
		//debug('session_id()', session_id());
		self::setSID(session_id());
		
		
		$upTime  = self::get("_sessionUpdateTime", time());
		$between = time() - $upTime;
		if ($between > self::$timeout and $upTime > 0)
		{
			self::destroy(true);
			self::$isExpired = true;
		}
		else
		{
			self::$isExpired = false;
		}
		//debug(self::$sessionName);
		//debug($_SESSION);
		//debug("------------------------------------------------");
		self::set("_sessionUpdateTime", time());
	}
	
	/**
	 * Retrives a 32bit session id hash
	 *
	 * @return string
	 */
	public static function getSID(): string
	{
		return self::$SID;
	}
	
	/**
	 * Set a 32bit session id hash
	 *
	 * @param string $SID
	 */
	private static function setSID(string $SID)
	{
		self::$SID = $SID;
	}
	
	/**
	 * Destroy session
	 *
	 * @param bool $takeNewID - take new session ID
	 */
	public static function destroy(bool $takeNewID = true)
	{
		self::flush();
		session_unset();
		session_destroy();
		if (self::$sessionName)
		{
			setcookie(self::$sessionName, "", 1);
			session_name(self::$sessionName);
			session_set_cookie_params(self::$sessionTime);
		}
		self::start(); //start new session
		//take new session ID
		if ($takeNewID)
		{
			session_regenerate_id(true);
			$SID = session_id();
			self::setSID($SID);
		}
		unset($_COOKIE[session_name()]);
	}
	
	public static function close()
	{
		session_write_close();
	}
	
	/**
	 * Sometimes PHP gives error session_id too long or containing illegal charactes
	 * Here is the solition http://stackoverflow.com/questions/3185779/the-session-id-is-too-long-or-contains-illegal-characters-valid-characters-are
	 */
	private static function start()
	{
		$sn = self::$sessionName;
		if (isset($_COOKIE[$sn]))
		{
			$sessid = $_COOKIE[$sn];
			if (preg_match('/.{32},.*/si', $sessid))
			{
				$str    = $sessid;
				$sessid = substr($str, 0, 32);
				$ex     = explode(",", trim(substr($str, 33)));
				foreach ($ex as $part)
				{
					$part = trim($part);
					if (strpos($part, "="))
					{
						$ex2 = explode("=", $part);
						Cookie::set(trim($ex2[0]), $ex2[1]);
					}
				}
			}
		}
		if (isset($sessid))
		{
			session_id($sessid);
		}
		if (self::$sessionName)
		{
			session_name(self::$sessionName);
		}
		session_set_cookie_params(self::$sessionTime);
		session_start();
		self::setSessionCookie();
		
		/*
		if (!preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $sessid))
		{
			return false;
		}
		session_start();
		self::setSessionCookie();
		*/
		
		return true;
	}
	
	private static function setSessionCookie()
	{
		if (self::$sessionName)
		{
			if (isset($_COOKIE[self::$sessionName]))
			{
				setcookie(self::$sessionName, $_COOKIE[self::$sessionName], time() + self::$sessionTime, "/");
			}
		}
	}
	
	/**
	 * Get value from session, if this cache is true
	 *
	 * @param string|null $name    - get value by $name, if null all session values are returned
	 * @param mixed       $default - if the var is not found this variable value is returned
	 * @return mixed
	 */
	public static function get(string $name = null, $default = false)
	{
		if ($name == null)
		{
			return $_SESSION;
		}
		if (self::exists($name))
		{
			return $_SESSION[$name];
		}
		
		return $default;
	}
	
	/**
	 * Set value to session
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function set(string $name, $value)
	{
		$_SESSION[$name] = $value;
	}
	
	/**
	 * Delete value from session
	 *
	 * @param string $name
	 */
	public static function delete(string $name)
	{
		if (array_key_exists($name, $_SESSION))
		{
			unset($_SESSION[$name]);
		}
	}
	
	/**
	 * Checks is the variable exists in $_SESSION
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function exists(string $name): bool
	{
		return array_key_exists($name, $_SESSION);
	}
	
	/**
	 * Ends session and writes cache to $_SESSION
	 */
	public static function flush()
	{
		$_SESSION = [];
	}
	
	/**
	 * Checks is session expired
	 *
	 * @return bool
	 */
	public static function isExpired(): bool
	{
		return self::$isExpired;
	}
	
} // Session
?>