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
 * A lightweight & flexible PHP CMS framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use System\Struct;

/**
 * Class StructTest
 * @package Tests\Unit
 */
class StructTest extends TestCase
{
	// Struct::factory()

	public function testMethodFactoryCase1() : void
	{
		$coords = Struct::factory('degree', 'minute', 'pole');

		$this->assertInstanceOf('System\Struct', $coords);
	}

	// Struct::create()

	public function testMethodCreateCase1() : void
	{
		$coords = Struct::factory('degree', 'minute', 'pole');
		$lat = $coords->create(35, 40, 'N');
		$lng = $coords->create(139, 45, 'E');

		$this->assertInstanceOf('System\Struct', $lat);
		$this->assertInstanceOf('System\Struct', $lng);
		$this->assertEquals(35, $lat->degree);
		$this->assertEquals(40, $lat->minute);
		$this->assertEquals('N', $lat->pole);
		$this->assertEquals(139, $lng->degree);
		$this->assertEquals(45, $lng->minute);
		$this->assertEquals('E', $lng->pole);
	}
}
