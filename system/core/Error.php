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
 * Class Error
 *
 * Error class provides error handling and logging for the Vanda framework.
 *
 * This class includes handlers for exceptions, native PHP errors, and shutdown errors.
 * It provides methods for logging exceptions and retrieving the last error message.
 *
 * @package System
 */
class Error {

	/**
	 * Exception handler
	 *
	 * This will log the exception and output the exception properties.
	 * formatted as html or a 500 response depending on your application config.
	 *
	 * @param  object $e  The uncaught exception. The exception can be an instance of \Error, System\Error,
	 *                    System\Exception\InvalidArgumentException etc. So we type hint it as object.
	 * @return void
	 */
	public static function exception(object $e)
	{
		if (Config::error('log'))
			static::log($e);

		$exceptionType = get_class($e);

		if (strpos($exceptionType, '\\'))
			$exceptionType = substr(strrchr($exceptionType, '\\'), 1);

		if (Config::error('report'))
		{
			// clear output buffer
			while (ob_get_level() > 1)
				ob_end_clean();

			if (Request::isCli())
			{
				Cli::write(PHP_EOL . 'Uncaught Exception', 'light_red');
				Cli::write($e->getMessage() . PHP_EOL);

				Cli::write('Origin', 'light_red');
				Cli::write(substr($e->getFile(), strlen(PATH)) . ' on line ' . $e->getLine() . PHP_EOL);

				Cli::write('Trace', 'light_red');
				Cli::write($e->getTraceAsString() . PHP_EOL);
			}
			else
			{
				$message = $e->getMessage();

				if ($exceptionType === 'InvalidArgumentException' )
				{
					$trace = $e->getTraceAsString();
					$lines = explode("\n", $trace);

					$origin = $lines[0];
					$start = strpos($origin, ' ') + 1;
					$end = strpos($origin, '(');
					$length = $end - $start;

					$origin = substr($origin, $start, $length);
					$origin = substr($origin, strlen(PATH_BASE));

					$lineNumber = $lines[0];
					$start = strpos($lineNumber, '(') + 1;
					$end = strpos($lineNumber, ')');
					$length = $end - $start;
					$lineNumber = substr($lineNumber, $start, $length);

					// The first line of the trace is the origin.
					// So, we remove it from the array.
					unset($lines[0]);

					// Re-index the array starting from 0.
					$lines = array_values($lines);

					// Reset the line numbers in the trace starting from 0.
					for ($i = 0, $n = count($lines); $i < $n; ++$i)
					{
						$pos = strpos($lines[$i], ' ');
						$lines[$i] = substr_replace($lines[$i], '#' . $i, 0, $pos);
					}

					$origin .= ' at line ' . $lineNumber;
					$trace = implode("\n", $lines);
				}
				else
				{
					$origin = substr($e->getFile(), strlen(PATH_BASE)) . ' at line ' . $e->getLine();
					$trace = $e->getTraceAsString();
				}

				$errorMsg = '<h1>Exception</h1>'
					. '<p><code>' . $message . '</code></p>'
					. '<h3>Origin</h3>'
					. '<p><code>' . $origin . '</code></p>'
					. '<h3>Trace</h3>'
					. '<pre>' . $trace . '</pre>';

				$path = PATH_THEMES . DS . 'system' . DS . 'error.php';

				if (is_file($path) and is_readable($path))
				{
					ob_start();

					include $path ;

					$content = ob_get_clean();
					$content = str_replace('{{main}}', $errorMsg, $content);
				}
				else
					$content = $errorMsg;

				echo $content;
			}
		}
		else
			static::render();

		exit;
	}

	/**
	 * Error handler
	 *
	 * This will catch the php native error and treat it as a exception
	 * which will provide a full back trace on all errors.
	 *
	 * @param  int    $code     The error code.
	 * @param  string $message  The error message.
	 * @param  string $file     The file the error occurred in.
	 * @param  int    $line     The line the error occurred on.
	 * @return void
	 */
	public static function native(int $code, string $message, string $file, int $line) : void
	{
		if ($code and error_reporting())
			static::exception(new \ErrorException($message, $code, 0, $file, $line));
	}

	/**
	 * Shutdown handler
	 *
	 * This will catch errors that are generated at the shutdown level
	 * of execution.
	 *
	 * @return void
	 */
	public static function shutdown() : void
	{
		$error = error_get_last();

		if ($error)
		{
			/** @var string $message */
			/** @var string $type */
			/** @var string $file */
			/** @var int    $line */
			extract($error);

			static::exception(new \ErrorException($message, $type, 0, $file, $line));
		}
	}

	/**
	 * Exception logger
	 *
	 * Log the exception at the error log level.
	 *
	 * @param  object $e  The exception to log. The exception can be an instance of \Error, System\Error,
	 *                    System\Exception\InvalidArgumentException etc. So we type hint it as object.
	 * @return void
	 */
	public static function log(object $e) : void
	{
		$data = [
			'date' => date('Y-m-d H:i:s'),
			'url' => Url::full(),
			'postVars' => $_POST,
			'getVars' => $_GET,
			'sessionVars' => $_SESSION,
			'cookieVars' => $_COOKIE,
			'env' => $_ENV,
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'trace' => $e->getTrace()
		];

		Logger::debug($e->getMessage(), $data);
	}

	/**
	 * Get the last error message.
	 *
	 * @return string|null  Returns the last error message if it exists.
	 */
	public static function getLast() : string
	{
		$error = error_get_last();
		$error = $error['message'];

		if ($error)
			$error .= '.';

		return $error;
	}

	/**
	 * If Config::error('report') is set to false and displaying error details
	 * to the client is disabled, simply render a 500 Internal Server Error view.
	 *
	 * @return void
	 */
	protected static function render()
	{
		$path = PATH_THEMES . DS . 'system' . DS . '500.php';

		if (is_file($path) and is_readable($path))
		{
			ob_start();

			include $path;

			$content = ob_get_clean();

			echo $content;
		}
		else
			die('500 Internal Server Error');
	}
}
