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

namespace System;

/**
 * Class Struct
 * @package System
 */
class Struct
{
	/**
	 * Using Example
	 *
	 * // define a 'coordinates' struct with 3 properties
	 * $coords = Struct::factory('degree', 'minute', 'pole');

	 * // create 2 latitude/longitude numbers
	 * $lat = $coords->create(35, 40, 'N');
	 * $lng = $coords->create(139, 45, 'E');

	 * // use the different values by name
	 * echo $lat->degree . '° ' . $lat->minute . "' " . $lat->pole;
	 * echo $lng->degree . '° ' . $lng->minute . "' " . $lng->pole;
	 */

	/**
	 * Define a new struct object, a blueprint object with only empty properties.
	 *
	 * @return Struct
	 */
	public static function factory() : Struct
	{
		$struct = new self;

		foreach (func_get_args() as $value)
			$struct->{$value} = null;

		return $struct;
	}
 
	/**
	 * Create a new variable of the struct type $this.
	 *
	 * @return Struct
	 */
	public function create() : Struct
	{
		// Clone the empty blueprint-struct ($this) into the new data $struct.
		$struct = clone $this;

		// Populate the new struct.
		$properties = array_keys((array)$struct);

		foreach (func_get_args() as $key => $value)
		{
			if (!is_null($value))
				$struct->{$properties[$key]} = $value;
		}

		// Return the populated struct.
		return $struct;
	}
}
