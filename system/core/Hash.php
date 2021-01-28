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
