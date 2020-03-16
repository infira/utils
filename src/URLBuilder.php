<?php

namespace Infira\Utils;;
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
	 * @param string|array $aie - add to url if exists in current url, example current url is ?name=gen, self::get(["lastName"=>"series"],"name") outpiuts url ?name=gen&lastName=series
	 * @param bool $getNiceUrl - true to return nice url instead of /?id=mainPage&subPage=content you get /mainPage?subPage=content
	 * @return string
	 */
	public static function get($urlParams = [], $aie = [], $getNiceUrl = TRUE)
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
		
		
		if (is_string($aie))
		{
			$aie = Variable::toArray($aie);
		}
		if (checkArray($aie))
		{
			foreach ($aie as $name)
			{
				if (Http::existsGET($name) AND !array_key_exists($name, $urlParams))
				{
					$urlParams[$name] = Http::getGET($name);
				}
			}
		}
		$link = self::$root;
		
		if (checkArray($urlParams))
		{
			$link .= "?" . http_build_query($urlParams, "&");
		}
		
		if ($getNiceUrl AND isset($urlParams[self::$mainVar]))
		{
			$mv = $urlParams[self::$mainVar];
			$mvr = $urlParams[self::$mainVar];
			$q = "";
			if (count($urlParams) > 1)
			{
				$q = "?";
			}
			$link = str_replace("?route=$mv", "/$mvr" . $q, $link);
			$link = str_replace('?&', '?', $link);
		}
		
		return $link;
	}
}

?>