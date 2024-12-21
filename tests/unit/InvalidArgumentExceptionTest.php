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
use System\Exception\InvalidArgumentException;

/**
 * Class InvalidArgumentExceptionTest
 * @package Tests\Unit
 */
class InvalidArgumentExceptionTest extends TestCase
{
	// InvalidArgumentException::typeError()

	public function testMethodTypeErrorCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string or int');

		$exception = InvalidArgumentException::typeError(1, ['string', 'int'], tmpfile());

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
	}

	public function testMethodTypeErrorCase2() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string or int');

		$exception = InvalidArgumentException::typeError(1, ['string', 'int'], tmpfile(), 'custom message');

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
	}

	// InvalidArgumentException::valueError()

	public function testMethodValueErrorCase1() : void
	{
		$exception = InvalidArgumentException::valueError(1, '$value must be greater than zero', false);

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
	}
}
