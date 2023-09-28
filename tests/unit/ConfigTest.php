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
use System\Config;

/**
 * Class ConfigTest
 * @package Tests\Unit
 */
class ConfigTest extends TestCase
{
	protected function tearDown() : void
	{
		Mockery::close();
	}
}
