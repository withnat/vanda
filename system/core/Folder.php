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
		if (strpos($path, '\\') !== false)
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
				$ds = static::getSeparator($path);
				$path = str_replace(PATH_BASE . $ds, '', $path);
				$subFolders = explode($ds, $path);
				$subFolderPath = '';

				foreach ($subFolders as $subFolder)
				{
					$subFolderPath .= $subFolder . DS;
					$file = $subFolderPath . 'index.html';

					if (!is_file($file))
					{
						$content = '<html lang="en"><body></body></html>';
						File::write($file, $content);
					}
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
		$path = rtrim($path, static::getSeparator($path));

		$folders = static::listFolders($path);
		$files = static::listFiles($path);

		$count = count($folders) + count($files);

		foreach ($folders as $folder)
			$count += static::countItems($path . DS . $folder->name);

		return $count;
	}

	/**
	 * Lists the items in a folder.
	 *
	 * @param  string $path  The path of the folder to list the items of.
	 * @return array         Returns an array of items in the folder.
	 * @codeCoverageIgnore
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
		$path = rtrim($path, static::getSeparator($path));

		if (!static::exists($path))
			throw new RuntimeException('Source folder not found: ' . $path);

		$entries = @scandir($path);

		if (!$entries)
			throw new RuntimeException('Cannot open source folder: ' . $path);

		$folders = [];

		foreach ($entries as $entry)
		{
			$entryPath = $path . DS . $entry;

			if ($entry === '.' or $entry === '..' or filetype($entryPath) === 'file')
				continue;

			$data = new stdClass();
			$data->name = $entry;
			$data->size = static::countItems($entryPath);
			$data->created = filectime($entryPath);
			$data->modified = filemtime($entryPath);
			$data->permission = File::getPermission($entryPath);
			$data->owner = File::getOwner($entryPath);

			$folders[] = $data;
		}

		if (!empty($folders))
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
		$path = rtrim($path, static::getSeparator($path));

		if (!static::exists($path))
			throw new RuntimeException('Source folder not found: ' . $path);

		$entries = @scandir($path);

		if (!$entries)
			throw new RuntimeException('Cannot open source folder: ' . $path);

		$files = [];

		foreach ($entries as $entry)
		{
			$entryPath = $path . DS . $entry;

			if ($entry === '.' or $entry === '..' or filetype($entryPath) === 'dir')
				continue;

			$data = new stdClass();
			$data->name = $entry;
			$data->size = filesize($entryPath);
			$data->created = filectime($entryPath);
			$data->modified = filemtime($entryPath);
			$data->permission = File::getPermission($entryPath);
			$data->owner = File::getOwner($entryPath);

			$files[] = $data;
		}

		if (!empty($files))
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
		$path = rtrim($path, static::getSeparator($path));

		if (!static::exists($path))
			throw new RuntimeException('Source folder not found: ' . $path);

		$entries = @scandir($path);

		if (!$entries)
			throw new RuntimeException('Cannot open source folder: ' . $path);

		$size = 0;

		foreach ($entries as $entry)
		{
			if ($entry === '.' or $entry === '..')
				continue;

			$entryPath = $path . DS . $entry;

			// A subdirectory occupies 4096 bytes of space, even when it's empty.
			$size += filesize($entryPath);

			if (filetype($entryPath) === 'dir')
				$size += static::getSize($entryPath);
		}

		return $size;
	}

	/**
	 * Gets the folder permissions.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = File::getPermission('/path/to/');
	 * // The $result will be: '0644'
	 *
	 * $result = File::getPermission('not-exist-path');
	 * // The $result will be: '0'
	 * ```
	 *
	 * @param  string $file  The folder to get the permissions of.
	 * @return string        Returns the folder permissions.
	 * @codeCoverageIgnore
	 */
	public static function getPermission(string $file) : string
	{
		return File::getPermission($file);
	}

	/**
	 * Deletes a folder.
	 *
	 * @param  string $path  The path of the folder to delete.
	 * @return bool          Returns true if the folder was deleted successfully, false otherwise.
	 */
	public static function delete(string $path) : bool
	{
		// When you use this function, the script timer is reset to 0; if you set 50 as the time limit,
		// then after 40 seconds set the time limit to 30, the script will run for 70 seconds in total.
		@set_time_limit((int)ini_get('max_execution_time'));

		$path = rtrim($path, static::getSeparator($path));

		if (!static::exists($path))
			return false;

		$entries = @scandir($path);

		if (!$entries)
			throw new RuntimeException('Cannot open source folder: ' . $path);

		foreach ($entries as $entry)
		{
			if ($entry === '.' or $entry === '..')
				continue;

			$entryPath = $path . DS . $entry;

			switch (filetype($entryPath))
			{
				case 'dir':
					static::delete($entryPath);
					break;

				case 'file':
					File::delete($entryPath);
					break;
			}
		}

		if (!@rmdir($path))
		{
			$error = Error::getLast();
			Logger::debug($error . ' Failed to delete folder: ' . $path);

			return false;
		}

		return true;
	}

	/**
	 * Determines whether a folder is empty.
	 *
	 * @param  string $path  The path of the folder to check.
	 * @return bool          Returns true if the folder is empty, false otherwise.
	 */
	public static function isEmpty(string $path) : bool
	{
		$path = rtrim($path, static::getSeparator($path));

		if (!static::exists($path))
			throw new RuntimeException('Source folder not found: ' . $path);

		$entries = @scandir($path);

		if (!$entries)
			throw new RuntimeException('Cannot open source folder: ' . $path);

		foreach ($entries as $entry)
		{
			if ($entry === '.' or $entry === '..')
				continue;

			if (strtolower($entry) === 'index.html')
			{
				$content = File::read($path . DS . $entry);

				if (trim($content) !== '<html lang="en"><body></body></html>')
					return false;
			}
			else
				return false;
		}

		return true;
	}

	/**
	 * Copies a folder.
	 *
	 * @param  string $src        The path of the source folder.
	 * @param  string $dest       The path of the destination folder.
	 * @param  bool   $merge      Whether to merge the contents of the source folder with the destination folder. Defaults to false.
	 * @param  bool   $overwrite  Whether to overwrite existing files when copying. Defaults to false.
	 * @return bool               Returns true if the folder was copied successfully, false otherwise.
	 */
	public static function copy(string $src, string $dest, bool $merge = false, bool $overwrite = false) : bool
	{
		$src = rtrim($src, static::getSeparator($src));
		$dest = rtrim($dest, static::getSeparator($dest));

		if (!static::exists($src))
			throw new RuntimeException('Source folder not found: ' . $src);

		if (static::exists($dest) and !$merge)
			throw new RuntimeException('Destination folder already exists: ' . $dest);

		$entries = @scandir($src);

		if (!$entries)
			throw new RuntimeException('Cannot open source folder: ' . $src);

		// Make the destination directory if not exist
		$permission = static::getPermission($src);// Convert from string to number with leading zero in octal number.
		$permission = octdec($permission);
		static::create($dest, $permission, false);

		foreach ($entries as $entry)
		{
			if ($entry === '.' or $entry === '..')
				continue;

			$srcPath = $src . DS . $entry;
			$destPath = $dest . DS . $entry;

			switch (filetype($srcPath))
			{
				case 'dir':

					static::copy($srcPath, $destPath, $merge, $overwrite);

					break;

				case 'file':

					if (is_file($destPath) and !$overwrite)
						throw new RuntimeException('Destination file already exists: ' . $destPath);

					// @codeCoverageIgnoreStart
					elseif (!@copy($srcPath, $destPath))
						throw new RuntimeException('Failed to copy file: ' . $destPath);
					// @codeCoverageIgnoreEnd

					break;
			}
		}

		return true;
	}

	/**
	 * Moves a folder.
	 *
	 * @param  string $src        The path of the source folder.
	 * @param  string $dest       The path of the destination folder.
	 * @param  bool   $merge      Whether to merge the contents of the source folder with the destination folder. Defaults to false.
	 * @param  bool   $overwrite  Whether to overwrite existing files when moving. Defaults to false.
	 * @return bool               Returns true if the folder was moved successfully, false otherwise.
	 */
	public static function move(string $src, string $dest, bool $merge = false, bool $overwrite = false) : bool
	{
		if (static::copy($src, $dest, $merge, $overwrite))
			return static::delete($src);

		return false;
	}

	/**
	 * Gets the folder info.
	 *
	 *  For example,
	 *
	 *  ```php
	 *  $result = Folder::getInfo('/path/to');
	 *  // The $result will be:
	 *  // Array
	 *  // (
	 *  //     [name] => to
	 *  //     [path] => /path/to
	 *  //     [size] => 4096
	 *  //     [date] => 1656838930
	 *  //     [readable] => 1
	 *  //     [writable] => 1
	 *  //     [executable] => 1
	 *  //     [fileperms] => 16895
	 *  // )
	 * ```
	 *
	 * @param  string $path  The path of the folder to get the info of.
	 * @return array         Returns an array of the folder info.
	 * @codeCoverageIgnore
	 */
	public static function getInfo(string $path) : array
	{
		if (!static::exists($path))
			throw new RuntimeException('Folder not found: ' . $path);

		$info['name'] = basename($path);
		$info['path'] = $path;
		$info['size'] = @filesize($path);
		$info['date'] = @filemtime($path);
		$info['readable'] = is_readable($path);
		$info['writable'] = static::isWritable($path);
		$info['executable'] = is_executable($path);
		$info['fileperms'] = @fileperms($path);

		return $info;
	}

	/**
	 * Tests for folder writability.
	 *
	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the folder, based on the read-only attribute.
	 *
	 * @param  string $path  The path to test.
	 * @return bool          Returns true if the folder is writable. False otherwise.
	 * @see https://bugs.php.net/bug.php?id=54709
	 * @codeCoverageIgnore
	 */
	public static function isWritable(string $path) : bool
	{
		return File::isWritable($path);
	}
}
