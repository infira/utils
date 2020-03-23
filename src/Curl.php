<?php

namespace Infira\Utils;

class Curl
{
	/**
	 * Get url content with curl
	 *
	 * @param string $url
	 * @param array  $optons
	 * @return mixed
	 */
	public static function getContent(string $url, array $optons = [])
	{
		$user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		
		$sendOptions = [
			
			CURLOPT_CUSTOMREQUEST  => "GET",        //set request type post or get
			CURLOPT_USERAGENT      => $user_agent, CURLOPT_HTTPGET => TRUE,        //set to GET
			CURLOPT_POST           => FALSE,        //set to GET
			CURLOPT_RETURNTRANSFER => TRUE,     // return web page
			CURLOPT_HEADER         => TRUE,    // don't return headers
			CURLOPT_FOLLOWLOCATION => TRUE,     // follow redirects
			CURLOPT_VERBOSE        => TRUE,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_AUTOREFERER    => TRUE,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 5,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		];
		if (strpos(Variable::toLower($url), "https") !== FALSE)
		{
			$sendOptions[CURLOPT_SSL_VERIFYPEER] = FALSE;
			$sendOptions[CURLOPT_SSL_VERIFYHOST] = FALSE;
		}
		
		if (isset($optons["username"]) AND isset($optons["password"]))
		{
			$sendOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
			$sendOptions[CURLOPT_USERPWD]  = base64_encode($optons["username"] . ":" . $optons["password"]);
			unset($optons["username"]);
			unset($optons["password"]);
		}
		
		if ($optons)
		{
			$sendOptions = array_merge($sendOptions, $optons);
		}
		
		$ch = curl_init($url);
		curl_setopt_array($ch, $sendOptions);
		$content = curl_exec($ch);
		
		$err    = curl_errno($ch);
		$errmsg = curl_error($ch);
		$header = curl_getinfo($ch);
		curl_close($ch);
		
		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $content;
		
		return $header;
	}
	
}

?>