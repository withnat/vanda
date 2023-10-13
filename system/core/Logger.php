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
 * Class Logger
 *
 * The Logger class is a utility for handling and managing error logging within
 * a PHP web framework. It provides methods to log various types of messages
 * (e.g., errors, warnings, debug information) to a designated log file. This
 * helps in tracking and troubleshooting issues that occur during the
 * application's execution.
 *
 * @package System
 */
class Logger
{
	/**
	 * The log levels.
	 *
	 * There are eight different log levels, matching to the RFC 5424 levels, and they are as follows:
	 *
	 * 1. Emergency:
	 *    The system is unusable.
	 *
	 * 2. Alert:
	 *    Action must be taken immediately, like when an entire website is down, the database unavailable, etc.
	 *
	 * 3. Critical:
	 *    Critical conditions, like an application component not available, or an unexpected exception.
	 *
	 * 4. Error:
	 *    Runtime errors that do not require immediate action but should typically be logged and monitored.
	 *
	 * 5. Warning:
	 *    Exceptional occurrences that are not errors, like the use of deprecated APIs,
	 *    poor use of an API, or other undesirable things that are not necessarily wrong.
	 *
	 * 6. Notice:
	 *    Normal, but significant events in your application.
	 *
	 * 7. Informational:
	 *    Interesting events in your application, like a user logging in, logging SQL queries, etc.
	 *
	 * 8. Debug:
	 *    Detailed debug information.
	 *
	 * @var array
	 * @see https://tools.ietf.org/html/rfc5424
	 */
	protected static $_logLevels = [
		1 => 'emergency',
		2 => 'alert',
		3 => 'critical',
		4 => 'error',
		5 => 'warning',
		6 => 'notice',
		7 => 'info',
		8 => 'debug'
	];

	protected static $_path = PATH_STORAGE . DS . 'logs' . DS . 'errors.log';

	/**
	 * Logger constructor.
	 */
	private function __construct(){}

	/**
	 * Writes a debug message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function emergency(string $message) : bool
	{
		return static::log('emergency', $message);
	}

	/**
	 * Writes an alert message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function alert(string $message) : bool
	{
		return static::log('alert', $message);
	}

	/**
	 * Writes a critical message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function critical(string $message) : bool
	{
		return static::log('critical', $message);
	}

	/**
	 * Writes an error message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function error(string $message) : bool
	{
		return static::log('error', $message);
	}

	/**
	 * Writes a warning message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function warning(string $message) : bool
	{
		return static::log('warning', $message);
	}

	/**
	 * Writes a notice message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function notice(string $message) : bool
	{
		return static::log('notice', $message);
	}

	/**
	 * Writes an info message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function info(string $message) : bool
	{
		return static::log('info', $message);
	}

	/**
	 * Writes a debug message to the log file.
	 *
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function debug(string $message) : bool
	{
		return static::log('debug', $message);
	}

	/**
	 * Writes a message to the log file.
	 *
	 * @param  string $level    The log level.
	 * @param  string $message  The message to write.
	 * @return bool             Returns true if the message was written to the log file, false otherwise.
	 */
	public static function log(string $level, string $message) : bool
	{
		$level = trim(strtolower($level));
		$levelId = array_search($level, static::$_logLevels);

		$threshold = Config::log('threshold');

		if ($threshold < $levelId)
			return false;

		$trace = debug_backtrace(0);

		$data = [
			'date' => date('Y-m-d H:i:s'),
			'message' => $message,
			'url' => Url::full(),
			'postVars' => $_POST,
			'getVars' => $_GET,
			'sessionVars' => $_SESSION,
			'cookieVars' => $_COOKIE,
			'env' => $_ENV,
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

		return true;
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
	 * Gets the latest log lines from the log file.
	 *
	 * @param  int|null $top  The number of top log lines to return.
	 * @return array          Returns an array of log lines.
	 */
	public static function get(?int $top = null) : array
	{
		if (!is_file(static::$_path))
			return [];

		$lines = file(static::$_path);
		krsort($lines);

		// Re-index number, starting from 0
		$lines = array_values($lines);

		if (!is_null($top))
			return array_slice($lines, 0, $top);

		return $lines;
	}
}