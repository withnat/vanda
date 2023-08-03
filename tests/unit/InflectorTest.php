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
use System\Inflector;

/**
 * Class InflectorTest
 * @package Tests\Unit
 * @see https://www.cl.cam.ac.uk/~mgk25/ucs/examples/quickbrown.txt
 */
class InflectorTest extends TestCase
{
	public static function isCountableProvider() : array
	{
		return [
			['audio', false],
			['AUDIO', false],
			['Dog', true]
		];
	}

	public static function pluralizeProvider() : array
	{
		return [
			['audio', 'audio'],
			['quiz', 'quizzes'],
			['ox', 'oxen'],
			['mouse', 'mice'],
			['matrix', 'matrices'],
			['search', 'searches'],
			['query', 'queries'],
			['archive', 'archives'],
			['half', 'halves'],
			['basis', 'bases'],
			['datum', 'data'],
			['person', 'people'],
			['man', 'men'],
			['child', 'children'],
			['buffalo', 'buffaloes'],
			['bus', 'buses'],
			['alias', 'aliases'],
			['octopus', 'octopi'],
			['axis', 'axes'],
		];
	}

	public static function singularizeProvider() : array
	{
		return [
			['matrices', 'matrix'],
			['vertices', 'vertex'],
			['oxen', 'ox'],
			['aliases', 'alias'],
			['octopi', 'octopus'],
			['crises', 'crisis'],
			['shoes', 'shoe'],
			['oes', 'o'],
			['buses', 'bus'],
			['mice', 'mouse'],
			['xes', 'x'],
			['movies', 'movie'],
			['series', 'series'],
			['tives', 'tive'],
			['hives', 'hive'],
			['people', 'person'],
			['men', 'man'],
			['statuses', 'status'],
			['children', 'child'],
			['news', 'news'],
			['quizzes', 'quiz']
		];
	}

	public static function camelizeProvider() : array
	{
		return [
			['Some Day', 'SomeDay'],
			['some_day', 'SomeDay'],
			['She\'s hot', 'SheSHot']
		];
	}

	public static function underscoreProvider() : array
	{
		return [
			['FooBar', 'foo_bar'],
			['foo bar', 'foo_bar']
		];
	}

	public static function variablizeProvider() : array
	{
		return [
			['Some Day', 'someDay'],
			['some_day', 'someDay'],
			['She\'s hot', 'sheSHot']
		];
	}

	public static function foreignKeyProvider() : array
	{
		return [
			['', ''],
			['UserGroup', 'userGroupId']
		];
	}

	public static function controllerizeProvider() : array
	{
		return [
			['Some Day', 'SomeDayController'],
			['some_day', 'SomeDayController'],
			['She\'s hot', 'SheSHotController']
		];
	}

	public static function actionizeProvider() : array
	{
		return [
			['Some Day', 'SomeDayAction'],
			['some_day', 'SomeDayAction'],
			['She\'s hot', 'SheSHotAction']
		];
	}

	public static function sentenceProvider() : array
	{
		return [
			[[], ''],
			[['Nat', 'Angela'], 'Nat and Angela'],
			[['Nat', 'Emma', 'Angela'], 'Nat, Emma and Angela']
		];
	}

	public static function ordinalizeProvider() : array
	{
		return [
			[1, 'st'],
			[21, 'st'],
			[2, 'nd'],
			[22, 'nd'],
			[3, 'rd'],
			[23, 'rd'],
			[4, 'th'],
			[24, 'th'],
			[111, 'th'],
			[112, 'th'],
			[113, 'th']
		];
	}

	protected function tearDown() : void
	{
		Mockery::close();
	}

	// Inflector::isCountableWord()

	/**
	 * @param string $string
	 * @param bool   $expected
	 * @dataProvider isCountableProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodIsCountableWord(string $string, bool $expected) : void
	{
		$mockedConfig = Mockery::mock('alias:\System\Config');
		$mockedConfig->shouldReceive('inflector')->with('unCountableWords')->andReturn(['audio']);

		$result = Inflector::isCountableWord($string);

		$this->assertSame($expected, $result);
	}

	// Inflector::pluralize()

	/**
	 * @param string $noun
	 * @param string $expected
	 * @dataProvider pluralizeProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodPluralize(string $noun, string $expected) : void
	{
		$mockedConfig = Mockery::mock('alias:\System\Config');
		$mockedConfig->shouldReceive('inflector')->with('unCountableWords')->andReturn(['audio']);

		$result = Inflector::pluralize($noun);

		$this->assertEquals($expected, $result);
	}

	// Inflector::singularize()

	/**
	 * @param string $noun
	 * @param string $expected
	 * @dataProvider singularizeProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodSingularize(string $noun, string $expected) : void
	{
		$mockedConfig = Mockery::mock('alias:\System\Config');
		$mockedConfig->shouldReceive('inflector')->with('unCountableWords')->andReturn(['audio']);

		$result = Inflector::singularize($noun);

		$this->assertEquals($expected, $result);
	}

	// Inflector::camelize()

	/**
	 * @param string $string
	 * @param string $expected
	 * @dataProvider camelizeProvider
	 */
	public function testMethodCamelize(string $string, string $expected) : void
	{
		$result = Inflector::camelize($string);

		$this->assertEquals($expected, $result);
	}

	// Inflector::underscore()

	/**
	 * @param string $string
	 * @param string $expected
	 * @dataProvider underscoreProvider
	 */
	public function testMethodUnderscore(string $string, string $expected) : void
	{
		$result = Inflector::underscore($string);

		$this->assertEquals($expected, $result);
	}

	// Inflector::explode()

	public function testMethodExplode() : void
	{
		$result = Inflector::explode('FooBar');

		$this->assertEquals(['foo', 'bar'], $result);
	}

	// Inflector::implode()

	public function testMethodImplode() : void
	{
		$result = Inflector::implode(['foo', 'bar']);

		$this->assertEquals('FooBar', $result);
	}

	// Inflector::humanize()

	public function testMethodHumanize() : void
	{
		$result = Inflector::humanize('I had my car fixed_yesTerday');

		$this->assertEquals('I Had My Car Fixed Yesterday', $result);
	}

	// Inflector::variablize()

	/**
	 * @param string $string
	 * @param string $expected
	 * @dataProvider variablizeProvider
	 */
	public function testMethodVariablize(string $string, string $expected) : void
	{
		$result = Inflector::variablize($string);

		$this->assertEquals($expected, $result);
	}

	// Inflector::foreignKey()

	/**
	 * @param string $string
	 * @param string $expected
	 * @dataProvider foreignKeyProvider
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodForeignKey(string $string, string $expected) : void
	{
		$mockedStr = Mockery::mock('alias:\System\Str');
		$mockedStr->shouldReceive('ensureEndsWith')
			->andReturnUsing(function ($arg1, $arg2)
			{
				if ($arg1)
					return $arg1 . $arg2;
				else
					return '';
			});

		$result = Inflector::foreignKey($string);

		$this->assertEquals($expected, $result);
	}

	// Inflector::slugify()

	public function testMethodSlugifyCase1() : void
	{
		$result = Inflector::slugify('Vanda PHP Web Framework');

		$this->assertEquals('vanda-php-web-framework', $result);
	}

	public function testMethodSlugifyCase2() : void
	{
		$result = Inflector::slugify('Vanda PHP Web Framework', '_', false);

		$this->assertEquals('Vanda_PHP_Web_Framework', $result);
	}

	// Inflector::ordinalize()

	/**
	 * @param int    $number
	 * @param string $suffix
	 * @dataProvider ordinalizeProvider
	 */
	public function testMethodOrdinalize(int $number, string $suffix) : void
	{
		$expected = $number . $suffix;
		$result = Inflector::ordinalize($number);

		$this->assertEquals($expected, $result);
	}

	// Inflector::controllerize()

	/**
	 * @param string $string
	 * @param string $expected
	 * @dataProvider controllerizeProvider
	 */
	public function testMethodControllerize(string $string, string $expected) : void
	{
		$result = Inflector::controllerize($string);

		$this->assertEquals($expected, $result);
	}

	// Inflector::actionize()

	/**
	 * @param string $string
	 * @param string $expected
	 * @dataProvider actionizeProvider
	 */
	public function testMethodActionize(string $string, string $expected) : void
	{
		$result = Inflector::actionize($string);

		$this->assertEquals($expected, $result);
	}

	//Inflector::sentence()

	/**
	 * @param array  $words
	 * @param string $expected
	 * @dataProvider sentenceProvider
	 */
	public function testMethodSentence(array $words, string $expected) : void
	{
		$result = Inflector::sentence($words);

		$this->assertEquals($expected, $result);
	}
}
