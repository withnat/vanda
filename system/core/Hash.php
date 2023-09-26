<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2021, Nat Withe. All rights reserved.
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace System;

/**
 * Class Hash
 * @package System
 */
class Hash
{
	/**
	 * The Hash class provides methods for creating and verifying hashes.
	 *
	 * Hash constructor.
	 */
	private function __construct(){}

	/**
	 * Creates a new hash from the given string.
	 *
	 * @param  string      $string  The input string to hash.
	 * @param  int         $rounds  Optional, hashing rounds to apply. Defaults to 10.
	 * @return string|bool          Returns the hashed string, or false on failure.
	 */
	public static function make(string $string, int $rounds = 10)
	{
		return password_hash($string, PASSWORD_DEFAULT, ['cost' => $rounds]);
	}

	/**
	 * Verifies a hash.
	 *
	 * @param  string $value  The input value to verify the hash against.
	 * @param  string $hash   The hash to check.
	 * @return bool           Returns true if the hash is valud, false otherwise.
	 */
	public static function verify(string $value, string $hash) : bool
	{
		return password_verify($value, $hash);
	}
}
