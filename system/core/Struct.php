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

namespace System;

/**
 * Class Struct
 *
 * Using Example.
 *
 * // Define a 'coordinates' struct with 3 properties.
 * $coords = Struct::factory('degree', 'minute', 'pole');

 * // Create 2 latitude/longitude numbers.
 * $lat = $coords->create(35, 40, 'N');
 * $lng = $coords->create(139, 45, 'E');

 * // Use the different values by name.
 * echo $lat->degree . '° ' . $lat->minute . "' " . $lat->pole;
 * echo $lng->degree . '° ' . $lng->minute . "' " . $lng->pole;
 *
 * @package System
 */
class Struct
{
	/**
	 * Defines a new struct object, a blueprint object with only empty properties.
	 *
	 * @return Struct
	 */
	public static function factory() : Struct
	{
		$struct = new static;

		foreach (func_get_args() as $value)
			$struct->{$value} = null;

		return $struct;
	}
 
	/**
	 * Creates a new variable of the struct type $this.
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
