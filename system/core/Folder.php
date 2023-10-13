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

use stdClass;
use RuntimeException;

/**
 * Class Folder
 *
 * The Folder class provides utility methods for working with folders or
 * directories in a PHP web framework. It encapsulates various operations
 * such as creating, deleting, renaming, copying, and retrieving information
 * about folders. This class helps in managing and organizing the folder
 * structure of a web application, allowing for seamless handling of
 * directories and their contents.
 *
 * @package System
 */
class Folder
{
	/**
	 * Gets the directory separator for the specified path.
	 * 
	 * @param  string $path  The path to get the directory separator for.
	 * @return string
	 */
	public static function getSeparator(string $path) : string
	{
		if (strpos($path, '\\'))
			$ds = '\\';
		else
			$ds = '/';

		return $ds;
	}

	/**
	 * Creates a folder.
	 *
	 * @param  string $path                   The path of the folder to create.
	 * @param  int    $mode                   The mode to use when creating the folder. Defaults to 0755.
	 * @param  bool   $createDefaultHtmlFile  Whether to create a default index.html file in the folder. Defaults to true.
	 * @return bool                           Returns true if the folder was created successfully, false otherwise.
	 */
	public static function create(string $path, int $mode = 0755, bool $createDefaultHtmlFile = true) : bool
	{
		if (static::exists($path))
			return true;

		if (@mkdir($path, $mode, true))
		{
			if ($createDefaultHtmlFile)
			{
				$path = str_replace(PATH_BASE . '/', '', $path);
				$subFolders = explode(DS, $path);
				$subFolderPath = '';

				foreach ($subFolders as $subFolder)
				{
					$subFolderPath .= $subFolder . '/';
					$file = $subFolderPath . 'index.html';
					$content = '<html lang="en"><body></body></html>';

					if (!is_file($file))
						File::write($file, $content);
				}
			}

			return true;
		}
		else
			return false;
	}

	/**
	 * Determines whether a folder exists.
	 *
	 * @param  string $path  The path of the folder to check.
	 * @return bool          Returns true if the folder exists, false otherwise.
	 */
	public static function exists(string $path) : bool
	{
		if ($path === '.' or $path === '..')
			return false;
		else
			return is_dir($path);
	}

	/**
	 * Counts the number of items in a folder and its subfolders.
	 *
	 * @param  string $path  The path of the folder to count the items of.
	 * @return int           Returns the number of items in the folder.
	 */
	public static function countItems(string $path) : int
	{
		$path = rtrim($path, '/');
		$path = rtrim($path, '\\');

		$folders = static::listFolders($path);
		$files = static::listFiles($path);

		$count = count($folders) + count($files);

		foreach ($folders as $folder)
			$count += static::countItems($path . '/' . $folder->name);

		return $count;
	}

	/**
	 * Lists the items in a folder.
	 *
	 * @param  string $path  The path of the folder to list the items of.
	 * @return array         Returns an array of items in the folder.
	 */
	public static function listItems(string $path) : array
	{
		$folders = static::listFolders($path);
		$files = static::listFiles($path);

		for ($i = 0, $n = count($folders); $i < $n; ++$i)
		{
			$folders[$i] = Arr::fromObject($folders[$i]);
			$folders[$i] = Arr::insert($folders[$i], 'folder', 'type');
			$folders[$i] = Arr::toObject($folders[$i]);
		}

		for ($i = 0, $n = count($files); $i < $n; ++$i)
		{
			$files[$i] = Arr::fromObject($files[$i]);
			$files[$i] = Arr::insert($files[$i], 'file', 'type');
			$files[$i] = Arr::toObject($files[$i]);
		}

		$items = array_merge($folders, $files);

		return $items;
	}

	/**
	 * Lists the folders in a folder.
	 *
	 * @param  string $path  The path of the folder to list the folders of.
	 * @return array         Returns an array of the folder structure.
	 */
	public static function listFolders(string $path) : array
	{
		$path = rtrim($path, '/');

		if (!static::exists($path))
			throw new RuntimeException('The source folder could not be found at the specified path: ' . $path);

		$fp = @opendir($path);

		if (!$fp)
			throw new RuntimeException('Unable to open the source folder at the specified path: ' . $path);

		$folders = [];

		while (($entry = readdir($fp)) !== false)
		{
			$entryPath = $path . '/' . $entry;

			if ($entry === '.' or $entry === '..' or filetype($entryPath) === 'file')
				continue;

			$data = new stdClass();
			$data->name = $entry;
			$data->size = static::countItems($entryPath);
			$data->created = filectime($entryPath);
			$data->modified = filemtime($entryPath);
			$data->permissions = File::getPermission($entryPath);
			$data->owner = File::getOwner($entryPath);

			$folders[] = $data;
		}

		$folders = Arr::sortRecordset($folders, 'name');

		return $folders;
	}

	/**
	 * Lists the files in a folder.
	 *
	 * @param  string $path  The path of the folder to list the files of.
	 * @return array         Returns an array of the files in the folder.
	 */
	public static function listFiles(string $path) : array
	{
		$path = rtrim($path, '/');

		if (!static::exists($path))
			throw new RuntimeException('The source folder could not be found at the specified path: ' . $path);

		$fp = @opendir($path);

		if (!$fp)
			throw new RuntimeException('Unable to open the source folder at the specified path: ' . $path);

		$files = [];

		while (($entry = readdir($fp)) !== false)
		{
			$entryPath = $path . '/' . $entry;

			if ($entry === '.' or $entry === '..' or filetype($entryPath) === 'dir')
				continue;

			$data = new stdClass();
			$data->name = $entry;
			$data->size = filesize($entryPath);
			$data->created = filectime($entryPath);
			$data->modified = filemtime($entryPath);
			$data->permissions = File::getPermission($entryPath);
			$data->owner = File::getOwner($entryPath);

			$files[] = $data;
		}

		$files = Arr::sortRecordset($files, 'name');

		return $files;
	}

	/**
	 * Gets the size of a folder.
	 *
	 * @param  string $path  The path of the folder to get the size of.
	 * @return int           Returns the size of the folder in bytes.
	 */
	public static function getSize(string $path) : int
	{
		$path = rtrim($path, '/');

		if (!static::exists($path))
			throw new RuntimeException('The source folder could not be found at the specified path: ' . $path);

		$fp = @opendir($path);

		if (!$fp)
			throw new RuntimeException('Unable to open the source folder at the specified path: ' . $path);

		$size = 0;

		while (($entry = readdir($fp)) !== false)
		{
			if ($entry === '.' or $entry === '..')
				continue;

			$entryPath = $path . '/' . $entry;

			// A subdirectory occupies 4096 bytes of space, even when it's empty.
			$size += filesize($entryPath);

			if (filetype($entryPath) === 'dir')
				$size += static::getSize($entryPath);
		}

		return $size;
	}

	/**
	 * @param  string $path
	 * @return bool
	 */
	public static function delete(string $path) : bool
	{
		@set_time_limit((int)ini_get('max_execution_time'));

		$path = rtrim($path, static::getSeparator($path));

		if (!static::exists($path))
			return false;

		$result = true;
		$fp = @opendir($path);

		if ($fp)
		{
			while (($entry = readdir($fp)) !== false)
			{
				if ($entry === '.' or $entry === '..')
					continue;

				$entryPath = $path . '/' . $entry;

				switch (filetype($entryPath))
				{
					case 'dir':
						$result = static::delete($entryPath);
						break;
					case 'file':
						$result = File::delete($entryPath);
						break;
				}

				// Stop to save time.
				if (!$result)
					break;
			}

			closedir($fp);

			if (!@rmdir($path))
			{
				$error = Error::getLast();
				Log::add($error . 'Delete folder failed: ' . $path);

				$result = false;
			}
		}
		else
			throw new \RuntimeException('Cannot open source folder: ' . $path);

		return $result;
	}

	/**
	 * @param  string $path
	 * @return bool
	 */
	public static function isEmpty(string $path) : bool
	{
		$path = rtrim($path, '/');

		if (!static::exists($path))
			throw new \RuntimeException('Source folder not found: ' . $path);

		$fp = @opendir($path);

		if ($fp)
		{
			while (($entry = readdir($fp)) !== false)
			{
				if ($entry === '.' or $entry === '..')
					continue;

				if (strtolower($entry) === 'index.html')
				{
					$content = File::read($path . '/' . $entry);

					if (trim($content) !== '<html lang="en"><body></body></html>')
						return false;
				}
				else
					return false;
			}

			closedir($fp);
		}
		else
			throw new \RuntimeException('Cannot open source folder: ' . $path);

		return true;
	}

	/**
	 * @param  string $src
	 * @param  string $dest
	 * @param  bool   $merge
	 * @param  bool   $overwrite
	 * @return bool
	 */
	public static function copy(string $src, string $dest, bool $merge = false, bool $overwrite = false) : bool
	{
		$src = rtrim($src, static::getSeparator($src));
		$dest = rtrim($dest, static::getSeparator($dest));

		if (!static::exists($src))
			throw new \RuntimeException('Source folder not found: ' . $src);

		$fp = @opendir($src);

		if ($fp)
		{
			while (($entry = readdir($fp)) !== false)
			{
				if ($entry === '.' or $entry === '..')
					continue;

				$srcPath = $src . '/' . $entry;
				$destPath = $dest . '/' . $entry;

				switch (filetype($srcPath))
				{
					case 'dir':

						if (static::exists($destPath) and !$merge)
							throw new \RuntimeException('Destination folder already exists: ' . $destPath);

						if (!static::create($destPath))
							throw new \RuntimeException('Cannot create destination folder: ' . $destPath);

						static::copy($srcPath, $destPath);

						break;

					case 'file':

						$filename = strtolower(File::getName($destPath));

						if ($filename !== 'index.html')
						{
							if (is_file($destPath) and !$overwrite)
								throw new \RuntimeException('Destination file already exists: ' . $destPath);
							elseif (!@copy($srcPath, $destPath))
								throw new \RuntimeException('Copy file failed: ' . $destPath);
						}

						break;
				}
			}

			closedir($fp);
		}
		else
			throw new \RuntimeException('Cannot open source folder: ' . $src);

		return true;
	}

	/**
	 * @param  string $src
	 * @param  string $dest
	 * @param  bool   $merge
	 * @param  bool   $overwrite
	 * @return bool
	 */
	public static function move(string $src, string $dest, bool $merge = false, bool $overwrite = false) : bool
	{
		if (static::copy($src, $dest, $merge, $overwrite))
			return static::delete($src);
		else
			return false;
	}

	/**
	 * @param  string $path
	 * @return array
	 */
	public static function getInfo(string $path) : array
	{
		return File::getInfo($path);
	}

	/**
	 * @param  string $path
	 * @return bool
	 */
	public static function isWritable(string $path) : bool
	{
		return File::isWritable($path);
	}
}
