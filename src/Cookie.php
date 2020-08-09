<?php

namespace Infira\Utils;
class Cookie
{
	/**
	 * Get value from $_COOKIE
	 *
	 * @param string $name              - get value by $name
	 * @param mixed  $returnOnNotExists - if the var is not found this variable value is returned
	 * @return mixed
	 */
	public static function get(string $name, $returnOnNotExists = null)
	{
		if (self::exists($name))
		{
			$r = $_COOKIE[$name];
		}
		else
		{
			$r = $returnOnNotExists;
		}
		
		return $r;
	}
	
	/**
	 * Returns $_COOKIE
	 *
	 * @return array
	 */
	public function getAll(): array
	{
		return $_COOKIE;
	}
	
	/**
	 * Set item to $_COOKIE
	 *
	 * @param string     $name
	 * @param mixed      $value
	 * @param int|string $expires - when expires. (int)0 - forever,(string)"10 hours" -  will be converted to time using strtotime(), (int)1596885301 - timestamp. If $expires is in the past, it will be converted as forever.
	 * @param bool       $secure  Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client. When set to TRUE, the cookie will only be set if a secure connection exists. On the server-side, it's on the programmer to send this kind of cookie only on secure connection (e.g. with respect to $_SERVER["HTTPS"]).
	 * @see https://www.php.net/manual/en/function.setcookie.php
	 */
	public static function set(string $name, $value, $expires = 0, $secure = true)
	{
		$cookie_host = preg_replace('|^www\.(.*)$|', '.\\1', $_SERVER['HTTP_HOST']);
		/*
		 * Make sure that Driver get always integer
		 */
		if (!empty($expires))
		{
			if ($expires === "0")
			{
				$expires = 0;
			}
			elseif (is_string($expires))
			{
				if ($expires{0} != "+")
				{
					$expires = "+$expires";
				}
				$expires = strtotime($expires);
			}
			elseif (is_numeric($expires))
			{
				$expires = intval($expires);
			}
		}
		else
		{
			$expires = 0;
		}
		if ($expires == 0)
		{
			$expires = 2147483640;
		}
		$_COOKIE[$name] = $value;
		setcookie($name, $value, $expires, "/", $cookie_host, $secure);
	}
	
	/**
	 * Deletes item from $_COOKIE
	 *
	 * @param string $name
	 * @return void
	 */
	public static function delete(string $name): void
	{
		if (self::exists($name))
		{
			//Actually, there is not a way to directly delete a cookie. Just use setcookie with expiration date in the past, to trigger the removal mechanism in your browser. https://www.pontikis.net/blog/create-cookies-php-javascript
			unset($_COOKIE[$name]);
			// empty value and expiration one hour before
			setcookie($name, '', time() - 3600);
		}
	}
	
	/**
	 * Checks does item exists in $_COOKIE
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function exists(string $name): bool
	{
		return array_key_exists($name, $_COOKIE);
	}
	
	/**
	 * Flushes $_COOKIE
	 */
	public static function flush()
	{
		$_COOKIE = [];
	}
}

?>