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

use Mockery;
use PHPUnit\Framework\TestCase;
use System\Language;

/**
 * Class ConfigTest
 * @package Tests\Unit
 */
class LanguageTest extends TestCase
{
	protected function tearDown() : void
	{
		Mockery::close();
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testLanguage() : void
	{
		define('SIDE', 'backend');
		define('MODULE', 'user');

		$stubDB = Mockery::mock('alias:\System\DB');

		$stubDB->shouldAllowMockingProtectedMethods()->makePartial();
		$stubDB->shouldReceive('select')->with('code')->andReturn($stubDB);
		$stubDB->shouldReceive('from')->with('Language')->andReturn($stubDB);
		$stubDB->shouldReceive('where')->with('default', 1)->andReturn($stubDB);
		$stubDB->shouldReceive('loadSingle')->andReturn('en');

		$stubDB->shouldReceive('select')->with('direction')->andReturn($stubDB);
		$stubDB->shouldReceive('from')->with('Language')->andReturn($stubDB);
		$stubDB->shouldReceive('where')->with('default', 1)->andReturn($stubDB);
		$stubDB->shouldReceive('loadSingle')->andReturn('ltr');

		$result = Language::_('test');

		$this->assertEquals('test', $result);
	}
}
