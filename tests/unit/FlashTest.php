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
use System\Flash;

/**
 * Class ConfigTest
 * @package Tests\Unit
 */
class FlashTest extends TestCase
{
	protected function tearDown() : void
	{
		Mockery::close();
	}

	// Flash::info()
	
	public function testMethodInfoCase1() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isAjax')->andReturnTrue();

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('core')->with('closeFlashInfoMessageWrapperClass')->andReturn('dummy-wrapper-class');
		$stubConfig->shouldReceive('core')->with('closeFlashInfoMessageButtonClass')->andReturn('dummy-button-class');

		$expected = '<div class="dummy-wrapper-class">'
				. '<button aria-hidden="true" data-dismiss="alert" type="button" class="dummy-button-class">×</button>'
				. 'test'
				. '</div>';

		$result = Flash::info('test');

		$this->assertEquals($expected, $result);
	}

	public function testMethodInfoCase2() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isAjax')->andReturnFalse();

		$mockedSession = Mockery::mock('alias:\System\Session');
		$mockedSession->shouldReceive('set')->once();

		Flash::info('test');

		// If this test fails, it will stop before returning true below.
		$this->assertTrue(true);
	}

	// Flash::success()

	public function testMethodSuccessCase1() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isAjax')->andReturnTrue();

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('core')->with('closeFlashSuccessMessageWrapperClass')->andReturn('dummy-wrapper-class');
		$stubConfig->shouldReceive('core')->with('closeFlashSuccessMessageButtonClass')->andReturn('dummy-button-class');

		$expected = '<div class="dummy-wrapper-class">'
			. '<button aria-hidden="true" data-dismiss="alert" type="button" class="dummy-button-class">×</button>'
			. 'test'
			. '</div>';

		$result = Flash::success('test');

		$this->assertEquals($expected, $result);
	}

	public function testMethodSuccessCase2() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isAjax')->andReturnFalse();

		$mockedSession = Mockery::mock('alias:\System\Session');
		$mockedSession->shouldReceive('set')->once();

		Flash::success('test');

		// If this test fails, it will stop before returning true below.
		$this->assertTrue(true);
	}

	// Flash::warning()

	public function testMethodWarningCase1() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isAjax')->andReturnTrue();

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('core')->with('closeFlashWarningMessageWrapperClass')->andReturn('dummy-wrapper-class');
		$stubConfig->shouldReceive('core')->with('closeFlashWarningMessageButtonClass')->andReturn('dummy-button-class');

		$expected = '<div class="dummy-wrapper-class">'
			. '<button aria-hidden="true" data-dismiss="alert" type="button" class="dummy-button-class">×</button>'
			. 'test'
			. '</div>';

		$result = Flash::warning('test');

		$this->assertEquals($expected, $result);
	}

	public function testMethodWarningCase2() : void
	{
		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('isAjax')->andReturnFalse();

		$mockedSession = Mockery::mock('alias:\System\Session');
		$mockedSession->shouldReceive('set')->once();

		Flash::warning('test');

		// If this test fails, it will stop before returning true below.
		$this->assertTrue(true);
	}
}
