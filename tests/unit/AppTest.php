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
use System\App;

/**
 * Class AppTest
 * @package Tests\Unit
 */
class AppTest extends TestCase
{
	// App::isSpa()

	public function testMethodIsSpaCase1() : void
	{
		$result = App::isSpa();

		$this->assertFalse($result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsSpaCase2() : void
	{
		putenv('APP_SIDE=frontend');
		putenv('APP_FRONTEND_SPA_MODE=1');

		$result = App::isSpa();

		$this->assertTrue($result);

		putenv('APP_SIDE');
		putenv('APP_FRONTEND_SPA_MODE');
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsSpaCase3() : void
	{
		putenv('APP_SIDE=backend');
		putenv('APP_BACKEND_SPA_MODE=1');

		$result = App::isSpa();

		$this->assertTrue($result);

		putenv('APP_SIDE');
		putenv('APP_BACKEND_SPA_MODE');
	}
}
