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

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use System\Hash;

/**
 * Class HashTest
 * @package Tests\Unit
 */
class HashTest extends TestCase
{
	// Hash::make() & Hash::varify()

	public function testMethodMakeCase1() : void
	{
		$hash = Hash::make('password');
		$result = Hash::verify('password', $hash);

		$this->assertTrue($result);
	}
}
