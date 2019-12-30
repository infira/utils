<?php

namespace InfiraUtils;
/**
 * Class that handles url and link constructors
 */
class URLBuilder
{
	public static $mainVar = "route";
	public static $root    = "";
	
	/**
	 * Its like construct
	 * @param string $mainVar - the main var of url, for example route, http://domain.com/?route=mainpage
	 * @param string $root - base url for url builder for example http://domain.com/, defaults to nothing
	 */
	public static function init(string $mainVar = "route", string $root = "")
	{
		self::$mainVar = $mainVar;
		self::$root = $root;
	}
	
	/**
	 * Generate url string
	 *
	 * @param string|array $urlParams
	 * @param bool $getNiceUrl - true to reutnr nice url instead of /?id=mainPage&subPage=content you get /mainPage?subPage=content
	 * @param string|array $flag - add to end of the url sepearated with : for exmplae http://domain.com/mainPage:flag1,flag2?route=mainPage
	 * @return string
	 */
	public static function get($urlParams = [], $getNiceUrl = TRUE, $flag = NULL)
	{
		$tmpID = FALSE;
		if (checkArray($urlParams))
		{
			if (count($urlParams) == 2 && isset($urlParams[self::$mainVar]) and isset($urlParams[0]))
			{
				$tmpID = $urlParams[self::$mainVar];
				$urlParams = $urlParams[0];
			}
		}
		elseif (is_string($urlParams))
		{
			if (strpos($urlParams, "=") !== FALSE)
			{
				$urlParams = parseStr($urlParams);
			}
			else
			{
				$tmpID = $urlParams;
				$urlParams = [];
			}
		}
		else
		{
			$urlParams = [];
		}
		
		if ($tmpID !== FALSE)
		{
			$urlParams[self::$mainVar] = $tmpID;
		}
		
		$link = "";
		
		
		if (checkArray($urlParams))
		{
			$link .= "?" . http_build_query($urlParams, "&");
		}
		
		$link = self::$root . $link;
		if ($getNiceUrl AND isset($urlParams[self::$mainVar]))
		{
			$mv = $urlParams[self::$mainVar];
			$mvr = $urlParams[self::$mainVar];
			if ($flag !== NULL)
			{
				$mvr .= ":" . join(",", Variable::toArray($flag));
			}
			$q = "";
			if (count($urlParams) > 1)
			{
				$q = "?";
			}
			$link = str_replace("?route=$mv", "/$mvr" . $q, $link);
			$link = str_replace('?&', '?', $link);
		}
		else
		{
			if ($flag !== NULL)
			{
				$link .= ":" . join(",", Variable::toArray($flag));
			}
		}
		
		return $link;
	}
}

?>