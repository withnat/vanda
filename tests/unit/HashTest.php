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
