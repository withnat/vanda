<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license     MIT
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace System\Exception;

use System\Inflector;

/**
 * Class InvalidArgumentException
 * @package System\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException
{
	/**
	 * @param  int         $argument
	 * @param  array       $allowedDataTypes
	 * @param  mixed       $value
	 * @param  string|null $customMsg
	 * @return InvalidArgumentException
	 */
	public static function create(int $argument, array $allowedDataTypes = null, $value = null, string $customMsg = null) : InvalidArgumentException
	{
		$trace = debug_backtrace(0);

		if (strpos($trace[0]['file'], 'Data.php'))
			$index = 2;
		else
			$index = 1;

		$class = $trace[$index]['class'];
		$function = $trace[$index]['function'];
		$file = $trace[$index]['file'];
		$line = $trace[$index]['line'];

		if ($customMsg)
		{
			$msg = 'Argument ' . $argument . ' passed to ' . $class . '::' . $function . '() ' . $customMsg
				. ', called in ' . $file . ' on line ' . $line;
		}
		else
		{
			$given = gettype($value);
			$types = Inflector::sentence($allowedDataTypes, ' or ');

			$msg = 'Argument ' . $argument . ' passed to ' . $class . '::' . $function . '()'
				. ' must be of the type ' . $types . ', ' . $given . ' given, '
				. ' called in ' . $file . ' on line ' . $line;
		}

		return new self($msg);
	}
}