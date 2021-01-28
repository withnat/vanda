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

use Mockery;
use PHPUnit\Framework\TestCase;
use System\App;

/**
 * Class AppTest
 * @package Tests\Unit
 */
class AppTest extends TestCase
{
    protected function tearDown() : void
    {
        Mockery::close();
    }

	// App::isSpa()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
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
