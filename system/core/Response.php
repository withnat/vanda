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

use System\Exception\InvalidArgumentException;

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
	 * A map of integer HTTP 1.1 response codes to the full HTTP Status for the headers.
	 *
	 * @var array  An array of standard HTTP status codes and reason phrases.
	 * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 */
	protected static $_statuses = [
		// 1xx: Informational
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		103 => 'Early Hints',
		// 2xx: Success
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		// 3xx: Redirection
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // Moved Temporarily
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Proxy', // No longer used
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		// 4xx: Client error
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a Teapot',
		421 => 'Misdirected Request',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Too Early',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		451 => 'Unavailable For Legal Reasons',
		// 5xx: Server error
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
		511 => 'Network Authentication Required'
	];

	/**
	 * Stores the singleton instance of this class.
	 *
	 * @var Response  Instance of this class.
	 */
	private static $_instance;

	/**
	 * The current status code for this response.
	 *
	 * @var int   The HTTP status code.
	 */
	private static $_status = 200;

	/**
	 * @var array  An array of HTTP headers.
	 */
	private static $_headers = [];

	/**
	 * @var string  A body content of the response.
	 */
	private static $_body;

	/**
	 * Response constructor.
	 */
	private function __construct(){}

	/**
	 * Returns a reference to the global Response object,
	 * only creating it if it doesn't already exist.
	 *
	 * @return Response
	 */
	private static function _getInstance() : Response
	{
		if (is_null(Response::$_instance))
			Response::$_instance = new Response;

		return Response::_getInstance();
	}

	/**
	 * Get the current response status code.
	 *
	 * @return int  The current status code.
	 */
	public static function getStatus() : int
	{
		return Response::$_status;
	}

	/**
	 * Sets the response status code.
	 *
	 * @param  int $status  The status code.
	 * @return Response     Instance of $this to allow chaining.
	 */
	public static function setStatus(int $status) : Response
	{
		if (!array_key_exists($status, Response::$_statuses))
				throw InvalidArgumentException::valueError(1, '$status is not a valid HTTP return status code', $status);

		Response::$_status = $status;

		return Response::_getInstance();
	}

	/**
	 * Get a single header information to be sent
	 * when the response is sent to the client.
	 *
	 * If multiple headers with the same name exist,
	 * then will return an array of header objects.
	 *
	 * @param  string $name       The name of the header to get.
	 * @return string|array|null  The value of the header.
	 */
	public static function getHeader(string $name)
	{
		$values = [];

		foreach (Response::$_headers as $key => $value)
		{
			if ($key === $name)
				$values[] = $value;
		}

		switch (count($values))
		{
			case 0:
				return null;
				break;
			case 1:
				return $values[0];
				break;
			default:
				return $values;
		}
	}

	/**
	 * Get the array of header informations to be sent
	 * when the response is sent to the client.
	 *
	 * @return array
	 */
	public static function getHeaderList() : array
	{
		return Response::$_headers;
	}

	/**
	 * Set a header and it's value to the queue. If the replace flag is set
	 * then all headers with the given name will be replaced by the new one.
	 *
	 * Duplicate HTTP response headers are acceptable.
	 * see : https://stackoverflow.com/questions/4371328/are-duplicate-http-response-headers-acceptable
	 *
	 * @param  string   $name     The name of the header to set.
	 * @param  string   $value    The value of the header to set.
	 * @return Response           Instance of $this to allow chaining.
	 */
	public static function setHeader(string $name, string $value) : Response
	{
		Response::$_headers[] = [$name => $value];

		return Response::_getInstance();
	}

	/**
	 * Prepend a header and it's value to the queue.
	 *
	 * @param  string   $name   The name of the header to set.
	 * @param  string   $value  The value of the header to set.
	 * @return Response         Instance of $this to allow chaining.
	 */
	public static function prependHeader(string $name, string $value) : Response
	{
		unset(Response::$_headers[$name]);
		array_unshift(Response::$_headers, [$name, $value]);

		return Response::_getInstance();
	}

	/**
	 * Append a header and it's value to the queue.
	 *
	 * @param  string   $name   The name of the header to set.
	 * @param  string   $value  The value of the header to set.
	 * @return Response         Instance of $this to allow chaining.
	 */
	public static function appendHeader(string $name, string $value) : Response
	{
		unset(Response::$_headers[$name]);
		Response::$_headers[] = [$name, $value];

		return Response::_getInstance();
	}

	/**
	 * Removes a header from the list of headers.
	 *
	 * @param  string   $name  The header name
	 * @return Response        Instance of $this to allow chaining.
	 */
	public static function removeHeader(string $name) : Response
	{
		unset(Response::$_headers[$name]);

		return Response::_getInstance();
	}

	/**
	 * Method to clear any set response headers.
	 *
	 * @return Response  Instance of $this to allow chaining.
	 */
	public static function clearHeaders() : Response
	{
		Response::$_headers = [];

		return Response::_getInstance();
	}

	/**
	 * Return the body content.
	 *
	 * @return string  The body content.
	 */
	public static function getBody() : string
	{
		return Response::$_body;
	}

	/**
	 * Set body content. If body content already defined, this will replace it.
	 *
	 * @param  string   $content  The content to set as the response body.
	 * @return Response           Instance of $this to allow chaining.
	 */
	public static function setBody(string $content) : Response
	{
		Response::$_body = $content;

		return Response::_getInstance();
	}

	/**
	 * Prepend content to the body content.
	 *
	 * @param  string   $content  The content to prepend to the response body.
	 * @return Response           Instance of $this to allow chaining.
	 */
	public static function prependBody(string $content) : Response
	{
		Response::$_body = $content . Response::$_body;

		return Response::_getInstance();
	}

	/**
	 * Append content to the body content.
	 *
	 * @param  string   $content  The content to append to the response body.
	 * @return Response           Instance of $this to allow chaining.
	 */
	public static function appendBody(string $content) : Response
	{
		Response::$_body .= $content;

		return Response::_getInstance();
	}

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
