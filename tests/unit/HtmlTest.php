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

	/*
	 * 1. Check attribute datatype.
	 * 2. No SPA mode. No given URL, text and attribute.
	 * 3. No SPA mode. Has a given URL and text.
	 * 4. No SPA mode. A given attribute is a string.
	 * 5. No SPA mode. A given attribute is an array.
	 * 6. SPA mode. No given URL, text and attribute.
	 * 7. SPA mode. Has a given URL and text.
	 * 8. SPA mode. A given attribute is a string.
	 * 9. SPA mode. A given attribute is an array.
	 */

	/**
	 * 1. Check attribute datatype.
	 *
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
	 * 2. No SPA mode. No given URL, text and attribute.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase2() : void
	{
		$expected = '<a href="http://localhost">http://localhost</a>';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnTrue();

		$result = Html::link();

		$this->assertEquals($expected, $result);
	}

	/**
	 * 3. No SPA mode. Has a given URL and text.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase3() : void
	{
		$expected = '<a href="http://localhost/user">User</a>';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/user');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::link('user', 'User');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 4. No SPA mode. A given attribute is a string.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase4() : void
	{
		$expected = '<a class="primary" href="http://localhost/user">User</a>';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/user');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::link('user', 'User', 'class="primary"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 5. No SPA mode. A given attribute is an array.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase5() : void
	{
		$expected = '<a class="primary" href="http://localhost/user">User</a>';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnFalse();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/user');

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toString')->andReturn('class="primary"');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::link('user', 'User', ['class' => 'primary']);

		$this->assertEquals($expected, $result);
	}

	/**
	 * 6. SPA mode. No given URL, text and attribute.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase6() : void
	{
		$expected = '<a href="#" data-url="http://localhost">http://localhost</a>';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnTrue();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');
		$stubUrl->shouldReceive('hashSpa')->andReturn('#');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnTrue();

		$result = Html::link();

		$this->assertEquals($expected, $result);
	}

	/**
	 * 7. SPA mode. Has a given URL and text.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase7() : void
	{
		$expected = '<a href="#user" data-url="http://localhost">User</a>';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnTrue();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');
		$stubUrl->shouldReceive('hashSpa')->andReturn('#user');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::link('user', 'User');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 8. SPA mode. A given attribute is a string.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase8() : void
	{
		$expected = '<a class="primary" href="#user" data-url="http://localhost">User</a>';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnTrue();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');
		$stubUrl->shouldReceive('hashSpa')->andReturn('#user');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::link('user', 'User', 'class="primary"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 9. SPA mode. A given attribute is an array.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkCase9() : void
	{
		$expected = '<a class="primary" href="#user" data-url="http://localhost">User</a>';

		$stubApp = Mockery::mock('alias:\System\App');
		$stubApp->shouldReceive('isSpa')->andReturnTrue();

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');
		$stubUrl->shouldReceive('hashSpa')->andReturn('#user');

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toString')->andReturn('class="primary"');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::link('user', 'User', ['class' => 'primary']);

		$this->assertEquals($expected, $result);
	}

	// Html::linkUnlessCurrent()

	/*
	 * 1. Check attribute datatype.
	 * 2. Link to current page.
	 * 3. Link to another page.
	 */

	/**
	 * 1. Check attribute datatype.
	 *
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
	 * 2. Link to current page.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkUnlessCurrentCase2() : void
	{
		$expected = 'http://localhost';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('current')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnTrue();

		$result = Html::linkUnlessCurrent();

		$this->assertEquals($expected, $result);
	}

	/**
	 * 3. Link to another page.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLinkUnlessCurrentCase3() : void
	{
		$expected = '<a href="http://localhost/contact">Contact</a>';

		$stubUrl = Mockery::mock('alias:\System\Url');
		$stubUrl->shouldReceive('current')->andReturn('http://localhost');
		$stubUrl->shouldReceive('create')->andReturn('http://localhost/contact');

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$html = Mockery::mock('System\Html')->makePartial();
		$html->shouldReceive('link')->andReturn($expected);

		$result = $html->linkUnlessCurrent('contact', 'Contact');

		$this->assertEquals($expected, $result);
	}

	// Html::mailto()

	/*
	 * 1. Check attribute datatype.
	 * 2. No given text and attribute.
	 * 3. A given attribute is a string.
	 * 4. A given attribute is an array.
	 */

	/**
	 * 1. Check attribute datatype.
	 *
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
	 * 2. No given text and attribute.
	 *
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
	 * 3. A given attribute is a string.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodMailtoCase3() : void
	{
		$expected = '<a class="primary" href="mailto:nat@withnat.com">Contact</a>';

		$stubStr = Mockery::mock('alias:\System\Str');
		$stubStr->shouldReceive('isBlank')->andReturnFalse();

		$result = Html::mailto('nat@withnat.com', 'Contact', 'class="primary"');

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

	/*
	 * 1. Check attribute datatype.
	 * 2. No given alt and attribute.
	 * 3. Has a given alt.
	 * 4. A given attribute is a string.
	 * 5. A given attribute is an array.
	 * 6. A given attribute is 'alt'.
	 * 7. A given attribute is 'title'.
	 * 8. A given URL does not start with 'http' or even a slash, the image has been found.
	 * 9. A given URL does not start with 'http' or even a slash, the image was not found.
	 * 10. A given URL does not start with 'http' but with a slash.
	 * 11. A given URL does not start with 'http', the image can be loaded.
	 * 12. A given URL does not start with 'http', the image cannot be loaded.
	 * 13. A given URL starts with 'http'.
	 */

	/**
	 * 1. Check attribute datatype.
	 *
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
	 * 2. No given alt and attribute.
	 *
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
	 * 3. Has a given alt.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase3() : void
	{
		$expected = '<img src="http://localhost/image.jpg" alt="Image" title="Image">';

		$result = Html::image('http://localhost/image.jpg', 'Image');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 4. A given attribute is a string.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase4() : void
	{
		$expected = '<img src="http://localhost/image.jpg" class="rounded" alt="" title="">';

		$result = Html::image('http://localhost/image.jpg', null, 'class="rounded"');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 5. A given attribute is an array.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodImageCase5() : void
	{
		$expected = '<img src="http://localhost/image.jpg" class="rounded" alt="" title="">';

		$stubArr = Mockery::mock('alias:\System\Arr');
		$stubArr->shouldReceive('toString')->andReturn('class="rounded"');

		$result = Html::image('http://localhost/image.jpg', null, ['class' => 'rounded']);

		$this->assertEquals($expected, $result);
	}

	// Html::css()

	/*
	 * 1. Check attribute datatype.
	 * 2. Ensure Html::_showIncludeFileWarning() method is called.
	 * 3. No given attribute.
	 * 4. A given attribute is a string.
	 * 5. A given attribute is an array.
	 * 6. Development mode, no given query.
	 * 7. Development mode, has a given query, no version.
	 * 8. Development mode, has a given query, has version.
	 * 9. Production mode, no given query.
	 * 10. Production mode, has a given query, no version.
	 * 11. Production mode, has a given query, has version.
	 */

	/**
	 * 1. Check attribute datatype.
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
	 * 2. Ensure Html::_showIncludeFileWarning() method is called.
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
	 * 3. No given attribute.
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
	 * 4. A given attribute is a string.
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
	 * 5. A given attribute is an array.
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
	 * 6. Development mode, no given query.
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

	/**
	 * 7. Development mode, has a given query, no version.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase7() : void
	{
		$expected = '<link rel="stylesheet" type="text/css" href="http://localhost/assets/css/style.css?dummyKey=dummyVal&v=1695701570">';

		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_getCssUrl')->andReturn(['http://localhost/assets/css/style.css', 'dummyKey=dummyVal']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('development');

		$stubTime = $this->getFunctionMock('System', 'time');
		$stubTime->expects($this->once())->willReturn(1695701570);

		$result = $html->css('style.css?dummyKey=dummyVal');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 8. Development mode, has a given query, has version.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase8() : void
	{
		$expected = '<link rel="stylesheet" type="text/css" href="http://localhost/assets/css/style.css?v=1695701570">';

		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_getCssUrl')->andReturn(['http://localhost/assets/css/style.css', 'v=1695701570']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('development');

		$result = $html->css('style.css?v=1695701570');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 9. Production mode, no given query.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase9() : void
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
	 * 10. Production mode, has a given query, no version.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase10() : void
	{
		$expected = '<link rel="stylesheet" type="text/css" href="http://localhost/assets/css/style.css?dummyKey=dummyVal">';

		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_getCssUrl')->andReturn(['http://localhost/assets/css/style.css', 'dummyKey=dummyVal']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('production');

		$result = $html->css('style.css?dummyKey=dummyVal');

		$this->assertEquals($expected, $result);
	}

	/**
	 * 11. Production mode, has a given query, has version.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodCssCase11() : void
	{
		$expected = '<link rel="stylesheet" type="text/css" href="http://localhost/assets/css/style.css?v=1695701570">';

		$html = Mockery::mock('System\Html');
		$html->shouldAllowMockingProtectedMethods()->makePartial();
		$html->shouldReceive('_getCssUrl')->andReturn(['http://localhost/assets/css/style.css', 'v=1695701570']);

		$stubConfig = Mockery::mock('alias:\System\Config');
		$stubConfig->shouldReceive('app')->with('env')->andReturn('production');

		$result = $html->css('style.css?v=1695701570');

		$this->assertEquals($expected, $result);
	}
}
