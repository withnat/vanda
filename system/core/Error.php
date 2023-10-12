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
 * Class Error
 * @package System
 */
class Error {

	/**
	 * Exception handler
	 *
	 * This will log the exception and output the exception properties.
	 * formatted as html or a 500 response depending on your application config.
	 *
	 * @param  ErrorException  The uncaught exception
	 * @return void
	 * @throws ErrorException
	 */
	public static function exception($e)
	{
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

				/*
				if ($exceptionType === 'InvalidArgumentException' and 1==2)
				{
					$trace = $e->getTraceAsString();
					$lines = explode("\n", $trace);

					$origin = $lines[0];
					$start = strpos($origin, ' ') + 1;
					$end = strpos($origin, '(');
					$length = $end - $start;

					$origin = substr($origin, $start, $length);
					$origin = substr($origin, strlen(PATH_BASE));

					$line = $lines[0];
					$start = strpos($line, '(') + 1;
					$end = strpos($line, ')');
					$length = $end - $start;
					$line = substr($line, $start, $length);

					$lines = Arr::removeKey($lines, '0');
					$lines = Arr::toNumericIndex($lines);

					for ($i = 0, $n = count($lines); $i < $n; ++$i)
					{
						$pos = strpos($lines[$i], ' ');
						$lines[$i] = substr_replace($lines[$i], '#' . $i, 0, $pos);
					}

					$origin .= ' at line ' . $line;
					$trace = implode("\n", $lines);
				}
				else
				{
				*/
					$origin = substr($e->getFile(), strlen(PATH_BASE)) . ' at line ' . $e->getLine();
					$trace = $e->getTraceAsString();
				//}

				$errorMsg = '<h1>Exception</h1>
							<p><code>' . $message . '</code></p>
							<h3>Origin</h3>
							<p><code>' . $origin . '</code></p>
							<h3>Trace</h3>
							<pre>' . $trace . '</pre>';

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
		{
			//Response::error(500, ['exception' => $e])->send();
		}

		exit;
	}

	/**
	 * Error handler
	 *
	 * This will catch the php native error and treat it as a exception
	 * which will provide a full back trace on all errors.
	 *
	 * @param  int
	 * @param  string $message
	 * @param  string $file
	 * @param  int    $line
	 * @return void
	 * @throws ErrorException
	 */
	public static function native($code, $message, $file, $line)
	{
		if ($code and error_reporting())
			static::exception(new ErrorException($message, $code, 0, $file, $line));
	}

	/**
	 * Shutdown handler
	 *
	 * This will catch errors that are generated at the
	 * shutdown level of execution.
	 *
	 * @return void
	 * @throws ErrorException
	 */
	public static function shutdown()
	{
		$error = error_get_last();

		if ($error)
		{
			/** @var string $message */
			/** @var string $type */
			/** @var string $file */
			/** @var int    $line */
			extract($error);

			static::exception(new ErrorException($message, $type, 0, $file, $line));
		}
	}

	/**
	 * Exception logger
	 *
	 * Log the exception depending on the application config.
	 *
	 * @param  object $e  The exception
	 * @return void
	 */
	public static function log($e)
	{
		$logger = Config::error('log');

		if (is_callable($logger))
			call_user_func($logger, $e);
	}

	/**
	 * @return string|null
	 */
	public static function getLast()
	{
		$error = error_get_last();
		$error = $error['message'];

		if ($error)
			$error .= '. ';

		return $error;
	}
}
