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

use ErrorException;

/**
 * Class Log
 *
 * The Log class is a utility for handling and managing error logging within
 * a PHP web framework. It provides methods to log various types of messages
 * (e.g., errors, warnings, debug information) to a designated log file. This
 * helps in tracking and troubleshooting issues that occur during the
 * application's execution.
 *
 * @package System
 */
class Log
{
	protected static $_path = PATH_STORAGE . DS . 'logs' . DS . 'errors.log';

	/**
	 * Log constructor.
	 */
	private function __construct(){}

	/**
	 * Writes a message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return void
	 */
	public static function add(string $message) : void
	{
		$trace = debug_backtrace(0);

		$data = [
			'date' => date('Y-m-d H:i:s'),
			'message' => $message,
			'file' => $trace[0]['file'],
			'line' => $trace[0]['line'],
			'trace' => $trace
		];

		// A resource cannot be encoded, remove it.
		$data = Arr::removeType($data, 'resource');

		// Do not use JSON::encode() to avoid stopping execution due to ErrorException
		// in JSON::encode(), which is unnecessary for logging purposes.
		$content = json_encode($data) . "\n";

		file_put_contents(static::$_path, $content, FILE_APPEND | LOCK_EX);
	}

	/**
	 * Clears the log file.
	 *
	 * @return bool  Returns true if the log file was deleted, false otherwise.
	 */
	public static function clear() : bool
	{
		return File::delete(static::$_path);
	}

	/**
	 * @param  int|null $top
	 * @return array
	 */
	public static function get(int $top = null) : array
	{
		if (!is_file(static::$_path))
			return [];

		$lines = file(static::$_path);
		krsort($lines);

		// Re-index number, starting from 0
		$lines = array_values($lines);

		if (!is_null($top))
			return array_slice($lines, 0, $top);
		else
			return $lines;
	}
}