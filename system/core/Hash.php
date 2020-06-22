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

namespace System;

/**
 * Class Hash
 * @package System
 */
final class Hash
{
	/**
	 * Hash constructor.
	 */
	private function __construct(){}

	/**
	 * Creates a new hash
	 *
	 * @param  string      $value   String to hash.
	 * @param  int         $rounds  Hashing rounds to apply (optional).
	 * @return string|bool          Returns the hashed password, or FALSE on failure.
	 */
	public static function make(string $value, int $rounds = 10)
	{
		return password_hash($value, PASSWORD_DEFAULT, ['cost' => $rounds]);
	}

	/**
	 * Verifies a hash
	 *
	 * @param  string $value  Value to verify the hash against.
	 * @param  string $hash   Hash to check.
	 * @return bool           Whether the hash is valud.
	 */
	public static function verify(string $value, string $hash) : bool
	{
		return password_verify($value, $hash);
	}
}
