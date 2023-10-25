<?php
/*
 * __      __             _
 * \ \    / /            | |
 *  \ \  / /_ _ _ __   __| | __ _
 *   \ \/ / _` | '_ \ / _` |/ _` |
 *    \  / (_| | | | | (_| | (_| |
 *     \/ \__,_|_| |_|\__,_|\__,_|
 *
 * Vanda
 *
 * A lightweight & flexible PHP web framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace System;
use RuntimeException;

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
	 * @codeCoverageIgnore
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
			$resizePath = dirname($file) . DS . 'resize' . DS;

			$fp = @opendir($resizePath);
			$entries = @scandir($resizePath);

			if ($entries)
			{
				foreach ($entries as $entry)
				{
					if ($entry === '.' or $entry === '..' or !is_dir($resizePath . DS . $entry))
						continue;

					if (@unlink($resizePath . DS . $entry . DS . $fileName))
					{
						if (Folder::isEmpty($resizePath . DS . $entry))
							Folder::delete($resizePath . DS . $entry);
					}
				}

				if (Folder::isEmpty($resizePath))
					Folder::delete($resizePath);
			}

			return true;
		}
		else
		{
			$error = Error::getLast();
			Logger::debug($error . ' Failed to delete file: ' . $file);

			return false;
		}
	}

	/**
	 * Determines if the file is an image.
	 *
	 * @param  string $file  The file to check.
	 * @return bool          Returns true if the given file is an image. False otherwise.
	 * @codeCoverageIgnore
	 */
	public static function isImage(string $file) : bool
	{
		return @exif_imagetype($file) > 0;
	}

	/**
	 * Gets the path of an asset file.
	 *
	 * @param  string $filename        The file name.
	 * @param  string $folder          The folder name of the asset file.
	 * @param  string $calledFromPath  The path of the file that called this method.
	 * @return string                  Returns the path of the asset file.
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

				case 'systemPackageAssetFolder':
					$path = PATH_PACKAGE_SYSTEM . DS . PACKAGE . DS . 'assets' . DS . $folder . DS . $filename;
					break;

				case 'systemPackageAssetRootFolder':
					$path = PATH_PACKAGE_SYSTEM . DS . PACKAGE . DS . 'assets' . DS . $filename;
					break;

				case 'appPackageAssetFolder':
					$path = PATH_PACKAGE . DS . PACKAGE . DS . 'assets' . DS . $folder . DS . $filename;
					break;

				case 'appPackageAssetRootFolder':
					$path = PATH_PACKAGE . DS . PACKAGE . DS . 'assets' . DS . $filename;
					break;

				case 'systemAssetFolder':
					$path = PATH_ASSET_SYSTEM . DS . $folder . DS . $filename;
					break;

				case 'systemAssetRootFolder':
					$path = PATH_ASSET_SYSTEM . DS . $filename;
					break;

				case 'appAssetFolder':
					$path = PATH_ASSET . DS . $folder . DS . $filename;
					break;

				// appAssetRootFolder
				default:
					$path = PATH_ASSET . DS . $filename;
			}

			$path = substr_replace($path, '', 0, strlen(PATH_BASE . DS));

			if (is_file($path))
				return $path;
		}

		return $filename;
	}

	/**
	 * Gets the possible paths for an asset file.
	 *
	 * @param  string $calledFromPath  The path of the file that called this method.
	 * @return array                   Returns an array of possible paths.
	 */
	protected static function _getPossibleAssetPaths(string $calledFromPath) : array
	{
		$appPackagePath = PATH_BASE . DS . 'packages' . DS . PACKAGE;
		$systemPackagePath = PATH_BASE . DS . 'system' . DS . 'packages' . DS . PACKAGE;

		if (stripos($calledFromPath, PATH_THEME) !== false)
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
		elseif (stripos($calledFromPath, $appPackagePath) !== false)
		{
			$possibleAssetPaths = [
				'appPackageAssetFolder',
				'appPackageAssetRootFolder',

				'systemPackageAssetFolder',
				'systemPackageAssetRootFolder',

				'appAssetFolder',
				'appAssetRootFolder',

				'systemAssetFolder',
				'systemAssetRootFolder'
			];
		}
		elseif (stripos($calledFromPath, $systemPackagePath) !== false)
		{
			$possibleAssetPaths = [
				'systemPackageAssetFolder',
				'systemPackageAssetRootFolder',

				'systemAssetFolder',
				'systemAssetRootFolder',

				'appAssetFolder',
				'appAssetRootFolder'
			];
		}
		// e.g. call from /system/MVC/View.php
		elseif (stripos($calledFromPath, PATH_BASE . DS . 'system' . DS) !== false)
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
	 * Gets the exact path of a file.
	 *
	 * @param  array        $paths  An array of possible paths.
	 * @return string|false         Returns the exact path of the file. Returns false if the file does not exist.
	 */
	public static function getExactPath(array $paths)
	{
		foreach ($paths as $path)
		{
			if (is_file($path))
				return $path;
		}

		return false;
	}

	/**
	 * Stream a file to the browser.
	 *
	 * @param  string      $file     The file to stream.
	 * @param  string|null $data     Optionally, the data to stream. Defaults to null.
	 * @param  bool        $setMime  Whether to set the mime type. Defaults to true.
	 * @return void
	 * @codeCoverageIgnore
	 */
	public static function stream(string $file, ?string $data = null, bool $setMime = true) : void
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
	 * Gets the mime type of the file.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getMime('picture.jpg');
	 * // The $result will be: 'image/jpeg'
	 * ```
	 *
	 * @param  string       $file  The file to get the mime type of.
	 * @return string|false        Returns the mime type of the file. Returns false if the mime type cannot be
	 *                             determined.
	 */
	public static function getMime(string $file)
	{
		if (is_file($file))
			return mime_content_type($file);
		else
			return false;
	}

	/**
	 * Gets the mime type of the file by its extension.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getMimeByExtension('picture.jpg');
	 * // The $result will be: 'image/jpeg'
	 * ```
	 *
	 * @param  string       $file  The file to get the mime type of.
	 * @return string|false        Returns the mime type of the file. Returns false if the mime type cannot be
	 *                             determined.
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
	 * Gets the file info.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getInfo('picture.jpg');
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [name] => picture.php
	 * //     [path] => /path/picture.php
	 * //     [size] => 60317
	 * //     [date] => 1656838930
	 * //     [readable] => 1
	 * //     [writable] => 1
	 * //     [executable] => 1
	 * //     [fileperms] => 33279
	 * // )
	 * ```
	 *
	 * @param  string $file  The file to get the info of.
	 * @return array         Returns an array of file info.
	 * @codeCoverageIgnore
	 */
	public static function getInfo(string $file) : array
	{
		if (!static::exists($file))
			throw new RuntimeException('File not found: ' . $file);

		$info['name'] = basename($file);
		$info['path'] = $file;
		$info['size'] = @filesize($file);
		$info['date'] = @filemtime($file);
		$info['readable'] = is_readable($file);
		$info['writable'] = static::isWritable($file);
		$info['executable'] = is_executable($file);
		$info['fileperms'] = @fileperms($file);

		return $info;
	}

	/**
	 * Reads entire file into a string.
	 *
	 * This method is an alias of `file_get_contents()`.
	 *
	 * @param  string       $file  The file to read.
	 * @return string|false        Returns the read data or false on failure.
	 * @codeCoverageIgnore
	 */
	public static function read(string $file)
	{
		return @file_get_contents($file);
	}

	/**
	 * Writes a string to a file.
	 *
	 * @param  string $file  The file to write to.
	 * @param  string $data  The data to write.
	 * @param  string $mode  Optionally, the file open mode. Defaults to 'wb'.
	 * @return bool          Returns true on success or false on failure.
	 */
	public static function write(string $file, string $data, string $mode = 'wb') : bool
	{
		// When you use this function, the script timer is reset to 0; if you set 50 as the time limit,
		// then after 40 seconds set the time limit to 30, the script will run for 70 seconds in total.
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
			$result = fwrite($fp, mb_substr($data, $written));

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
	 * the file, based on the read-only attribute.
	 *
	 * @param  string $file  The file to test.
	 * @return bool          Returns true if the file is writable. False otherwise.
	 * @see https://bugs.php.net/bug.php?id=54709
	 * @codeCoverageIgnore
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
				$file = rtrim($file, '/');
				$file = rtrim($file, '\\');

				$file = $file . DS . md5((string)mt_rand());

				$fp = @fopen($file, 'ab');

				if ($fp === false)
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
	 * Gets the size of a file.
	 *
	 * @param  string      $file       The file to get the size of.
	 * @param  int         $precision  Optionally, the number of decimal places to round to. Defaults to 1.
	 * @param  string|null $unit       Optionally, the unit to use. Possible values are: 'B', 'KB', 'MB', 'GB', 'TB',
	 *                                 'PB', 'EB'. Defaults to null. If null, the unit will be automatically determined.
	 * @return string                  Returns the size of the file.
	 */
	public static function getSize(string $file, int $precision = 1, ?string $unit = null) : string
	{
		$size = @filesize($file);
		$size = Number::byteFormat($size, $precision, $unit);

		return $size;
	}

	/**
	 * Gets the file permissions.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getPermission('picture.jpg');
	 * // The $result will be: '0644'
	 *
	 * $result = File::getPermission('not-exist.jpg');
	 * // The $result will be: '0'
	 * ```
	 *
	 * @param  string $file  The file to get the permissions of.
	 * @return string        Returns the file permissions.
	 * @codeCoverageIgnore
	 */
	public static function getPermission(string $file) : string
	{
		$perms = @fileperms($file);

		return substr(sprintf('%o', $perms), -4);
	}

	/**
	 * Gets the owner of a file.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getOwner('picture.jpg');
	 * // The $result will be: 'username:groupname'
	 * ```
	 *
	 * @param  string $file  The file to get the owner of.
	 * @return string        Returns the owner of the file.
	 * @codeCoverageIgnore
	 */
	public static function getOwner(string $file) : string
	{
		// Get the user and group IDs
		$userId = fileowner($file);
		$groupId = filegroup($file);

		// Get the username and groupname
		$userInfo = posix_getpwuid($userId);
		$groupInfo = posix_getgrgid($groupId);

		// Format the result as 'username:groupname'
		$owner = $userInfo['name'] . ':' . $groupInfo['name'];

		return $owner;
	}

	/**
	 * Copies file from source to destination.
	 *
	 * @param  string $src        The source file.
	 * @param  string $dest       The destination file.
	 * @param  bool   $overwrite  Optionally, whether to overwrite the destination file if it already exists. Defaults
	 *                            to false.
	 * @return bool               Returns true on success or false on failure.
	 */
	public static function copy(string $src, string $dest, bool $overwrite = false) : bool
	{
		if (!is_readable($src))
			throw new RuntimeException('Unable to find or read file: ' . $src);

		if (is_file($dest) and !$overwrite)
			throw new RuntimeException('Destination file already exists: ' . $dest);

		return @copy($src, $dest);
	}

	/**
	 * Moves file from source to destination.
	 *
	 * @param  string $src        The source file.
	 * @param  string $dest       The destination file.
	 * @param  bool   $overwrite  Optionally, whether to overwrite the destination file if it already exists. Defaults
	 *                            to false.
	 * @return bool               Returns true on success or false on failure.
	 */
	public static function move(string $src, string $dest, bool $overwrite = false) : bool
	{
		if (static::copy($src, $dest, $overwrite))
			return static::delete($src);
		else
			return false;
	}
}
