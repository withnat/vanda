<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2024, Nat Withe. All rights reserved.
 * @link        https://vanda.io
 */

declare(strict_types=1);

namespace System;

/**
 * Class File
 *
 * The File class provides utility methods for working with files, such as
 * reading, writing, copying, moving, deleting, and manipulating file paths.
 * It encapsulates various file operations, making it easier to handle
 * file-related tasks within a PHP application.
 *
 * @package System
 */
class File
{
	protected static $_mimes = [
		'hqx' => ['application/mac-binhex40', 'application/mac-binhex', 'application/x-binhex40', 'application/x-mac-binhex40'],
		'cpt' => 'application/mac-compactpro',
		'csv' => ['text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'],
		'bin' => ['application/macbinary', 'application/mac-binary', 'application/octet-stream', 'application/x-binary', 'application/x-macbinary'],
		'dms' => 'application/octet-stream',
		'lha' => 'application/octet-stream',
		'lzh' => 'application/octet-stream',
		'exe' => ['application/octet-stream', 'application/x-msdownload'],
		'class' => 'application/octet-stream',
		'psd' => ['application/x-photoshop', 'image/vnd.adobe.photoshop'],
		'so' => 'application/octet-stream',
		'sea' => 'application/octet-stream',
		'dll' => 'application/octet-stream',
		'oda' => 'application/oda',
		'pdf' => ['application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'],
		'ai' => ['application/pdf', 'application/postscript'],
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'mif' => 'application/vnd.mif',
		'xls' => ['application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel', 'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls', 'application/excel', 'application/download', 'application/vnd.ms-office', 'application/msword'],
		'ppt' => ['application/powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/msword'],
		'pptx' => 	['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/x-zip', 'application/zip'],
		'wbxml' => 'application/wbxml',
		'wmlc' => 'application/wmlc',
		'dcr' => 'application/x-director',
		'dir' => 'application/x-director',
		'dxr' => 'application/x-director',
		'dvi' => 'application/x-dvi',
		'gtar' => 'application/x-gtar',
		'gz' => 'application/x-gzip',
		'gzip' => 'application/x-gzip',
		'php' => ['application/x-httpd-php', 'application/php', 'application/x-php', 'text/php', 'text/x-php', 'application/x-httpd-php-source'],
		'php4' => 'application/x-httpd-php',
		'php3' => 'application/x-httpd-php',
		'phtml' => 'application/x-httpd-php',
		'phps' => 'application/x-httpd-php-source',
		'js' => ['application/x-javascript', 'text/plain'],
		'swf' => 'application/x-shockwave-flash',
		'sit' => 'application/x-stuffit',
		'tar' => 'application/x-tar',
		'tgz' => ['application/x-tar', 'application/x-gzip-compressed'],
		'z' => 'application/x-compress',
		'xhtml' => 'application/xhtml+xml',
		'xht' => 'application/xhtml+xml',
		'zip' => ['application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/s-compressed', 'multipart/x-zip'],
		'rar' => ['application/x-rar', 'application/rar', 'application/x-rar-compressed'],
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'mpga' => 'audio/mpeg',
		'mp2' => 'audio/mpeg',
		'mp3' => ['audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'],
		'aif' => ['audio/x-aiff', 'audio/aiff'],
		'aiff' => ['audio/x-aiff', 'audio/aiff'],
		'aifc' => 'audio/x-aiff',
		'ram' => 'audio/x-pn-realaudio',
		'rm' => 'audio/x-pn-realaudio',
		'rpm' => 'audio/x-pn-realaudio-plugin',
		'ra' => 'audio/x-realaudio',
		'rv' => 'video/vnd.rn-realvideo',
		'wav' => ['audio/x-wav', 'audio/wave', 'audio/wav'],
		'bmp' => ['image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'],
		'gif' => 'image/gif',
		'jpeg' => ['image/jpeg', 'image/pjpeg'],
		'jpg' => ['image/jpeg', 'image/pjpeg'],
		'jpe' => ['image/jpeg', 'image/pjpeg'],
		'jp2' => ['image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'],
		'j2k' => ['image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'],
		'jpf' => ['image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'],
		'jpg2' => ['image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'],
		'jpx' => ['image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'],
		'jpm' => ['image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'],
		'mj2' => ['image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'],
		'mjp2' => ['image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'],
		'png' => ['image/png', 'image/x-png'],
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'css' => ['text/css', 'text/plain'],
		'html' => ['text/html', 'text/plain'],
		'htm' => ['text/html', 'text/plain'],
		'shtml' => ['text/html', 'text/plain'],
		'txt' => 'text/plain',
		'text' => 'text/plain',
		'log' => ['text/plain', 'text/x-log'],
		'rtx' => 'text/richtext',
		'rtf' => 'text/rtf',
		'xml' => ['application/xml', 'text/xml', 'text/plain'],
		'xsl' => ['application/xml', 'text/xsl', 'text/xml'],
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'avi' => ['video/x-msvideo', 'video/msvideo', 'video/avi', 'application/x-troff-msvideo'],
		'movie' => 'video/x-sgi-movie',
		'doc' => ['application/msword', 'application/vnd.ms-office'],
		'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword', 'application/x-zip'],
		'dot' => ['application/msword', 'application/vnd.ms-office'],
		'dotx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword'],
		'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/vnd.ms-excel', 'application/msword', 'application/x-zip'],
		'word' => ['application/msword', 'application/octet-stream'],
		'xl' => 'application/excel',
		'eml' => 'message/rfc822',
		'json' => ['application/json', 'text/json'],
		'pem' => ['application/x-x509-user-cert', 'application/x-pem-file', 'application/octet-stream'],
		'p10' => ['application/x-pkcs10', 'application/pkcs10'],
		'p12' => 'application/x-pkcs12',
		'p7a' => 'application/x-pkcs7-signature',
		'p7c' => ['application/pkcs7-mime', 'application/x-pkcs7-mime'],
		'p7m' => ['application/pkcs7-mime', 'application/x-pkcs7-mime'],
		'p7r' => 'application/x-pkcs7-certreqresp',
		'p7s' => 'application/pkcs7-signature',
		'crt' => ['application/x-x509-ca-cert', 'application/x-x509-user-cert', 'application/pkix-cert'],
		'crl' => ['application/pkix-crl', 'application/pkcs-crl'],
		'der' => 'application/x-x509-ca-cert',
		'kdb' => 'application/octet-stream',
		'pgp' => 'application/pgp',
		'gpg' => 'application/gpg-keys',
		'sst' => 'application/octet-stream',
		'csr' => 'application/octet-stream',
		'rsa' => 'application/x-pkcs7',
		'cer' => ['application/pkix-cert', 'application/x-x509-ca-cert'],
		'3g2' => 'video/3gpp2',
		'3gp' => ['video/3gp', 'video/3gpp'],
		'mp4' => 'video/mp4',
		'm4a' => 'audio/x-m4a',
		'f4v' => ['video/mp4', 'video/x-f4v'],
		'flv' => 'video/x-flv',
		'webm' => 'video/webm',
		'aac' => 'audio/x-acc',
		'm4u' => 'application/vnd.mpegurl',
		'm3u' => 'text/plain',
		'xspf' => 'application/xspf+xml',
		'vlc' => 'application/videolan',
		'wmv' => ['video/x-ms-wmv', 'video/x-ms-asf'],
		'au' => 'audio/x-au',
		'ac3' => 'audio/ac3',
		'flac' => 'audio/x-flac',
		'ogg' => ['audio/ogg', 'video/ogg', 'application/ogg'],
		'kmz' => ['application/vnd.google-earth.kmz', 'application/zip', 'application/x-zip'],
		'kml' => ['application/vnd.google-earth.kml+xml', 'application/xml', 'text/xml'],
		'ics' => 'text/calendar',
		'ical' => 'text/calendar',
		'zsh' => 'text/x-scriptzsh',
		'7zip' => ['application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'],
		'cdr' => ['application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'],
		'wma' => ['audio/x-ms-wma', 'video/x-ms-asf'],
		'jar' => ['application/java-archive', 'application/x-java-application', 'application/x-jar', 'application/x-compressed'],
		'svg' => ['image/svg+xml', 'application/xml', 'text/xml'],
		'vcf' => 'text/x-vcard',
		'srt' => ['text/srt', 'text/plain'],
		'vtt' => ['text/vtt', 'text/plain'],
		'ico' => ['image/x-icon', 'image/x-ico', 'image/vnd.microsoft.icon']
	];

	/**
	 * File constructor.
	 */
	private function __construct(){}

	/**
	 * Returns the name without path.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getName('/path/to/picture.jpg');
	 * // The $result will be: 'picture.jpg'
	 * ```
	 *
	 * @param  string $file  File path.
	 * @return string        Returns the file name.
	 */
	public static function getName(string $file) : string
	{
		return basename($file);
	}

	/**
	 * Returns the name without extension.
	 *
	 *This method is different from `File::removeExtension()` in that
	 * it will remove the path information and return only the file name.
	 *
	 * For example,
	 *
	 * ```php
	 * $resule = File::getNameWithoutExtension('picture.jpg');
	 * // The $result will be: 'picture'
	 * ```
	 *
	 * @param  string $file  File path.
	 * @return string        Returns the file name without extension.
	 */
	public static function getNameWithoutExtension(string $file) : string
	{
		return basename($file, '.' . static::getExtension($file));
	}

	/**
	 * Gets the extension of a file name.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getExtension('picture.jpg');
	 * // The $result will be: 'jpg'
	 * ```
	 *
	 * @param  string $file  The file name.
	 * @return string        Returns the extension of the file.
	 */
	public static function getExtension(string $file) : string
	{
		return pathinfo($file)['extension'];
	}

	/**
	 * Gets the path of a file.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getPath('/path/to/picture.jpg');
	 * // The $result will be: '/path/to'
	 * ```
	 *
	 * @param  string $file  The file path.
	 * @return string        Returns the path of the file.
	 */
	public static function getPath(string $file) : string
	{
		return dirname($file);
	}

	/**
	 * Changes the extension of a file.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::changeExtension('picture.jpg', 'png');
	 * // The $result will be: 'picture.png'
	 * ```
	 *
	 * @param  string $file          The file name.
	 * @param  string $newExtension  The new extension.
	 * @return string                Returns the file name with new extension.
	 */
	public static function changeExtension(string $file, string $newExtension) : string
	{
		$filename = static::getNameWithoutExtension($file);
		$newExtension = ltrim($newExtension, '.');

		return $filename . '.' . $newExtension;
	}

	/**
	 * Makes file name safe to use.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::makeSafe('pic%ture.jpg');
	 * // The $result will be: 'picture.jpg'
	 * ```
	 *
	 * @param  string $file  The file name, excluding the path.
	 * @return string        The sanitised string.
	 */
	public static function makeSafe(string $file) : string
	{
		// Remove any trailing dots, as those aren't ever valid file names.
		$file = rtrim($file, '.');
		$regex = ['#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#'];

		return trim(preg_replace($regex, '', $file));
	}

	/**
	 * Remove the extension from a file name.
	 *
	 * This method is different from `File::getNameWithoutExtension()` in that
	 * it will keep the path information.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::removeExtension('/path/to/picture.jpg');
	 * // The $result will be: '/path/to/picture'
	 * ```
	 *
	 * @param  string $file  The file name (and path).
	 * @return string        The file name (and path) without the extension.
	 */
	public static function removeExtension(string $file) : string
	{
		return preg_replace('#\.[^.]*$#', '', $file);
	}

	/**
	 * Returns true if filename exists.
	 *
	 * @param  string $file  The file name.
	 * @return bool          Returns true if file exists. False otherwise.
	 */
	public static function exists(string $file) : bool
	{
		return is_file($file);
	}

	/**
	 * Delete a file and its thumbnails if any.
	 *
	 * @param  string $file  The file name.
	 * @return bool          Returns true if file was deleted. False otherwise.
	 */
	public static function delete(string $file) : bool
	{
		if (!is_file($file))
			return false;

		@chmod($file, 0755);

		if (@unlink($file))
		{
			$fileName = basename($file);
			$resizePath = dirname($file) . '/' . RESIZE_DIR;

			$fp = @opendir($resizePath);

			if ($fp)
			{
				while (($entry = readdir($fp)) !== false)
				{
					if ($entry === '.' or $entry === '..')
						continue;

					if (@unlink($resizePath . '/' . $entry . '/' . $fileName))
					{
						if (Folder::isEmpty($resizePath . '/' . $entry))
							Folder::delete($resizePath . '/' . $entry);
					}
				}

				closedir($fp);

				if (Folder::isEmpty($resizePath))
					Folder::delete($resizePath);
			}

			return true;
		}
		else
		{
			$error = Error::getLast();
			Log::add($error . 'Delete file failed: ' . $file);;

			return false;
		}
	}

	/**
	 * Determines if the file is an image.
	 *
	 * @param  string $file  The file to check.
	 * @return bool          Returns true if the given file is an image. False otherwise.
	 */
	public static function isImage(string $file) : bool
	{
		return @exif_imagetype($file) > 0;
	}

	/**
	 * Gets the size of a file.
	 *
	 * @param  string      $file       The file to get the size of.
	 * @param  int         $precision  The number of decimal places to round to.
	 * @param  string|null $unit       The unit to use. If null, the unit will be automatically determined.
	 * @return string                  Returns the size of the file.
	 */
	public static function getSize(string $file, int $precision = 1, string $unit = null) : string
	{
		$size = @filesize($file);
		$size = Number::byteFormat($size, $precision, $unit);

		return $size;
	}

	/**
	 * @param  string $filename
	 * @param  string $folder
	 * @param  string $calledFromPath
	 * @return string
	 */
	public static function getAssetPath(string $filename, string $folder, string $calledFromPath) : string
	{
		$possibleFilePaths = static::_getPossibleAssetPaths($calledFromPath);

		foreach ($possibleFilePaths as $possibleFilePath)
		{
			switch ($possibleFilePath)
			{
				case 'themeAssetFolder':
					$path = THEME_PATH . DS . 'assets' . DS . $folder . DS . $filename;
					break;

				case 'themeAssetRootFolder':
					$path = THEME_PATH . DS . 'assets' . DS . $filename;
					break;

				case 'systemModuleAssetFolder':
					$path = PATH_SYSTEM . DS . 'modules. ' . DS . SIDE . DS . MODULE . DS . 'assets' . DS . $folder . DS . $filename;
					break;

				case 'systemModuleAssetRootFolder':
					$path = PATH_SYSTEM . DS . 'modules. ' . DS . SIDE . DS . MODULE . DS . 'assets' . DS . $filename;
					break;

				case 'appModuleAssetFolder':
					$path = PATH_APP . DS . 'modules. ' . DS . SIDE . DS . MODULE . DS . 'assets' . DS . $folder . DS . $filename;
					break;

				case 'appModuleAssetRootFolder':
					$path = PATH_APP . DS . 'modules. ' . DS . SIDE . DS . MODULE . DS . 'assets' . DS . $filename;
					break;

				case 'systemAssetFolder':
					$path = PATH_SYSTEM . DS . 'assets' . DS . $folder . DS . $filename;
					break;

				case 'systemAssetRootFolder':
					$path = PATH_SYSTEM . DS . 'assets' . DS . $filename;
					break;

				case 'appAssetFolder':
					$path = PATH_APP . DS . 'assets' . DS . $folder . DS . $filename;
					break;

				// appAssetRootFolder
				default:
					$path = PATH_APP . DS . 'assets' . DS . $filename;
			}

			if (is_file($path))
			{
				$path = substr_replace($path, '', 0, strlen(PATH_BASE . DS));
				return $path;
			}
		}

		return $filename;
	}

	/**
	 * @param  string $calledFromPath
	 * @return array
	 */
	private static function _getPossibleAssetPaths(string $calledFromPath) : array
	{
		$appModulePath = PATH_APP . DS . 'modules' . DS . SIDE . DS . MODULE;
		$systemModulePath = PATH_SYSTEM . DS . 'modules' . DS . SIDE . DS . MODULE;

		if (stripos($calledFromPath, THEME_PATH) !== false)
		{
			$possibleAssetPaths = [
				'themeAssetFolder',
				'themeAssetRootFolder',
				'appAssetFolder',
				'appAssetRootFolder',
				'systemAssetFolder',
				'systemAssetRootFolder'
			];
		}
		elseif (stripos($calledFromPath, $appModulePath) !== false)
		{
			$possibleAssetPaths = [
				'appModuleAssetFolder',
				'appModuleAssetRootFolder',

				'systemModuleAssetFolder',
				'systemModuleAssetRootFolder',

				'appAssetFolder',
				'appAssetRootFolder',

				'systemAssetFolder',
				'systemAssetRootFolder'
			];
		}
		elseif (stripos($calledFromPath, $systemModulePath) !== false)
		{
			$possibleAssetPaths = [
				'systemModuleAssetFolder',
				'systemModuleAssetRootFolder',

				'systemAssetFolder',
				'systemAssetRootFolder',

				'appAssetFolder',
				'appAssetRootFolder'
			];
		}
		// ie. call from /system/MVC/view.php
		elseif (stripos($calledFromPath, PATH_SYSTEM) !== false)
		{
			$possibleAssetPaths = [
				'systemAssetFolder',
				'systemAssetRootFolder',

				'appAssetFolder',
				'appAssetRootFolder'
			];
		}
		else
		{
			$possibleAssetPaths = [
				'appAssetFolder',
				'appAssetRootFolder'
			];
		}

		return $possibleAssetPaths;
	}

	/**
	 * @param  array        $paths
	 * @return string|false
	 */
	public static function getExactFilePath(array $paths)
	{
		foreach ($paths as $path)
		{
			if (is_file($path))
				return $path;
		}

		return false;
	}

	/**
	 * @param  string      $file
	 * @param  string|null $data
	 * @param  bool        $setMime
	 * @return void
	 */
	public static function download(string $file, string $data = null, bool $setMime = true) : void
	{
		if (is_null($data))
		{
			if (!is_file($file))
				return;

			$filesize = @filesize($file);

			if (!$filesize)
				return;

			$filename = basename($file);
		}
		else
		{
			$filename = $file;
			$filesize = strlen($data);
		}

		$mime = 'application/octet-stream';
		$extension = static::getExtension($filename);

		if ($setMime === true)
		{
			if (!$extension)
				return;

			if (isset(static::$_mimes[$extension]))
			{
				if (is_array(static::$_mimes[$extension]))
					$mime = static::$_mimes[$extension][0];
				else
					$mime = static::$_mimes[$extension];
			}
		}

		$fp = @fopen($file, 'rb');

		if (is_null($data) and $fp === false)
			return;

		// Clean output buffer
		if (ob_get_level() !== 0 and @ob_end_clean() === false)
			@ob_clean();

		header('Content-Type: ' . $mime);
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . $filesize); // show file size during download.
		header('Cache-Control: private, no-transform, no-store, must-revalidate');
		header('Expires: 0');

		if (!is_null($data))
			exit($data);

		// Flush 1MB chunks of data
		while (!feof($fp) and ($data = fread($fp, 1048576)) !== false)
			echo $data;

		fclose($fp);

		exit;
	}

	/**
	 * @param  string       $file
	 * @return string|false
	 */
	public static function getMime(string $file)
	{
		if (is_file($file))
			return mime_content_type($file);
		else
			return false;
	}

	/**
	 * @param  string       $file
	 * @return string|false
	 */
	public static function getMimeByExtension(string $file)
	{
		$extension = strtolower(static::getExtension($file));

		if (isset(static::$_mimes[$extension]))
		{
			if (is_array(static::$_mimes[$extension]))
				return static::$_mimes[$extension][0];
			else
				return static::$_mimes[$extension];
		}

		return false;
	}

	/**
	 * @param  string $file
	 * @return array
	 */
	public static function getInfo(string $file) : array
	{
		$fileinfo['name'] = basename($file);
		$fileinfo['path'] = $file;
		$fileinfo['size'] = @filesize($file);
		$fileinfo['date'] = @filemtime($file);
		$fileinfo['readable'] = is_readable($file);
		$fileinfo['writable'] = static::isWritable($file);
		$fileinfo['executable'] = is_executable($file);
		$fileinfo['fileperms'] = @fileperms($file);

		return $fileinfo;
	}

	/**
	 * @param  string       $file
	 * @return string|false
	 */
	public static function read(string $file)
	{
		return @file_get_contents($file);
	}

	/**
	 * @param  string $file
	 * @param  string $data
	 * @param  string $mode
	 * @return bool
	 */
	public static function write(string $file, string $data, string $mode = 'wb') : bool
	{
		@set_time_limit((int)ini_get('max_execution_time'));

		$fp = @fopen($file, $mode);

		if (!$fp)
			return false;

		flock($fp, LOCK_EX);

		$result = 0;
		$written = 0;
		$length = mb_strlen($data);

		while ($written < $length)
		{
			$result = fwrite($fp, substr($data, $written));

			if ($result === false)
				break;

			$written += $result;
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}

	/**
	 * Tests for file writability.
	 *
	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the file, based on the read-only attribute. is_writable() is also unreliable
	 * on Unix servers if safe_mode is on.
	 *
	 * @link   https://bugs.php.net/bug.php?id=54709
	 * @param  string
	 * @return bool
	 */
	public static function isWritable(string $file) : bool
	{
		// If we're on a Unix server we call is_writable
		if (DS === '/')
			return is_writable($file);
		// For Windows servers we'll actually write a file then read it.
		else
		{
			if (is_dir($file))
			{
				$file = rtrim($file, '/') . '/' . md5(mt_rand());

				if (($fp = @fopen($file, 'ab')) === false)
					return false;

				fclose($fp);
				@chmod($file, 0777);
				@unlink($file);

				return true;
			}
			elseif (is_file($file))
			{
				$fp = @fopen($file, 'ab');

				if ($fp === false)
					return false;

				fclose($fp);

				return true;
			}
			else
				return false;
		}
	}

	/**
	 * @param  string $file
	 * @return string
	 */
	public static function getPermission(string $file) : string
	{
		$perms = @fileperms($file);

		return substr(sprintf('%o', $perms), -3);
	}

	/**
	 * @param  string $src
	 * @param  string $dest
	 * @param  bool $overwrite
	 * @return bool
	 */
	public static function copy(string $src, string $dest, bool $overwrite = false) : bool
	{
		if (!is_readable($src))
		{
			Log::add('Can\'t find or read file: ' . $src);
			return false;
		}

		if (is_file($dest) and !$overwrite)
		{
			Log::add('Destination file already exists: ' . $dest);
			return false;
		}

		return @copy($src, $dest);
	}

	/**
	 * @param  string $src
	 * @param  string $dest
	 * @param  bool   $overwrite
	 * @return bool
	 */
	public static function move(string $src, string $dest, bool $overwrite = false) : bool
	{
		if (static::copy($src, $dest, $overwrite))
			return static::delete($src);
		else
			return false;
	}
}
