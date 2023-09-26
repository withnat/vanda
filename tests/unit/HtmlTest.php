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

use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use System\Html;

/**
 * Class HtmlTest
 * @package Tests\Unit
 */
class HtmlTest extends TestCase
{
	use \phpmock\phpunit\PHPMock;

	protected function tearDown() : void
	{
		Mockery::close();
	}

	// Html::link()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string, array or null');

		$this->expectException(InvalidArgumentException::class);

		Html::link('user', 'User', new stdClass());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase2() : void
	{
		$expected = '<a href="http://localhost">http://localhost</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnTrue();

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$result = Html::link();

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase3() : void
	{
		$expected = '<a style="font-weight:bold;" href="http://localhost/user">Users</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/user');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$result = Html::link('user', 'Users', 'style="font-weight:bold;"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase4() : void
	{
		$expected = '<a style="font-weight:bold;" href="http://localhost/user">Users</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/user');

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toString')->andReturn('style="font-weight:bold;"');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$result = Html::link('user', 'Users', ['style' => 'font-weight:bold;']);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase5() : void
	{
		$expected = '<a href="#user" data-url="http://localhost/user">http://localhost/user</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/user');
		$stubUrl->shouldReceive('hashSpa')->andReturn('#user');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnTrue();

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnTrue();
		$stubApp->shouldReceive('isSpa')->andReturnTrue();

		$result = Html::link('user');

		$this->assertEquals($expected, $result);
	}

	// Html::linkUnlessCurrent()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkUnlessCurrentCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string, array or null');

		$this->expectException(InvalidArgumentException::class);

		Html::linkUnlessCurrent('user', 'User', new stdClass());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkUnlessCurrentCase2() : void
	{
		$expected = 'http://localhost';

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('url')->andReturn('http://localhost');

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnTrue();

		$result = Html::linkUnlessCurrent();

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkUnlessCurrentCase3() : void
	{
		$expected = '<a href="http://localhost/contact">Contact</a>';

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('url')->andReturn('http://localhost');

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/contact');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$result = Html::linkUnlessCurrent('contact', 'Contact');

		$this->assertEquals($expected, $result);
	}

	// Html::mailto()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMailtoCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string, array or null');

		$this->expectException(InvalidArgumentException::class);

		Html::mailto('user', 'User', new stdClass());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMailtoCase2() : void
	{
		$expected = '<a href="mailto:nat@withnat.com">nat@withnat.com</a>';

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnTrue();

		$result = Html::mailto('nat@withnat.com');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMailtoCase3() : void
	{
		$expected = '<a href="mailto:nat@withnat.com">Contact</a>';

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::mailto('nat@withnat.com', 'Contact');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMailtoCase4() : void
	{
		$expected = '<a style="font-weight:bold;" href="mailto:nat@withnat.com">Contact</a>';

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::mailto('nat@withnat.com', 'Contact', 'style="font-weight:bold;"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMailtoCase5() : void
	{
		$expected = '<a style="font-weight:bold;" href="mailto:nat@withnat.com">Contact</a>';

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toString')->andReturn('style="font-weight:bold;"');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::mailto('nat@withnat.com', 'Contact', ['style' => 'font-weight:bold;']);

		$this->assertEquals($expected, $result);
	}

	// Html::image()

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string, array or null');

		$this->expectException(InvalidArgumentException::class);

		Html::image('image.jpg', 'alt', new stdClass());
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase2() : void
	{
		$expected = '<img src="http://localhost/image.jpg" alt="" title="">';

		$result = Html::image('http://localhost/image.jpg');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase3() : void
	{
		$expected = '<img src="http://localhost/image.jpg" style="font-weight:bold;" alt="Image" title="Image">';

		$result = Html::image('http://localhost/image.jpg', 'Image', 'style="font-weight:bold;"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase4() : void
	{
		$expected = '<img src="http://localhost/image.jpg" style="font-weight:bold;" alt="Image" title="Image">';

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toString')->andReturn('style="font-weight:bold;"');

		$result = Html::image('http://localhost/image.jpg', 'Image', ['style' => 'font-weight:bold;']);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase5() : void
	{
		$expected = '<img src="http://localhost/image.jpg" alt="" title="" width="100" height="100">';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->once())->willReturn(true);

		$stubImage = Mockery::mock('alias:\System\Image');
		$stubImage->shouldReceive('load')->andReturnTrue();
		$stubImage->shouldReceive('width')->andReturn(100);
		$stubImage->shouldReceive('height')->andReturn(100);

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('basePath')->andReturn('http://localhost');

		$result = Html::image('image.jpg');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase6() : void
	{
		$expected = '<img src="http://localhost/assets/images/image.jpg" alt="" title="" width="100" height="100">';

		$stubFile = $this->getFunctionMock('System', 'is_file');
		$stubFile->expects($this->once())->willReturn(false);

		$stubFile = Mockery::mock('alias:\System\File');
		$stubFile->shouldReceive('getAssetPath')->andReturn('assets/images/image.jpg');

		$stubImage = Mockery::mock('alias:\System\Image');
		$stubImage->shouldReceive('load')->andReturnTrue();
		$stubImage->shouldReceive('width')->andReturn(100);
		$stubImage->shouldReceive('height')->andReturn(100);

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('basePath')->andReturn('http://localhost');

		$result = Html::image('image.jpg');

		$this->assertEquals($expected, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase7() : void
	{
		$expected = '<img src="http://localhost/image.jpg" alt="" title="" width="100" height="100">';

		$stubImage = Mockery::mock('alias:\System\Image');
		$stubImage->shouldReceive('load')->andReturnTrue();
		$stubImage->shouldReceive('width')->andReturn(100);
		$stubImage->shouldReceive('height')->andReturn(100);

		$stubRequest = Mockery::mock('alias:\System\Request');
		$stubRequest->shouldReceive('basePath')->andReturn('http://localhost');

		$result = Html::image('/image.jpg');

		$this->assertEquals($expected, $result);
	}

	// Html::css()

	/**
	 * Check attributes datatype.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase1() : void
	{
		$stubInflector = Mockery::mock('alias:\System\Inflector');
		$stubInflector->shouldReceive('sentence')->andReturn('string, array or null');

		$this->expectException(InvalidArgumentException::class);

		Html::css('style.css', new stdClass());
	}

	/**
	 * Ensure Html::_showIncludeFileWarning() method is called.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase2() : void
	{
		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_showIncludeFileWarning')->once();
		$html->shouldReceive('_getCssUrl')->andReturn(['dummy', 'dummy']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('development');

		$stubArray = $this->getFunctionMock('System', 'in_array');
		$stubArray->expects($this->once())->willReturn(true);

		$html->css('style.css');

		$this->assertTrue(true);
	}

	/**
	 * No attributes.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase3() : void
	{
		$expected = '<link rel="stylesheet" type="text/css" href="http://localhost/assets/css/style.css">';

		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_getCssUrl')->andReturn(['http://localhost/assets/css/style.css', '']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('production');

		$result = $html->css('style.css');

		$this->assertEquals($expected, $result);
	}

	/**
	 * Given attributes is a string.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase4() : void
	{
		$expected = '<link rel="stylesheet" type="text/css" href="http://localhost/assets/css/style.css" media="print">';

		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_getCssUrl')->andReturn(['http://localhost/assets/css/style.css', '']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('production');

		$result = $html->css('style.css', 'media="print"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * Given attributes is an array.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase5() : void
	{
		$expected = '<link rel="stylesheet" type="text/css" href="http://localhost/assets/css/style.css" media="print">';

		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_getCssUrl')->andReturn(['http://localhost/assets/css/style.css', '']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('production');

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toString')->andReturn('media="print"');

		$result = $html->css('style.css', ['media' => "print"]);

		$this->assertEquals($expected, $result);
	}

	/**
	 * Development mode, no given query.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase6() : void
	{
		$expected = '<link rel="stylesheet" type="text/css" href="http://localhost/assets/css/style.css?v=1695701570">';

		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_getCssUrl')->andReturn(['http://localhost/assets/css/style.css', '']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('development');

		$stubTime = $this->getFunctionMock('System', 'time');
		$stubTime->expects($this->once())->willReturn(1695701570);

		$result = $html->css('style.css');

		$this->assertEquals($expected, $result);
	}
}
