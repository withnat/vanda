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
 * Trait for static method calls
 *
 * This is useful to make static method calls mockable in tests.
 *
 * This trait must not be used more than once in a class hierarchy,
 * otherwise endless call loops occur for parent method calls.
 * See https://bugs.php.net/bug.php?id=48770 for details.
 */
trait StaticCalling {
	/**
	 * Performs a static method call
	 *
	 * Note that parent::class should be used instead of 'parent'
	 * to refer to the actual parent class.
	 *
	 * @param string $classAndMethod Name of the class
	 * @param string $methodName Name of the method
	 * @param mixed $parameter,... Parameters to the method
	 * @return mixed
	 */
	protected function callStatic($className, $methodName) {
		$parameters = func_get_args();
		$parameters = array_slice($parameters, 2); // Remove $className and $methodName

		return call_user_func_array($className . '::' . $methodName, $parameters);
	}
}
