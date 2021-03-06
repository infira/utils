<?php

namespace Infira\Utils;

use Infira\Utils\Variable as Variable;
use Infira\Utils\Fix as Fix;

class File
{
	
	/**
	 * Delete file
	 *
	 * @param string|array $files
	 * @return bool - success
	 */
	public static function delete($files): bool
	{
		if (checkArray($files))
		{
			foreach ($files as $file)
			{
				self::delete($file);
			}
			
			return true;
		}
		elseif (is_string($files))
		{
			if (file_exists($files))
			{
				if (unlink($files))
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Move file
	 *
	 * @param String $file
	 * @param String $to
	 * @return bool - success
	 */
	public static function move(string $file, string $to): bool
	{
		if (file_exists($file))
		{
			if (rename($file, $to))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Copy file
	 *
	 * @param String $file
	 * @param String $to
	 * @return bool - success
	 */
	public static function copy(string $file, string $to): bool
	{
		if (file_exists($file))
		{
			if (copy($file, $to))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Get file extension
	 *
	 * @param string $file    - filename with extension
	 * @param bool   $toLower - return extension name as lowercase string
	 * @return string
	 */
	public static function getExtension(string $file, bool $toLower = true)
	{
		$info = pathinfo($file);
		if (array_key_exists("extension", $info))
		{
			$ext = $info["extension"];
			
			return ($toLower) ? strtolower($ext) : $ext;
		}
		
		return "";
	}
	
	/**
	 * remove extension form file name
	 *
	 * @param string $file
	 * @return string
	 */
	public static function removeExtension(string $file)
	{
		return str_replace("." . self::getExtension($file, false), "", $file);
	}
	
	/**
	 * get file filename without extension
	 *
	 * @param string $file - file to be replaced
	 * @return string
	 */
	public static function getFileNameWithoutExtension(string $file)
	{
		return pathinfo($file)["filename"];
	}
	
	/**
	 * Replace filename with new one
	 *
	 * @param string $file    - file to be replaced
	 * @param string $newName without extension
	 * @return string
	 */
	public static function replaceName(string $file, string $newName)
	{
		$info    = pathinfo($file);
		$dirName = trim(Fix::dirPath($info["dirname"]));
		
		return $dirName . self::removeExtension($newName) . "." . $info["extension"];
	}
	
	/**
	 * Get file location dir
	 *
	 * @param string $file
	 * @return string
	 */
	public static function getPath(string $file)
	{
		$info = pathinfo($file);
		
		return $info["dirname"];
	}
	
	/**
	 * @param string   $file
	 * @param          $content
	 * @param string   $writeMode
	 * @param int|null $chmod - defautls to null, chmod will not be applied
	 * @param mixed    $owner - defautls to null, owner will not be applied
	 * @return bool
	 */
	public static function create(string $file, $content, $writeMode = "w+", int $chmod = null, $owner = null)
	{
		$fp = fopen($file, $writeMode);
		fwrite($fp, $content);
		fclose($fp);
		if ($owner)
		{
			chown($file, $owner);
		}
		if ($chmod > 0)
		{
			chmod($file, str_pad($chmod, 4, "0", STR_PAD_LEFT));
		}
		
		return true;
	}
	
	/**
	 * Put content to file
	 *
	 * @param string $file
	 * @param mixed  $content
	 * @return false|int
	 */
	public static function put(string $file, $content)
	{
		return file_put_contents($file, $content);
	}
	
	/**
	 * @param string $file
	 * @param mixed  $line
	 * @param bool   $addLineBreak - add break to end of the line
	 */
	public static function addLine(string $file, $line, $addLineBreak = true)
	{
		$fp = fopen($file, "a");
		if ($addLineBreak)
		{
			$line .= "\n";
		}
		fwrite($fp, $line);
		fclose($fp);
	}
	
	/**
	 * @param       $file
	 * @param array $steamContextOptions
	 * @throws Error
	 * @return bool|false|string
	 */
	public static function getContent(string $file, $steamContextOptions = [])
	{
		if (filter_var($file, FILTER_VALIDATE_URL))
		{
			$file = html_entity_decode($file);
			if (strpos(Variable::toLower($file), "https") !== false)
			{
				$steamContextOptions = array_merge(["ssl" => ["verify_peer" => false, "verify_peer_name" => false]], $steamContextOptions);
			}
			
			if (checkArray($steamContextOptions))
			{
				$content = file_get_contents($file, false, stream_context_create($steamContextOptions));
			}
			else
			{
				$content = file_get_contents($file);
			}
		}
		else
		{
			if (file_exists($file))
			{
				$content = file_get_contents($file);
			}
			else
			{
				throw new Error("file not found");
			}
		}
		
		return $content;
	}
	
	/**
	 * Send file to browser to download
	 *
	 * @param string      $file - file path
	 * @param string|NULL $downloadAsFileName
	 * @param bool        $deleteAfterDownload
	 * @throws Error
	 */
	public static function forceDownload(string $file, string $downloadAsFileName = null, bool $deleteAfterDownload = false)
	{
		if (file_exists($file))
		{
			$downloadAsFileName = ($downloadAsFileName != false) ? $downloadAsFileName : basename($file);
			
			// Set headers
			header('Content-Description: File Transfer');
			header('Content-Type: application/force-download');
			header('Content-Disposition: attachment; filename="' . $downloadAsFileName . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			//clean all levels of output buffering
			cleanOutput();
			readfile($file);
			if ($deleteAfterDownload == true)
			{
				ignore_user_abort(true);
				self::delete($file);
			}
			exit();
		}
		else
		{
			throw new Error("File not found");
		}
	}
	
	/**
	 * Get file mime type
	 *
	 * @param string $file    - full file location
	 * @param string $default - if mime type is not found then application/octet-stream is returned
	 * @return string
	 */
	public static function getMimeType(string $file, $default = "application/octet-stream"): string
	{
		$ext            = self::getExtension($file);
		$types          = [];
		$types["pdf"]   = "application/pdf";
		$types["exe"]   = "application/octet-stream";
		$types["zip"]   = "application/zip";
		$types["doc"]   = "application/msword";
		$types["xls"]   = "application/vnd.ms-excel";
		$types["ppt"]   = "application/vnd.ms-powerpoint";
		$types["gif"]   = "image/gif";
		$types["jpeg"]  = $types["jpg"] = $types["jpe"] = "image/jpg";
		$types['avi']   = 'video/avi';
		$types['bmp']   = 'image/bmp';
		$types['css']   = 'text/css';
		$types['htm']   = 'text/html';
		$types['html']  = 'text/html';
		$types['htmls'] = 'text/html';
		$types['ico']   = 'image/x-ico';
		$types['js']    = 'text/javascript';
		$types['midi']  = 'audio/midi';
		$types['mid']   = 'audio/midi';
		$types['mod']   = 'audio/mod';
		$types['mov']   = 'movie/quicktime';
		$types['mp3']   = 'audio/mp3';
		$types['mpg']   = 'video/mpeg';
		$types['mpeg']  = 'video/mpeg';
		$types['png']   = 'image/png';
		$types['swf']   = 'application/shockwave-flash';
		$types['tif']   = 'image/tiff';
		$types['tiff']  = 'image/tiff';
		$types['wav']   = 'audio/wav';
		$types['xbm']   = 'image/xbm';
		$types['xml']   = 'text/xml';
		if (isset($types[$ext]))
		{
			return $types[$ext];
		}
		else
		{
			return $default;
		}
	}
	
	/**
	 * Show file content as header
	 *
	 * @param string $file
	 * @param bool   $deleteAfterDownload
	 */
	public static function show(string $file, bool $deleteAfterDownload = false)
	{
		if (file_exists($file))
		{
			header('Content-Type: ' . self::getMimeType($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			readfile($file);
			if ($deleteAfterDownload === true)
			{
				self::delete($file);
			}
			// ob_end_clean();
			exit();
		}
	}
	
	/**
	 * Human readable file size
	 *
	 * @param string $file
	 * @return string file size
	 */
	public static function formatSize(string $file): string
	{
		$bytes     = filesize($file);
		$units     = ['b', 'kb', 'mb', 'gb'];
		$converted = $bytes . ' ' . $units[0];
		for ($i = 0; $i < count($units); $i++)
		{
			if (($bytes / pow(1024, $i)) >= 1)
			{
				$converted = round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
			}
		}
		
		return $converted;
	}
	
	/**
	 * Readable filesize
	 *
	 * @param string $fileUrl - full url
	 * @param string $saveTo  - path
	 * @throws Error
	 * @return bool
	 */
	public static function download(string $fileUrl, string $saveTo)
	{
		return file_put_contents($saveTo, self::getContent($fileUrl));
	}
}

?>