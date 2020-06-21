<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license     MIT
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace Tests\Unit;

use System\Str;
use PHPUnit\Framework\TestCase;

/**
 * Class StrTest
 * @package Tests\Unit
 * @see https://www.cl.cam.ac.uk/~mgk25/ucs/examples/quickbrown.txt
 */
final class StrTest extends TestCase
{
	protected static $_string = 'ABCDEF:MNRZ:/abcdef:mnrz';
	protected static $_text = 'Nat is so tall, and handsome as hell. Nat is so bad but he does it so well.';

	// Str::length

	public function testMethodLengthCase1() : void
	{
		$result = Str::length('');

		$this->assertEquals(0, $result);
	}

	public function testMethodLengthCase2() : void
	{
		$result = Str::length(static::$_string);

		$this->assertEquals(24, $result);
	}

	// Str::count

	public function testMethodCountCase1() : void
	{
		$result = Str::count(static::$_string, 'A');

		$this->assertEquals(1, $result);
	}

	public function testMethodCountCase2() : void
	{
		$result = Str::count(static::$_string, 'A', false);

		$this->assertEquals(2, $result);
	}

	// Str::countWords

	public function testMethodCountwordsCase1() : void
	{
		$result = Str::countWords(static::$_text);

		$this->assertEquals(18, $result);
	}

	// Str::left

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

		$this->assertEquals('ABCDEF:MNRZ', $result);
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

	// Str::right

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
		$result = Str::right(static::$_string, 5);

		$this->assertEquals(':mnrz', $result);
	}

	public function testMethodRightCase4() : void
	{
		$result = Str::right(static::$_string, -13);

		$this->assertEquals('abcdef:mnrz', $result);
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

	// Str::at

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
		$result = Str::at(static::$_string, -5);

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

	// Str::slice

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

		$this->assertEquals('ABCDEF:MNRZ', $result);
	}

	public function testMethodSliceCase5() : void
	{
		$result = Str::slice(static::$_string, 10, 4);

		$this->assertEquals('Z:/a', $result);
	}

	public function testMethodSliceCase6() : void
	{
		$result = Str::slice(static::$_string, 5, -10);

		$this->assertEquals('F:MNRZ:/a', $result);
	}

	public function testMethodSliceCase7() : void
	{
		$result = Str::slice(static::$_string, -8, 2);

		$this->assertEquals('de', $result);
	}

	public function testMethodSliceCase8() : void
	{
		$result = Str::slice(static::$_string, -8, -3);

		$this->assertEquals('def:m', $result);
	}

	// Str::limit
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

	// Str::limitWords

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

	// Str::position

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

		$this->assertEquals(11, $result);
	}

	// Str::lastPosition

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

		$this->assertEquals(19, $result);
	}

	public function testMethodLastpositionCase4() : void
	{
		$result = Str::lastPosition(static::$_string, ':', -10);

		$this->assertEquals(11, $result);
	}

	// Str::between

	public function testMethodBetweenCase1() : void
	{
		$result = Str::between(static::$_string, 'F', 'M');

		$this->assertEquals(':', $result);
	}

	public function testMethodBetweenCase2() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Str::between(static::$_string, ':', ':', -10);
	}

	public function testMethodBetweenCase3() : void
	{
		$result = Str::between(static::$_string, ':', ':', 10);

		$this->assertEquals('/abcdef', $result);
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

	// Str::trim

	public function testMethodTrimCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

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

	// Str::trimLeft

	public function testMethodTrimLeftCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

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
		$string = 'bbxa,ayb';

		$result = Str::trimLeft($string, 'b');

		$this->assertEquals('xa,ayb', $result);
	}

	// Str::trimRight

	public function testMethodTrimRightCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

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

	// Str::addSlashes

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

	// Str::stripSlashes

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

	// Str::htmlEncode

	public function testMethodHtmlEncodeCase1() : void
	{
		$string = '<strong>Nat</strong>';

		$result = Str::htmlEncode($string);

		$this->assertEquals('&lt;strong&gt;Nat&lt;/strong&gt;', $result);
	}

	// Str::htmlDecode

	public function testMethodHtmlDecodeCase1() : void
	{
		$string = '&lt;strong&gt;Nat&lt;/strong&gt;';

		$result = Str::htmlDecode($string);

		$this->assertEquals('<strong>Nat</strong>', $result);
	}

	// Str::removeWhitespace

	public function testMethodRemoveWhitespaceCase1() : void
	{
		$string = " a\tb\nc\rd\0e\x0Bf";

		$result = Str::removeWhitespace($string);

		$this->assertEquals('abcdef', $result);
	}

	// Str::removeTags

	public function testMethodRemoveTagsCase1() : void
	{
		$string = '<strong>Nat</strong>';

		$result = Str::removeTags($string);

		$this->assertEquals('Nat', $result);
	}

	// Str::removeQuotes

	public function testMethodRemoveQuotesCase1() : void
	{
		$string = '"Nat"';

		$result = Str::removeQuotes($string);

		$this->assertEquals('Nat', $result);
	}

	public function testMethodRemoveQuotesCase2() : void
	{
		$string = '"Nat"';

		$result = Str::removeQuotes($string);

		$this->assertEquals('Nat', $result);
	}

	// Str::removeInvisibleCharacters

	public function testMethodRemoveInvisibleCharactersCase1() : void
	{
		$string = "http://www.some-site.com//index.php\0";

		$result = Str::removeInvisibleCharacters($string, true);

		$this->assertEquals('http://www.some-site.com//index.php', $result);
	}

	// Str::removeLeft

	public function testMethodRemoveLeftCase1() : void
	{
		$result = Str::removeLeft(static::$_string, 'NoneExistingChar');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodRemoveLeftCase2() : void
	{
		$result = Str::removeLeft(static::$_string, 'ABCDEF');

		$this->assertEquals(':MNRZ:/abcdef:mnrz', $result);
	}

	// Str::removeRight

	public function testMethodRemoveRightCase1() : void
	{
		$result = Str::removeRight(static::$_string, 'NoneExistingChar');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodRemoveRightCase2() : void
	{
		$result = Str::removeRight(static::$_string, 'mnrz');

		$this->assertEquals('ABCDEF:MNRZ:/abcdef:', $result);
	}

	// Str::reduceDoubleSpaces

	public function testMethodReduceDoubleSpacesCase1() : void
	{
		$string = 'A  B      C';

		$result = Str::reduceDoubleSpaces($string);

		$this->assertEquals('A B C', $result);
	}

	// Str::reduceDoubleSlashes

	public function testMethodReduceDoubleSlashesCase1() : void
	{
		$string = 'http://www.some-site.com//index.php';

		$result = Str::reduceDoubleSlashes($string);

		$this->assertEquals('http://www.some-site.com/index.php', $result);
	}

	// Str::lowerCase

	public function testMethodLowerCaseCase1() : void
	{
		$string = 'I LOVE YOU';

		$result = Str::lowerCase($string);

		$this->assertEquals('i love you', $result);
	}

	// Str::lowerCaseFirst

	public function testMethodLowerCaseFirstCase1() : void
	{
		$string = 'I LOVE YOU';

		$result = Str::lowerCaseFirst($string);

		$this->assertEquals('i LOVE YOU', $result);
	}

	// Str::lowerCaseWords

	public function testMethodLowerCaseWordsCase1() : void
	{
		$string = 'I LOVE YOU';

		$result = Str::lowerCaseWords($string);


		$this->assertEquals('i lOVE yOU', $result);
	}

	// Str::upperCase

	public function testMethodUpperCaseCase1() : void
	{
		$string = 'i love you';

		$result = Str::upperCase($string);

		$this->assertEquals('I LOVE YOU', $result);
	}

	// Str::upperCaseFirst

	public function testMethodUpperCaseFirstCase1() : void
	{
		$string = 'i love you';

		$result = Str::upperCaseFirst($string);

		$this->assertEquals('I love you', $result);
	}

	// Str::upperCaseWords

	public function testMethodUpperCaseWordsCase1() : void
	{
		$string = 'i love you';

		$result = Str::upperCaseWords($string);

		$this->assertEquals('I Love You', $result);
	}

	// Str::repeat

	public function testMethodRepeatCase1() : void
	{
		$string = 'A';

		$result = Str::repeat($string, 3);

		$this->assertEquals('AAA', $result);
	}

	// Str::replace

	public function testMethodReplaceCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Str::replace('string', 'search', 3.14);
	}

	public function testMethodReplaceCase2() : void
	{
		$this->expectException(\InvalidArgumentException::class);

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

		$this->assertEquals('|BCDEF:MNRZ:/abcdef:mnrz', $result);
	}

	public function testMethodReplaceCase5() : void
	{
		$result = Str::replace(static::$_string, ':', '|', 1);

		$this->assertEquals('ABCDEF|MNRZ:/abcdef:mnrz', $result);
	}

	public function testMethodReplaceCase6() : void
	{
		$result = Str::replace(static::$_string, [':', '/'], ['|', '-']);

		$this->assertEquals('ABCDEF|MNRZ|-abcdef|mnrz', $result);
	}

	public function testMethodReplaceCase7() : void
	{
		$result = Str::replace(static::$_string, 'x', 'y', 1);

		$this->assertEquals(static::$_string, $result);
	}

	// Str::replaceFirst

	public function testMethodReplaceFirstCase1() : void
	{
		$result = Str::replaceFirst(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodReplaceFirstCase2() : void
	{
		$result = Str::replaceFirst(static::$_string, ':', '|');

		$this->assertEquals('ABCDEF|MNRZ:/abcdef:mnrz', $result);
	}

	// Str::replaceLast

	public function testMethodReplaceLastCase1() : void
	{
		$result = Str::replaceLast(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodReplaceLastCase2() : void
	{
		$result = Str::replaceLast(static::$_string, ':', '|');

		$this->assertEquals('ABCDEF:MNRZ:/abcdef|mnrz', $result);
	}

	// Str::ireplace

	public function testMethodIReplaceCase1() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Str::ireplace('string', 'search', 3.14);
	}

	public function testMethodIReplaceCase2() : void
	{
		$this->expectException(\InvalidArgumentException::class);

		Str::ireplace('string', 3.14, 'replace');
	}

	public function testMethodIReplaceCase3() : void
	{
		$result = Str::ireplace(static::$_string, 'a', '|');

		$this->assertEquals('|BCDEF:MNRZ:/|bcdef:mnrz', $result);
	}

	public function testMethodIReplaceCase4() : void
	{
		$result = Str::ireplace(static::$_string, 'a', '|', 1);

		$this->assertEquals('|BCDEF:MNRZ:/abcdef:mnrz', $result);
	}

	public function testMethodIReplaceCase5() : void
	{
		$result = Str::ireplace(static::$_string, ['a', 'b'], ['4', '8']);

		$this->assertEquals('48CDEF:MNRZ:/48cdef:mnrz', $result);
	}

	public function testMethodIReplaceCase6() : void
	{
		$result = Str::ireplace(static::$_string, 'x', 'y', 1);

		$this->assertEquals(static::$_string, $result);
	}

	// Str::ireplaceFirst

	public function testMethodIReplaceFirstCase1() : void
	{
		$result = Str::ireplaceFirst(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodIReplaceFirstCase2() : void
	{
		$result = Str::ireplaceFirst(static::$_string, 'a', '|');

		$this->assertEquals('|BCDEF:MNRZ:/abcdef:mnrz', $result);
	}

	// Str::ireplaceLast

	public function testMethodIReplaceLastCase1() : void
	{
		$result = Str::ireplaceLast(static::$_string, '', '|');

		$this->assertEquals(static::$_string, $result);
	}

	public function testMethodIReplaceLastCase2() : void
	{
		$result = Str::ireplaceLast(static::$_string, 'a', '|');

		$this->assertEquals('ABCDEF:MNRZ:/|bcdef:mnrz', $result);
	}

	// Str::subreplace

	public function testMethodSupreplaceCase1() : void
	{
		$result = Str::subreplace(static::$_string, '_____', 5);

		$this->assertEquals('ABCDE_____', $result);
	}

	public function testMethodSupreplaceCase2() : void
	{
		$result = Str::subreplace(static::$_string, '_____', 5, 5);

		$this->assertEquals('ABCDE_____Z:/abcdef:mnrz', $result);
	}

	public function testMethodSupreplaceCase3() : void
	{
		$result = Str::subreplace(static::$_string, '_____', 5, -5);

		$this->assertEquals('ABCDE_____:mnrz', $result);
	}

	public function testMethodSupreplaceCase4() : void
	{
		$result = Str::subreplace(static::$_string, '_____', -5);

		$this->assertEquals('ABCDEF:MNRZ:/abcdef_____', $result);
	}

	public function testMethodSupreplaceCase5() : void
	{
		$result = Str::subreplace(static::$_string, '_____', -5, 5);

		$this->assertEquals('ABCDEF:MNRZ:/abcdef_____', $result);
	}

	public function testMethodSupreplaceCase6() : void
	{
		$result = Str::subreplace(static::$_string, '_____', -15, -5);

		$this->assertEquals('ABCDEF:MN_____:mnrz', $result);
	}

	public function testMethodSupreplaceCase7() : void
	{
		$result = Str::subreplace(static::$_string, '_____', 100, 5);

		$this->assertEquals('ABCDEF:MNRZ:/abcdef:mnrz_____', $result);
	}

	// Str::insert

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
}