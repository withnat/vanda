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
class Response
{
	/**
	 * A map of integer HTTP 1.1 response codes to the full HTTP Status for the headers.
	 *
	 * @var array  An array of standard HTTP status codes and reason phrases.
	 * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
	 */
	protected static $_statuses = [
		// 1xx: Informational
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing', // WebDAV; RFC 2518
		103 => 'Early Hints', // RFC 8297
		// 2xx: Success
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information', // since HTTP/1.1
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content', // RFC 7233
		207 => 'Multi-Status', // WebDAV; RFC 4918
		208 => 'Already Reported', // WebDAV; RFC 5842
		226 => 'IM Used', // RFC 3229
		// 3xx: Redirection
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // Moved Temporarily
		303 => 'See Other', // since HTTP/1.1
		304 => 'Not Modified', // RFC 7232
		305 => 'Use Proxy', // since HTTP/1.1
		306 => 'Switch Proxy', // No longer used
		307 => 'Temporary Redirect', // since HTTP/1.1
		308 => 'Permanent Redirect', // RFC 7538
		// 4xx: Client error
		400 => 'Bad Request',
		401 => 'Unauthorized', // RFC 7235
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required', // RFC 7235
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed', // RFC 7232
		413 => 'Request Entity Too Large', // RFC 7231
		414 => 'Request-URI Too Long', // RFC 7231
		415 => 'Unsupported Media Type', // RFC 7231
		416 => 'Requested Range Not Satisfiable', // RFC 7233
		417 => 'Expectation Failed',
		418 => 'I\'m a Teapot', // RFC 2324, RFC 7168
		421 => 'Misdirected Request', // RFC 7540
		422 => 'Unprocessable Entity', // WebDAV; RFC 4918
		423 => 'Locked', // WebDAV; RFC 4918
		424 => 'Failed Dependency', // WebDAV; RFC 4918
		425 => 'Too Early', // RFC 8470
		426 => 'Upgrade Required',
		428 => 'Precondition Required', // RFC 6585
		429 => 'Too Many Requests', // RFC 6585
		431 => 'Request Header Fields Too Large', // RFC 6585
		451 => 'Unavailable For Legal Reasons', // RFC 7725
		// 5xx: Server error
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates', // RFC 2295
		507 => 'Insufficient Storage', // WebDAV; RFC 4918
		508 => 'Loop Detected', // WebDAV; RFC 5842
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended', // RFC 2774
		511 => 'Network Authentication Required' // RFC 6585
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
	private static $_statusCode = 200;

	/**
	 * The current reason phrase for this response.
	 *
	 * @var string  The HTTP status reason phrase.
	 */
	private static $_statusReason;

	/**
	 * @var array  An array of HTTP headers.
	 */
	private static $_headers = [];

	/**
	 * @var string  A body content of the response.
	 */
	private static $_body = '';

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

		return Response::$_instance;
	}

	/**
	 * Get the current response status code.
	 *
	 * @return int  The current status code.
	 */
	public static function getStatusCode() : int
	{
		return Response::$_statusCode;
	}

	/**
	 * Gets the response response phrase associated with the status code.
	 *
	 * @return string  The HTTP status reason phrase.
	 */
	public static function getStatusReason() : string
	{
		if (!Response::$_statusReason)
			Response::$_statusReason = Response::$_statuses[Response::$_statusCode];

		return Response::$_statusReason;
	}

	/**
	 * Sets the response status code.
	 *
	 * @param  int $statusCode  The status code.
	 * @return Response         Instance of $this to allow chaining.
	 */
	public static function setStatusCode(int $statusCode) : Response
	{
		if (!array_key_exists($statusCode, Response::$_statuses))
				throw InvalidArgumentException::valueError(1, '$statusCode is not a valid HTTP return status code', $statusCode);

		Response::$_statusCode = $statusCode;
		Response::$_statusReason = Response::$_statuses[$statusCode];

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

		foreach (Response::$_headers as $header)
		{
			if ($header[0] === $name)
				$values[] = $header[1];
		}

		switch (count($values))
		{
			case 0:
				return null;
			case 1:
				return $values[0];
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
	 * @param  string   $name   The name of the header to set.
	 * @param  string   $value  The value of the header to set.
	 * @return Response         Instance of $this to allow chaining.
	 */
	public static function setHeader(string $name, string $value) : Response
	{
		Response::$_headers[] = [$name, $value];

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
		array_unshift(Response::$_headers, [$name, $value]);

		return Response::_getInstance();
	}

	/**
	 * Append a header and it's value to the queue.
	 * An alias for the Response::setHeader() method.
	 *
	 * @param  string   $name   The name of the header to set.
	 * @param  string   $value  The value of the header to set.
	 * @return Response         Instance of $this to allow chaining.
	 */
	public static function appendHeader(string $name, string $value) : Response
	{
		return Response::setHeader($name, $value);
	}

	/**
	 * Removes a header from the list of headers.
	 *
	 * @param  string   $name  The header name.
	 * @return Response        Instance of $this to allow chaining.
	 */
	public static function removeHeader(string $name) : Response
	{
		foreach (Response::$_headers as $key => $header)
		{
			if ($header[0] === $name)
				unset(Response::$_headers[$key]);
		}

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
	 * Method to clear the body content.
	 *
	 * @return Response  Instance of $this to allow chaining.
	 */
	public static function clearBody() : Response
	{
		Response::$_body = '';

		return Response::_getInstance();
	}

	/**
	 * Redirect to another URL.
	 *
	 * If the headers have not been sent the redirect will be accomplished using
	 * a "303 See Other" code in the header pointing to the new location. If the
	 * headers have already been sent this will be accomplished using a JavaScript
	 * statement.
	 *
	 * Why 303?
	 *
	 *  - A 302 redirect indicates that the redirect is temporary, clients should
	 *    check back at the original URL in future requests.
	 *  - A 303 redirect is meant to redirect a POST request to a GET resource
	 *    (otherwise, the client assumes that the request method for the new
	 *    location is the same as for the original resource).
	 *
	 * see : https://serverfault.com/questions/391181/examples-of-302-vs-303
	 *
	 * @param  string|null $url         The URI to redirect to.
	 * @param  int         $statusCode  The type of redirection, defaults to 303.
	 * @return void
	 */
	public static function redirect(string $url = null, int $statusCode = 303) : void
	{
		if (!array_key_exists($statusCode, static::$_statuses))
			throw InvalidArgumentException::valueError(2, '$statusCode is not a valid HTTP return status code', $statusCode);

		// Scrub all output buffer before we redirect.
		// The ob_get_level() function indicates how many output buffers are
		// currently on the stack. PHP may be configured to automatically
		// create an output buffer when the script begins, which is why the
		// buffer level may be 1 without calling ob_start().
		// @see http://www.mombu.com/php/php/t-output-buffering-and-zlib-compression-issue-3554315-last.html
		// @codeCoverageIgnoreStart
		while (ob_get_level() > 1)
			static::clean();
		// @codeCoverageIgnoreEnd

		if (App::isSpa() and Request::isAjax())
		{
			$data = [
				'title' => '',
				'content' => '',
				'redirect' => Url::hashSPA($url)
			];

			echo json_encode($data);
		}
		else
		{
			$url = Url::create($url);

			if (static::isHeadersSent())
			{
				$url = str_replace("'", '&apos;', $url);

				echo '<script>document.location.href="' . $url . "\";</script>\n";
			}
			else
			{
				// The refresh header works better on certain servers like IIS.
				if (strpos((string)Request::server('SERVER_SOFTWARE'), 'IIS') !== false)
					static::setHeader('Refresh', '0; url=' . $url);
				else
					static::setHeader('Location', $url);

				static::setStatusCode($statusCode);
			}
		}
	}

	/**
	 * Sends the output to the browser.
	 *
	 * @return Response
	 */
	public static function send() : Response
	{
		static::sendHeaders();
		static::sendBody();

		return Response::_getInstance();
	}

	/**
	 * Send all headers of this HTTP request to the browser.
	 *
	 * @return Response
	 */
	public static function sendHeaders() : Response
	{
		// Don't send headers for CLI.
		if (Request::isCli())
			return Response::_getInstance();

		// Always make sure we send the content type.

		$hasContentType = false;

		// If array is not empty.
		if (Response::$_headers)
		{
			foreach (Response::$_headers as $header)
			{
				if (strtolower($header[0]) === 'content-type')
				{
					$hasContentType = true;
					break;
				}
			}
		}

		if (!$hasContentType)
			static::setHeader('content-type', 'text/html; charset=' . Config::app('charset', 'UTF-8'));

		// Output headers.

		foreach (Response::$_headers as $header)
			header($header[0] . ': ' . $header[1]);

		return Response::_getInstance();
	}

	/**
	 * Sends the Body of the message to the browser.
	 *
	 * @return Response
	 */
	public static function sendBody() : Response
	{
		echo Response::$_body;

		return Response::_getInstance();
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

	/**
	 * Checks if or where headers have been sent.
	 * An alias for PHP's headers_sent() function.
	 *
	 * I am wrapping this to isolate the built-in PHP
	 * function from my code base for testing reasons.
	 *
	 * @return bool  True if the headers have already been sent.
	 * @see headers_sent()
	 * @codeCoverageIgnore
	 */
	protected static function isHeadersSent() : bool
	{
		return headers_sent();
	}
}
