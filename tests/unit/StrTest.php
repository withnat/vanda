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
use System\Str;

/**
 * Class StrTest
 * @package Tests\Unit
 * @see https://www.cl.cam.ac.uk/~mgk25/ucs/examples/quickbrown.txt
 */
class StrTest extends TestCase
{
	protected static $_string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	protected static $_text = 'Nat is so tall, and handsome as hell. Nat is so bad but he does it so well.';

    protected function tearDown() : void
    {
        Mockery::close();
    }

	// Str::length()

	public function testMethodLengthCase1() : void
	{
		$result = Str::length('');

		$this->assertEquals(0, $result);
	}

	public function testMethodLengthCase2() : void
	{
		$result = Str::length(static::$_string);

		$this->assertEquals(30, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLengthCase3() : void
	{
		$mockedConfig = Mockery::mock('alias:\System\Config');
		$mockedConfig->shouldReceive('app')->with('charset')->andReturn('UTF-8');

		$result = Str::length(static::$_string);

		$this->assertEquals(30, $result);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testMethodLengthCase4() : void
	{
		$mockedConfig = Mockery::mock('alias:\System\Config');
		$mockedConfig->shouldReceive('app')->with('charset')->andReturn('');

		$result = Str::length(static::$_string);

		$this->assertEquals(30, $result);
	}

	// Str::count()

	public function testMethodCountCase1() : void
	{
		$result = Str::count(static::$_string, 'A');

		$this->assertEquals(1, $result);
	}

	public function testMethodCountCase2() : void
	{
		$result = Str::count(static::$_string, 'A', false);

		$this->assertEquals(4, $result);
	}

	// Str::countWords()

	public function testMethodCountwordsCase1() : void
	{
		$result = Str::countWords(static::$_text);

		$this->assertEquals(18, $result);
	}

	// Str::left()

	public function testMethodLeftCase1() : void
	{
		$result = Str::left('', 0);

		$this->assertEquals('', $result);
	}

	public function testMethodLeftCase2() : void
	{
		$result = Str::left(static::$_string, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodLeftCase3() : void
	{
		$result = Str::left(static::$_string, 5);

		$this->assertEquals('ABCDE', $result);
	}

	public function testMethodLeftCase4() : void
	{
		$result = Str::left(static::$_string, -13);

		$this->assertEquals('ABCDEF:eFMNRZa:/f', $result);
	}

	public function testMethodLeftCase5() : void
	{
		$result = Str::left(static::$_string, 100);

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodLeftCase6() : void
	{
		$result = Str::left(static::$_string, -100);

		$this->assertEquals('', $result);
	}

	// Str::right()

	public function testMethodRightCase1() : void
	{
		$result = Str::right('', 0);

		$this->assertEquals('', $result);
	}

	public function testMethodRightCase2() : void
	{
		$result = Str::right(static::$_string, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodRightCase3() : void
	{
		$result = Str::right(static::$_string, 6);

		$this->assertEquals(':Bmnrz', $result);
	}

	public function testMethodRightCase4() : void
	{
		$result = Str::right(static::$_string, -13);

		$this->assertEquals('a:/fabcdefa:Bmnrz', $result);
	}

	public function testMethodRightCase5() : void
	{
		$result = Str::right(static::$_string, 100);

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodRightCase6() : void
	{
		$result = Str::right(static::$_string, -100);

		$this->assertEquals('', $result);
	}

	// Str::at()

	public function testMethodAtCase1() : void
	{
		$result = Str::at('', 5);

		$this->assertEquals('', $result);
	}

	public function testMethodAtCase2() : void
	{
		$result = Str::at(static::$_string, 5);

		$this->assertEquals('F', $result);
	}

	public function testMethodAtCase3() : void
	{
		$result = Str::at(static::$_string, -6);

		$this->assertEquals(':', $result);
	}

	public function testMethodAtCase4() : void
	{
		$result = Str::at(static::$_string, 100);

		$this->assertEquals('', $result);
	}

	public function testMethodAtCase5() : void
	{
		$result = Str::at(static::$_string, -100);

		$this->assertEquals('', $result);
	}

	// Str::slice()

	// Test Case
	// $start	$length
	// 0 		+
	// 0		-
	// +		+
	// +		-
	// -		+
	// -		-

	public function testMethodSliceCase1() : void
	{
		$result = Str::slice('', 0, 3);

		$this->assertEquals('', $result);
	}

	public function testMethodSliceCase2() : void
	{
		$result = Str::slice(static::$_string, 0, 0);

		$this->assertEquals('', $result);
	}

	public function testMethodSliceCase3() : void
	{
		$result = Str::slice(static::$_string, 0, 3);

		$this->assertEquals('ABC', $result);
	}

	public function testMethodSliceCase4() : void
	{
		$result = Str::slice(static::$_string, 0, -13);

		$this->assertEquals('ABCDEF:eFMNRZa:/f', $result);
	}

	public function testMethodSliceCase5() : void
	{
		$result = Str::slice(static::$_string, 12, 4);

		$this->assertEquals('Za:/', $result);
	}

	public function testMethodSliceCase6() : void
	{
		$result = Str::slice(static::$_string, 5, -10);

		$this->assertEquals('F:eFMNRZa:/fabc', $result);
	}

	public function testMethodSliceCase7() : void
	{
		$result = Str::slice(static::$_string, -8, 2);

		$this->assertEquals('fa', $result);
	}

	public function testMethodSliceCase8() : void
	{
		$result = Str::slice(static::$_string, -8, -3);

		$this->assertEquals('fa:Bm', $result);
	}

	// Str::limit()
	// Test for English only as this method does not work with Thai correctly.

	public function testMethodLimitCase1() : void
	{
		$result = Str::limit(static::$_text, -1);

		$this->assertEquals('...', $result);
	}

	public function testMethodLimitCase2() : void
	{
		$result = Str::limit(static::$_text, 0);

		$this->assertEquals('...', $result);
	}

	public function testMethodLimitCase3() : void
	{
		$result = Str::limit(static::$_text, 11);

		$this->assertEquals('Nat is so tall,...', $result);
	}

	public function testMethodLimitCase4() : void
	{
		$result = Str::limit(static::$_text, 15);

		$this->assertEquals('Nat is so tall,...', $result);
	}

	public function testMethodLimitCase5() : void
	{
		$result = Str::limit(static::$_text, 100);

		$this->assertEquals(static::$_text, $result);
	}

	// Str::limitWords()

	public function testMethodLimitwordsCase1() : void
	{
		$result = Str::limitWords(static::$_text, -1);

		$this->assertEquals('...', $result);
	}

	public function testMethodLimitwordsCase2() : void
	{
		$result = Str::limitWords(static::$_text, 0);

		$this->assertEquals('...', $result);
	}

	public function testMethodLimitwordsCase3() : void
	{
		$result = Str::limitWords(static::$_text, 4);

		$this->assertEquals('Nat is so tall,...', $result);
	}

	public function testMethodLimitwordsCase4() : void
	{
		$result = Str::limitWords(static::$_text, 6);

		$this->assertEquals('Nat is so tall, and handsome...', $result);
	}

	public function testMethodLimitwordsCase5() : void
	{
		$result = Str::limitWords(static::$_text, 100);

		$this->assertEquals(static::$_text, $result);
	}

	// Str::position()

	public function testMethodPositionCase1() : void
	{
		$result = Str::position('', 'a');

		$this->assertFalse($result);
	}

	public function testMethodPositionCase2() : void
	{
		$result = Str::position(static::$_string, 'x');

		$this->assertFalse($result);
	}

	public function testMethodPositionCase3() : void
	{
		$result = Str::position(static::$_string, 'a');

		$this->assertEquals(13, $result);
	}

	public function testMethodPositionCase4() : void
	{
		$result = Str::position(static::$_string, ':', 4);

		$this->assertEquals(6, $result);
	}

	public function testMethodPositionCase5() : void
	{
		$result = Str::position(static::$_string, ':', -15);

		$this->assertEquals(24, $result);
	}

	// Str::lastPosition()

	public function testMethodLastpositionCase1() : void
	{
		$result = Str::lastPosition('', 'a');

		$this->assertFalse($result);
	}

	public function testMethodLastpositionCase2() : void
	{
		$result = Str::lastPosition(static::$_string, 'x');

		$this->assertFalse($result);
	}

	public function testMethodLastpositionCase3() : void
	{
		$result = Str::lastPosition(static::$_string, ':');

		$this->assertEquals(24, $result);
	}

	public function testMethodLastpositionCase4() : void
	{
		$result = Str::lastPosition(static::$_string, ':', -10);

		$this->assertEquals(14, $result);
	}

	// Str::between()

	public function testMethodBetweenCase1() : void
	{
		$result = Str::between(static::$_string, 'F', 'M');

		$this->assertEquals(':eF', $result);
	}

	public function testMethodBetweenCase2() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::between(static::$_string, ':', ':', -10);
	}

	public function testMethodBetweenCase3() : void
	{
		$result = Str::between(static::$_string, ':', ':', 10);

		$this->assertEquals('/fabcdefa', $result);
	}

	public function testMethodBetweenCase4() : void
	{
		$result = Str::between(static::$_string, 'NoneExistingChar', 'b');

		$this->assertEquals('', $result);
	}

	public function testMethodBetweenCase5() : void
	{
		$result = Str::between(static::$_string, 'a', 'NoneExistingChar');

		$this->assertEquals('', $result);
	}

	// Str::trim()

	public function testMethodTrimCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::trim('', 3.14);
	}

	public function testMethodTrimCase2() : void
	{
		$string = ' axb,ayb ';

		$result = Str::trim($string);

		$this->assertEquals('axb,ayb', $result);
	}

	public function testMethodTrimCase3() : void
	{
		$string = ' axb,ayb ';

		$result = Str::trim($string, 2);

		$this->assertEquals('xb,ay', $result);
	}

	public function testMethodTrimCase4() : void
	{
		$string = 'bbxa,aybb';

		$result = Str::trim($string, 'b');

		$this->assertEquals('xa,ay', $result);
	}

	// Str::trimLeft()

	public function testMethodTrimLeftCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::trimLeft('', 3.14);
	}

	public function testMethodTrimLeftCase2() : void
	{
		$string = ' axb,ayb ';

		$result = Str::trimLeft($string);

		$this->assertEquals('axb,ayb ', $result);
	}

	public function testMethodTrimLeftCase3() : void
	{
		$string = ' axb,ayb ';

		$result = Str::trimLeft($string, 2);

		$this->assertEquals('xb,ayb ', $result);
	}

	public function testMethodTrimLeftCase4() : void
	{
		$string = 'axb,ayb';

		$result = Str::trimLeft($string, -2);

		$this->assertEquals('yb', $result);
	}

	public function testMethodTrimLeftCase5() : void
	{
		$string = 'bbxa,ayb';

		$result = Str::trimLeft($string, 'b');

		$this->assertEquals('xa,ayb', $result);
	}

	// Str::trimRight()

	public function testMethodTrimRightCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::trimRight('', 3.14);
	}

	public function testMethodTrimRightCase2() : void
	{
		$string = ' axb,ayb ';

		$result = Str::trimRight($string);

		$this->assertEquals(' axb,ayb', $result);
	}

	public function testMethodTrimRightCase3() : void
	{
		$string = ' axb,ayb ';

		$result = Str::trimRight($string, 2);

		$this->assertEquals(' axb,ay', $result);
	}

	public function testMethodTrimRightCase4() : void
	{
		$string = 'axb,ayb';

		$result = Str::trimRight($string, -2);

		$this->assertEquals('ax', $result);
	}

	public function testMethodTrimRightCase5() : void
	{
		$string = 'bxa,aybb';

		$result = Str::trimRight($string, 'b');

		$this->assertEquals('bxa,ay', $result);
	}

	// Str::addSlashes()

	public function testMethodAddSlashesCase1() : void
	{
		$string = "'";

		$result = Str::addSlashes($string);

		$this->assertEquals("\'", $result);
	}

	public function testMethodAddSlashesCase2() : void
	{
		$string = '"';

		$result = Str::addSlashes($string);

		$this->assertEquals('\"', $result);
	}

	public function testMethodAddSlashesCase3() : void
	{
		$string = '\\';

		$result = Str::addSlashes($string);

		$this->assertEquals('\\\\', $result);
	}

	// Str::stripSlashes()

	public function testMethodStripSlashesCase1() : void
	{
		$string = "\'";

		$result = Str::stripSlashes($string);

		$this->assertEquals("'", $result);
	}

	public function testMethodStripSlashesCase2() : void
	{
		$string = '\"';

		$result = Str::stripSlashes($string);

		$this->assertEquals('"', $result);
	}

	public function testMethodStripSlashesCase3() : void
	{
		$string = '\\\\';

		$result = Str::stripSlashes($string);

		$this->assertEquals('\\', $result);
	}

	// Str::htmlEncode()

	public function testMethodHtmlEncodeCase1() : void
	{
		$string = '<strong>Nat</strong>';

		$result = Str::htmlEncode($string);

		$this->assertEquals('&lt;strong&gt;Nat&lt;/strong&gt;', $result);
	}

	// Str::htmlDecode()

	public function testMethodHtmlDecodeCase1() : void
	{
		$string = '&lt;strong&gt;Nat&lt;/strong&gt;';

		$result = Str::htmlDecode($string);

		$this->assertEquals('<strong>Nat</strong>', $result);
	}

	// Str::removeWhitespace()

	public function testMethodRemoveWhitespaceCase1() : void
	{
		$string = " a\tb\nc\rd\0e\x0Bf";

		$result = Str::removeWhitespace($string);

		$this->assertEquals('abcdef', $result);
	}

	// Str::removeTags()

	public function testMethodRemoveTagsCase1() : void
	{
		$string = '<strong>Nat</strong>';

		$result = Str::removeTags($string);

		$this->assertEquals('Nat', $result);
	}

	// Str::removeQuotes()

	public function testMethodRemoveQuotesCase1() : void
	{
		$string = '"Nat"';

		$result = Str::removeQuotes($string);

		$this->assertEquals('Nat', $result);
	}

	public function testMethodRemoveQuotesCase2() : void
	{
		$string = "'Nat'";

		$result = Str::removeQuotes($string);

		$this->assertEquals('Nat', $result);
	}

	// Str::removeInvisibleCharacters()

	public function testMethodRemoveInvisibleCharactersCase1() : void
	{
		$string = "http://www.some-site.com//index.php\0";

		$result = Str::removeInvisibleCharacters($string, true);

		$this->assertEquals('http://www.some-site.com//index.php', $result);
	}

	// Str::removeLeft()

	public function testMethodRemoveLeftCase1() : void
	{
		$result = Str::removeLeft(static::$_string, 'NoneExistingChar');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodRemoveLeftCase2() : void
	{
		$result = Str::removeLeft(static::$_string, 'ABCDEF');

		$this->assertEquals(':eFMNRZa:/fabcdefa:Bmnrz', $result);
	}

	// Str::removeRight()

	public function testMethodRemoveRightCase1() : void
	{
		$result = Str::removeRight(static::$_string, 'NoneExistingChar');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodRemoveRightCase2() : void
	{
		$result = Str::removeRight(static::$_string, 'defa:Bmnrz');

		$this->assertEquals('ABCDEF:eFMNRZa:/fabc', $result);
	}

	// Str::reduceDoubleSpaces()

	public function testMethodReduceDoubleSpacesCase1() : void
	{
		$string = 'A  B      C';

		$result = Str::reduceDoubleSpaces($string);

		$this->assertEquals('A B C', $result);
	}

	// Str::reduceDoubleSlashes()

	public function testMethodReduceDoubleSlashesCase1() : void
	{
		$string = 'http://www.some-site.com//index.php';

		$result = Str::reduceDoubleSlashes($string);

		$this->assertEquals('http://www.some-site.com/index.php', $result);
	}

	// Str::lowerCase()

	public function testMethodLowerCaseCase1() : void
	{
		$string = 'I LOVE YOU';

		$result = Str::lowerCase($string);

		$this->assertEquals('i love you', $result);
	}

	// Str::lowerCaseFirst()

	public function testMethodLowerCaseFirstCase1() : void
	{
		$string = 'I LOVE YOU';

		$result = Str::lowerCaseFirst($string);

		$this->assertEquals('i LOVE YOU', $result);
	}

	// Str::lowerCaseWords()

	public function testMethodLowerCaseWordsCase1() : void
	{
		$string = 'I LOVE YOU';

		$result = Str::lowerCaseWords($string);


		$this->assertEquals('i lOVE yOU', $result);
	}

	// Str::upperCase()

	public function testMethodUpperCaseCase1() : void
	{
		$string = 'i love you';

		$result = Str::upperCase($string);

		$this->assertEquals('I LOVE YOU', $result);
	}

	// Str::upperCaseFirst()

	public function testMethodUpperCaseFirstCase1() : void
	{
		$string = 'i love you';

		$result = Str::upperCaseFirst($string);

		$this->assertEquals('I love you', $result);
	}

	// Str::upperCaseWords()

	public function testMethodUpperCaseWordsCase1() : void
	{
		$string = 'i love you';

		$result = Str::upperCaseWords($string);

		$this->assertEquals('I Love You', $result);
	}

	// Str::repeat()

	public function testMethodRepeatCase1() : void
	{
		$string = 'A';

		$result = Str::repeat($string, 3);

		$this->assertEquals('AAA', $result);
	}

	// Str::replace()

	public function testMethodReplaceCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::replace('string', 'search', 3.14);
	}

	public function testMethodReplaceCase2() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::replace('string', 3.14, 'replace');
	}

	public function testMethodReplaceCase3() : void
	{
		$result = Str::replace(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodReplaceCase4() : void
	{
		$result = Str::replace(static::$_string, 'A', '|');

		$this->assertEquals('|BCDEF:eFMNRZa:/fabcdefa:Bmnrz', $result);
	}

	public function testMethodReplaceCase5() : void
	{
		$result = Str::replace(static::$_string, ':', '|', 1);

		$this->assertEquals('ABCDEF|eFMNRZa:/fabcdefa:Bmnrz', $result);
	}

	public function testMethodReplaceCase6() : void
	{
		$result = Str::replace(static::$_string, [':', '/'], ['|', '-']);

		$this->assertEquals('ABCDEF|eFMNRZa|-fabcdefa|Bmnrz', $result);
	}

	public function testMethodReplaceCase7() : void
	{
		$result = Str::replace(static::$_string, 'x', 'y', 1);

		$this->assertEquals(static::$_string, $result);
	}

	// Str::replaceFirst()

	public function testMethodReplaceFirstCase1() : void
	{
		$result = Str::replaceFirst(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodReplaceFirstCase2() : void
	{
		$result = Str::replaceFirst(static::$_string, ':', '|');

		$this->assertEquals('ABCDEF|eFMNRZa:/fabcdefa:Bmnrz', $result);
	}

	// Str::replaceLast()

	public function testMethodReplaceLastCase1() : void
	{
		$result = Str::replaceLast(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodReplaceLastCase2() : void
	{
		$result = Str::replaceLast(static::$_string, ':', '|');

		$this->assertEquals('ABCDEF:eFMNRZa:/fabcdefa|Bmnrz', $result);
	}

	// Str::ireplace()

	public function testMethodIReplaceCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::ireplace('string', 'search', 3.14);
	}

	public function testMethodIReplaceCase2() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::ireplace('string', 3.14, 'replace');
	}

	public function testMethodIReplaceCase3() : void
	{
		$result = Str::ireplace(static::$_string, 'a', '|');

		$this->assertEquals('|BCDEF:eFMNRZ|:/f|bcdef|:Bmnrz', $result);
	}

	public function testMethodIReplaceCase4() : void
	{
		$result = Str::ireplace(static::$_string, 'a', '|', 1);

		$this->assertEquals('|BCDEF:eFMNRZa:/fabcdefa:Bmnrz', $result);
	}

	public function testMethodIReplaceCase5() : void
	{
		$result = Str::ireplace(static::$_string, ['a', 'b'], ['4', '8']);

		$this->assertEquals('48CDEF:eFMNRZ4:/f48cdef4:8mnrz', $result);
	}

	public function testMethodIReplaceCase6() : void
	{
		$result = Str::ireplace(static::$_string, 'x', 'y', 1);

		$this->assertEquals(static::$_string, $result);
	}

	// Str::ireplaceFirst()

	public function testMethodIReplaceFirstCase1() : void
	{
		$result = Str::ireplaceFirst(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodIReplaceFirstCase2() : void
	{
		$result = Str::ireplaceFirst(static::$_string, 'a', '|');

		$this->assertEquals('|BCDEF:eFMNRZa:/fabcdefa:Bmnrz', $result);
	}

	// Str::ireplaceLast()

	public function testMethodIReplaceLastCase1() : void
	{
		$result = Str::ireplaceLast(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodIReplaceLastCase2() : void
	{
		$result = Str::ireplaceLast(static::$_string, 'a', '|');

		$this->assertEquals('ABCDEF:eFMNRZa:/fabcdef|:Bmnrz', $result);
	}

	// Str::subreplace()

	public function testMethodSupreplaceCase1() : void
	{
		$result = Str::subreplace(static::$_string, '_____', 5);

		$this->assertEquals('ABCDE_____', $result);
	}

	public function testMethodSupreplaceCase2() : void
	{
		$result = Str::subreplace(static::$_string, '_____', 5, 5);

		$this->assertEquals('ABCDE_____NRZa:/fabcdefa:Bmnrz', $result);
	}

	public function testMethodSupreplaceCase3() : void
	{
		$result = Str::subreplace(static::$_string, '_____', 5, -5);

		$this->assertEquals('ABCDE_____Bmnrz', $result);
	}

	public function testMethodSupreplaceCase4() : void
	{
		$result = Str::subreplace(static::$_string, '_____', -5);

		$this->assertEquals('ABCDEF:eFMNRZa:/fabcdefa:_____', $result);
	}

	public function testMethodSupreplaceCase5() : void
	{
		$result = Str::subreplace(static::$_string, '_____', -5, 5);

		$this->assertEquals('ABCDEF:eFMNRZa:/fabcdefa:_____', $result);
	}

	public function testMethodSupreplaceCase6() : void
	{
		$result = Str::subreplace(static::$_string, '_____', -15, -5);

		$this->assertEquals('ABCDEF:eFMNRZa:_____Bmnrz', $result);
	}

	public function testMethodSupreplaceCase7() : void
	{
		$result = Str::subreplace(static::$_string, '_____', 100, 5);

		$this->assertEquals('ABCDEF:eFMNRZa:/fabcdefa:Bmnrz_____', $result);
	}

	// Str::insert()

	public function testMethodInsertCase1() : void
	{
		$string = 'My name is :name and :age years old.';

		$result = Str::insert($string, '', ['name' => 'Nat', 'age' => 38]);

		$this->assertEquals($string, $result);
	}

	public function testMethodInsertCase2() : void
	{
		$string = 'My name is ? and ? years old.';

		$result = Str::insert($string, '?', ['Nat', 38]);

		$this->assertEquals('My name is Nat and 38 years old.', $result);
	}

	public function testMethodInsertCase3() : void
	{
		$string = 'My name is :name and :age years old.';

		$result = Str::insert($string, ':', ['name' => 'Nat', 'age' => 38]);

		$this->assertEquals('My name is Nat and 38 years old.', $result);
	}

	// Str::reverse()

	public function testMethodReverseCase1() : void
	{
		$result = Str::reverse(static::$_string);

		$this->assertEquals('zrnmB:afedcbaf/:aZRNMFe:FEDCBA', $result);
	}

	// Str::startsWith()

	public function testMethodStartsWithCase1() : void
	{
		$result = Str::startsWith(static::$_string, 'A');

		$this->assertTrue($result);
	}

	public function testMethodStartsWithCase2() : void
	{
		$result = Str::startsWith(static::$_string, 'a');

		$this->assertFalse($result);
	}

	public function testMethodStartsWithCase3() : void
	{
		$result = Str::startsWith(static::$_string, 'a', false);

		$this->assertTrue($result);
	}

	// Str::startsWithAny()

	public function testMethodStartsWithAnyCase1() : void
	{
		$result = Str::startsWithAny(static::$_string, ['A', 'B']);

		$this->assertTrue($result);
	}

	public function testMethodStartsWithAnyCase2() : void
	{
		$result = Str::startsWithAny(static::$_string, ['a', 'b']);

		$this->assertFalse($result);
	}

	public function testMethodStartsWithAnyCase3() : void
	{
		$result = Str::startsWithAny(static::$_string, ['a', 'b'], false);

		$this->assertTrue($result);
	}

	// Str::endsWith()

	public function testMethodEndsWithCase1() : void
	{
		$result = Str::endsWith(static::$_string, 'z');

		$this->assertTrue($result);
	}

	public function testMethodEndsWithCase2() : void
	{
		$result = Str::endsWith(static::$_string, 'Z');

		$this->assertFalse($result);
	}

	public function testMethodEndsWithCase3() : void
	{
		$result = Str::endsWith(static::$_string, 'Z', false);

		$this->assertTrue($result);
	}

	// Str::endsWithAny()

	public function testMethodEndsWithAnyCase1() : void
	{
		$result = Str::endsWithAny(static::$_string, ['z', 'b']);

		$this->assertTrue($result);
	}

	public function testMethodEndsWithAnyCase2() : void
	{
		$result = Str::endsWithAny(static::$_string, ['Z', 'B']);

		$this->assertFalse($result);
	}

	public function testMethodEndsWithAnyCase3() : void
	{
		$result = Str::endsWithAny(static::$_string, ['Z', 'B'], false);

		$this->assertTrue($result);
	}

	// Str::ensureStartsWith()

	public function testMethodEnsureStartsWithCase1() : void
	{
		$result = Str::ensureStartsWith(static::$_string, 'ABC');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodEnsureStartsWithCase2() : void
	{
		$result = Str::ensureStartsWith(static::$_string, '_');

		$this->assertEquals('_' . static::$_string, $result);
	}

	// Str::ensureEndsWith()

	public function testMethodEnsureEndsWithCase1() : void
	{
		$result = Str::ensureEndsWith(static::$_string, 'nrz');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodEnsureEndsWithCase2() : void
	{
		$result = Str::ensureEndsWith(static::$_string, '_');

		$this->assertEquals(static::$_string . '_', $result);
	}

	// Str::wrap()

	public function testMethodWrapCase1() : void
	{
		$result = Str::wrap('', '|');

		$this->assertEquals('||', $result);
	}

	public function testMethodWrapCase2() : void
	{
		$result = Str::wrap('value', '|');

		$this->assertEquals('|value|', $result);
	}

	// Str::after()

	public function testMethodAfterCase1() : void
	{
		$result = Str::after(static::$_string, '');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodAfterCase2() : void
	{
		$result = Str::after(static::$_string, 'r');

		$this->assertEquals('z', $result);
	}

	public function testMethodAfterCase3() : void
	{
		$result = Str::after(static::$_string, 'r', false);

		$this->assertEquals('Za:/fabcdefa:Bmnrz', $result);
	}

	// Str::afterLast()

	public function testMethodAfterLastCase1() : void
	{
		$result = Str::afterLast(static::$_string, '');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodAfterLastCase2() : void
	{
		$result = Str::afterLast(static::$_string, 'b');

		$this->assertEquals('cdefa:Bmnrz', $result);
	}

	public function testMethodAfterLastCase3() : void
	{
		$result = Str::afterLast(static::$_string, 'b', false);

		$this->assertEquals('mnrz', $result);
	}

	// Str::before()

	public function testMethodBeforeCase1() : void
	{
		$result = Str::before(static::$_string, '');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodBeforeCase2() : void
	{
		// todo
		$result = Str::before(static::$_string, 'NoneExistingChar');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodBeforeCase3() : void
	{
		$result = Str::before(static::$_string, 'e');

		$this->assertEquals('ABCDEF:', $result);
	}

	public function testMethodBeforeCase4() : void
	{
		$result = Str::before(static::$_string, 'e', false);

		$this->assertEquals('ABCD', $result);
	}

	// Str::beforeLast()

	public function testMethodBeforeLastCase1() : void
	{
		$result = Str::beforeLast(static::$_string, '');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodBeforeLastCase2() : void
	{
		$result = Str::beforeLast(static::$_string, 'NoneExistingChar');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodBeforeLastCase3() : void
	{
		$result = Str::beforeLast(static::$_string, 'b');

		$this->assertEquals('ABCDEF:eFMNRZa:/fa', $result);
	}

	public function testMethodBeforeLastCase4() : void
	{
		$result = Str::beforeLast(static::$_string, 'b', false);

		$this->assertEquals('ABCDEF:eFMNRZa:/fabcdefa:', $result);
	}

	// Str::highlight()

	public function testMethodHighlightCase1() : void
	{
		$string = '<html>html</html>';

		$result = Str::highlight($string, 'html');

		$this->assertEquals('<html><mark>html</mark></html>', $result);
	}

	public function testMethodHighlightCase2() : void
	{
		$string = '<html>html</html>';

		$result = Str::highlight($string, 'html', '<start>', '</end>', false);

		$this->assertEquals('<<start>html</end>><start>html</end></<start>html</end>>', $result);
	}

	// Str::increment()

	public function testMethodIncrementCase1() : void
	{
		$string = 'file';

		$result = Str::increment($string);

		$this->assertEquals('file_1', $result);
	}

	public function testMethodIncrementCase2() : void
	{
		$string = 'file_1';

		$result = Str::increment($string);

		$this->assertEquals('file_2', $result);
	}

	public function testMethodIncrementCase3() : void
	{
		$string = 'file';

		$result = Str::increment($string, '-', 100);

		$this->assertEquals('file-100', $result);
	}

	public function testMethodIncrementCase4() : void
	{
		$string = 'file-100';

		$result = Str::increment($string, '-', 100);

		$this->assertEquals('file-101', $result);
	}

	// Str::floatToString()

	public function testMethodFloatToStringCase1() : void
	{
		$this->expectException(InvalidArgumentException::class);

		Str::floatToString('3.14');
	}

	public function testMethodFloatToStringCase2() : void
	{
		$result = Str::floatToString(3.14);

		$this->assertIsString($result);
		$this->assertEquals('3.14', $result);
	}

	// Str::spaceToTab()

	public function testMethodSpaceToTabCase1() : void
	{
		$result = Str::spaceToTab('    ');

		$this->assertEquals("\t", $result);
	}

	// Str::tabToSpace()

	public function testMethodTabToSpaceCase1() : void
	{
		$result = Str::tabToSpace("\t");

		$this->assertEquals('    ', $result);
	}

	// Str::pad()

	public function testMethodPadCase1() : void
	{
		$string = 'string';

		$result = Str::pad($string, '_', 5);

		$this->assertEquals($string, $result);
	}

	public function testMethodPadCase2() : void
	{
		$string = 'string';

		$result = Str::pad($string, '_', 10);

		$this->assertEquals('__string__', $result);
	}

	// Str::padLeft()

	public function testMethodPadLeftCase1() : void
	{
		$string = 'string';

		$result = Str::padLeft($string, '_', 5);

		$this->assertEquals($string, $result);
	}

	public function testMethodPadLeftCase2() : void
	{
		$string = 'string';

		$result = Str::padLeft($string, '_', 10);

		$this->assertEquals('____string', $result);
	}

	// Str::padRight()

	public function testMethodPadRightCase1() : void
	{
		$string = 'string';

		$result = Str::padRight($string, '_', 5);

		$this->assertEquals($string, $result);
	}

	public function testMethodPadRightCase2() : void
	{
		$string = 'string';

		$result = Str::padRight($string, '_', 10);

		$this->assertEquals('string____', $result);
	}

	// Str::isBlank()

	public function testMethodIsBlankCase1() : void
	{
		$result = Str::isBlank(0);

		$this->assertFalse($result);
	}

	public function testMethodIsBlankCase2() : void
	{
		$result = Str::isBlank('0');

		$this->assertFalse($result);
	}

	public function testMethodIsBlankCase3() : void
	{
		$result = Str::isBlank(null);

		$this->assertTrue($result);
	}

	public function testMethodIsBlankCase4() : void
	{
		$result = Str::isBlank(' ');

		$this->assertTrue($result);
	}

	public function testMethodIsBlankCase5() : void
	{
		$result = Str::isBlank(false);

		$this->assertTrue($result);
	}

	// Str::isAlpha()

	public function testMethodIsAlphaCase1() : void
	{
		$string = 416;

		$result = Str::isAlpha($string);

		$this->assertFalse($result);
	}

	public function testMethodIsAlphaCase2() : void
	{
		$string = '416';

		$result = Str::isAlpha($string);

		$this->assertFalse($result);
	}

	public function testMethodIsAlphaCase3() : void
	{
		$string = 'Valkyrie416';

		$result = Str::isAlpha($string);

		$this->assertFalse($result);
	}

	public function testMethodIsAlphaCase4() : void
	{
		$string = 'Valkyrie';

		$result = Str::isAlpha($string);

		$this->assertTrue($result);
	}

	// Str::isAlphanumeric()

	public function testMethodIsAlphanumericCase1() : void
	{
		$string = 416;

		$result = Str::isAlphanumeric($string);

		$this->assertTrue($result);
	}

	public function testMethodIsAlphanumericCase2() : void
	{
		$string = '416';

		$result = Str::isAlphanumeric($string);

		$this->assertTrue($result);
	}

	public function testMethodIsAlphanumericCase3() : void
	{
		$string = 'Valkyrie416';

		$result = Str::isAlphanumeric($string);

		$this->assertTrue($result);
	}

	public function testMethodIsAlphanumericCase4() : void
	{
		$string = 'Valkyrie';

		$result = Str::isAlphanumeric($string);

		$this->assertTrue($result);
	}

	// Str::isBase64Encoded()

	public function testMethodIsBase64EncodedCase1() : void
	{
		$string = 'TmF0IFdpdGhl';

		$result = Str::isBase64Encoded($string);

		$this->assertTrue($result);
	}

	public function testMethodIsBase64EncodedCase2() : void
	{
		$string = 'I love you';

		$result = Str::isBase64Encoded($string);

		$this->assertFalse($result);
	}

	// Str::isHexadecimal()

	public function testMethodIsHexadecimalCase1() : void
	{
		$string = 'D1CE';

		$result = Str::isHexadecimal($string);

		$this->assertTrue($result);
	}

	public function testMethodIsHexadecimalCase2() : void
	{
		$string = 'D1ZE';

		$result = Str::isHexadecimal($string);

		$this->assertFalse($result);
	}

	// Str::isLowerCase()

	public function testMethodIsLowerCaseCase1() : void
	{
		$string = 'Abc';

		$result = Str::isLowerCase($string);

		$this->assertFalse($result);
	}

	public function testMethodIsLowerCaseCase2() : void
	{
		$string = 'abc';

		$result = Str::isLowerCase($string);

		$this->assertTrue($result);
	}

	// Str::isUpperCase()

	public function testMethodIsUpperCaseCase1() : void
	{
		$string = 'Abc';

		$result = Str::isUpperCase($string);

		$this->assertFalse($result);
	}

	public function testMethodIsUpperCaseCase2() : void
	{
		$string = 'ABC';

		$result = Str::isUpperCase($string);

		$this->assertTrue($result);
	}

	// Str::isSerialized()

	public function testMethodIsSerializedCase1() : void
	{
		$string = 'b:0;';

		$result = Str::isSerialized($string);

		$this->assertTrue($result);
	}

	public function testMethodIsSerializedCase2() : void
	{
		$string = 'a:0:{}';

		$result = Str::isSerialized($string);

		$this->assertTrue($result);
	}

	public function testMethodIsSerializedCase3() : void
	{
		$string = 'string';

		$result = Str::isSerialized($string);

		$this->assertFalse($result);
	}

	// Str::isMultibyte()

	public function testMethodIsMultibyteCase1() : void
	{
		$string = 'NAT WITHE';

		$result = Str::isMultibyte($string);

		$this->assertFalse($result);
	}

	public function testMethodIsMultibyteCase2() : void
	{
		$string = 'ŅÀŦ ŴĨŦĤÈ';

		$result = Str::isMultibyte($string);

		$this->assertTrue($result);
	}

	public function testMethodIsMultibyteCase3() : void
	{
		$string = 'นัทเองไงจะใครล่ะ';

		$result = Str::isMultibyte($string);

		$this->assertTrue($result);
	}

	// Str::contains()

	public function testMethodContainsCase1() : void
	{
		$result = Str::contains(static::$_string, '');

		$this->assertFalse($result);
	}

	public function testMethodContainsCase2() : void
	{
		$result = Str::contains(static::$_string, 'NoneExistingChar');

		$this->assertFalse($result);
	}

	public function testMethodContainsCase3() : void
	{
		$result = Str::contains(static::$_string, 'za');

		$this->assertFalse($result);
	}

	public function testMethodContainsCase4() : void
	{
		$result = Str::contains(static::$_string, 'za', false);

		$this->assertTrue($result);
	}

	// Str::containsAny()

	public function testMethodContainsAnyCase1() : void
	{
		$result = Str::containsAny(static::$_string, []);

		$this->assertFalse($result);
	}

	public function testMethodContainsAnyCase2() : void
	{
		$result = Str::containsAny(static::$_string, ['']);

		$this->assertFalse($result);
	}

	public function testMethodContainsAnyCase3() : void
	{
		$result = Str::containsAny(static::$_string, ['za', 'NoneExistingChar']);

		$this->assertFalse($result);
	}

	public function testMethodContainsAnyCase4() : void
	{
		$result = Str::containsAny(static::$_string, ['za', 'NoneExistingChar'], false);

		$this->assertTrue($result);
	}

	// Str::containsAll()

	public function testMethodContainsAllCase1() : void
	{
		$result = Str::containsAll(static::$_string, []);

		$this->assertFalse($result);
	}

	public function testMethodContainsAllCase2() : void
	{
		$result = Str::containsAll(static::$_string, ['']);

		$this->assertFalse($result);
	}

	public function testMethodContainsAllCase3() : void
	{
		$result = Str::containsAll(static::$_string, ['Za', 'NoneExistingChar']);

		$this->assertFalse($result);
	}

	public function testMethodContainsAllCase4() : void
	{
		$result = Str::containsAll(static::$_string, ['za', 'bm']);

		$this->assertFalse($result);
	}

	public function testMethodContainsAllCase5() : void
	{
		$result = Str::containsAll(static::$_string, ['za', 'bm'], false);

		$this->assertTrue($result);
	}

	// Str::hasLowerCase()

	public function testMethodHasLowerCaseCase1() : void
	{
		$string = 'ABC';

		$result = Str::hasLowerCase($string);

		$this->assertFalse($result);
	}

	public function testMethodHasLowerCaseCase2() : void
	{
		$string = 'Abc';

		$result = Str::hasLowerCase($string);

		$this->assertTrue($result);
	}

	public function testMethodHasLowerCaseCase3() : void
	{
		$string = 'bbc';

		$result = Str::hasLowerCase($string);

		$this->assertTrue($result);
	}

	// Str::hasUpperCase()

	public function testMethodHasUpperCaseCase1() : void
	{
		$string = 'ABC';

		$result = Str::hasUpperCase($string);

		$this->assertTrue($result);
	}

	public function testMethodHasUpperCaseCase2() : void
	{
		$string = 'Abc';

		$result = Str::hasUpperCase($string);

		$this->assertTrue($result);
	}

	public function testMethodHasUpperCaseCase3() : void
	{
		$string = 'bbc';

		$result = Str::hasUpperCase($string);

		$this->assertFalse($result);
	}

	// Str::chars()

	public function testMethodCharsCase1() : void
	{
		$string = 'ABC';

		$expected = [
			'A',
			'B',
			'C'
		];

		$result = Str::chars($string);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Str::lines()

	public function testMethodLinesCase1() : void
	{
		$string = "ABC\nDEF";

		$expected = [
			'ABC',
			'DEF'
		];

		$result = Str::lines($string);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	public function testMethodLinesCase2() : void
	{
		$string = "ABC\rDEF";

		$expected = [
			'ABC',
			'DEF'
		];

		$result = Str::lines($string);
		$compare = ($result === $expected);

		$this->assertTrue($compare);
	}

	// Str::explode()

	public function testMethodExplodeCase1() : void
	{
		$result = Str::explode(null, ',');

		$this->assertEquals([], $result);
	}

	public function testMethodExplodeCase2() : void
	{
		$result = Str::explode('0', ',');

		$this->assertEquals(['0'], $result);
	}

	public function testMethodExplodeCase3() : void
	{
		$expected = ['a', 'b', 'c'];
		$result = Str::explode(' a , b , c ', ',');

		$this->assertEquals($expected, $result);
	}

	public function testMethodExplodeCase4() : void
	{
		$expected = ['a', 'b'];
		$result = Str::explode(' a , b , c ', ',', 2);

		$this->assertEquals($expected, $result);
	}

	public function testMethodExplodeCase5() : void
	{
		$expected = ['b', 'c'];
		$result = Str::explode(' a , b , c ', ',', -2);

		$this->assertEquals($expected, $result);
	}

	public function testMethodExplodeCase6() : void
	{
		$result = Str::explode(' a , b , c ', ',', 0);

		$this->assertEquals([], $result);
	}

	// Str::shuffle()

	public function testMethodShuffleCase1() : void
	{
		$string = 'ABC';

		$possibleResults = [
			'ABC',
			'ACB',
			'BAC',
			'BCA',
			'CAB',
			'CBA'
		];

		$result = Str::shuffle($string);
		echo $result;
		$result = in_array($result, $possibleResults);

		$this->assertTrue($result);
	}

	// Str::random()

	public function testMethodRandomCase1() : void
	{
		$result = Str::random();

		$this->assertRegExp('/^[a-hj-km-np-zA-NP-Z2-9]{8}$/', $result);
	}

	public function testMethodRandomCase2() : void
	{
		$result = Str::random(10, 'alnum');

		$this->assertRegExp('/^[a-hj-km-np-zA-NP-Z2-9]{10}$/', $result);
	}

	public function testMethodRandomCase3() : void
	{
		$result = Str::random(10, 'alpha');

		$this->assertRegExp('/^[a-zA-Z]{10}$/', $result);
	}

	public function testMethodRandomCase4() : void
	{
		$result = Str::random(10, 'numeric');

		$this->assertRegExp('/^[0-9]{10}$/', $result);
	}

	public function testMethodRandomCase5() : void
	{
		$result = Str::random(10, 'nozero');

		$this->assertRegExp('/^[1-9]{10}$/', $result);
	}

	// Str::base64encode()

	public function testMethodBase64encodeCase1() : void
	{
		$string = 'Nat Withe';

		$result = Str::base64encode($string);

		$this->assertEquals('TmF0IFdpdGhl', $result);
	}

	// Str::base64decode()

	public function testMethodBase64decodeCase1() : void
	{
		$string = 'TmF0IFdpdGhl';

		$result = Str::base64decode($string);

		$this->assertEquals('Nat Withe', $result);
	}

	// Str::uuid()

	// Version 4 UUIDs have the form xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
	// where x is any hexadecimal digit and y is one of 8, 9, A, or B.
	//
	// ^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$
	//
	// To allow lowercase letters, use i modifier

	public function testMethodUuidCase1() : void
	{
		$pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

		$uuid = Str::uuid();

		if (preg_match($pattern, $uuid))
			$result = true;
		else
			$result = false;

		$this->assertTrue($result);
	}

	// Str::normalize()

	public function testMethodNormalizeCase1() : void
	{
		$string = 'ŅÀŦ ŴĨŦĤÈ';

		$result = Str::normalize($string);

		$this->assertEquals('NAT WITHE', $result);
	}
}