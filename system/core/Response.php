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
 * Class Response
 *
 * The web Response class represents an HTTP response. It holds the $headers
 * and $content that is to be sent to the client. It also controls the HTTP
 * status code.
 *
 * @package System
 */
final class Response
{
	/**
	 * Response constructor.
	 */
	private function __construct(){}

	/**
	 * @param  string|null $url
	 * @return void
	 */
	public static function redirect(string $url = null) : void
	{
		if (isSPA() and Request::isAjax())
		{
			$data = [
				'title' => '',
				'content' => '',
				'redirect' => Uri::hashSPA($url)
			];

			echo json_encode($data);
			exit;
		}
		else
		{
			header('Location:' . Uri::route($url));
			exit;
		}
	}

	public static function spa($data)
	{
		$data = [
			'title' => @$data['title'],
			'content' => @$data['content'],
			'flash' => @$data['flash'],
			'redirect' => Uri::hashSPA(@$data['url'])
		];

		$data = json_encode($data);

		header('Expires: Mon, 27 Jul 1981 08:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');

		echo $data;
	}
}
