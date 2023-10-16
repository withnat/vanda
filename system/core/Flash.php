<?php
/*
 * __      __             _
 * \ \    / /            | |
 *  \ \  / /_ _ _ __   __| | __ _
 *   \ \/ / _` | '_ \ / _` |/ _` |
 *    \  / (_| | | | | (_| | (_| |
 *     \/ \__,_|_| |_|\__,_|\__,_|
 *
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace System;

/**
 * Class Flash
 *
 * This class is used to communicate information with the users of your
 * application, so they can know what happens as a result of their actions.
 *
 * @package System
 */
class Flash
{
	/**
	 * Config constructor.
	 */
	private function __construct(){}

	/**
	 * Sets a flash message of type info.
	 *
	 * @param string $message  The message to display.
	 * @return string|void     Returns the HTML code for the message if the request is AJAX,
	 *						   otherwise it sets the message in the session.
	 */
 	public static function info(string $message)
 	{
		if (Request::isAjax())
		{
			$html = '<div class="' . Config::core('closeFlashInfoMessageWrapperClass') . '">'
				. '<button aria-hidden="true" data-dismiss="alert" type="button" class="' . Config::core('closeFlashInfoMessageButtonClass') . '">×</button>'
				. $message . '</div>';

			return $html;
		}
		else
			Session::set('__vandaFlashInfo[]', $message);
 	}

	/**
	 * Sets a flash message of type success.
	 *
	 * @param string $message  The message to display.
	 * @return string|void     Returns the HTML code for the message if the request is AJAX,
	 *						   otherwise it sets the message in the session.
	 */
  	public static function success(string $message)
 	{
		if (Request::isAjax())
		{
			$html = '<div class="' . Config::core('closeFlashSuccessMessageWrapperClass') . '">';
			$html .= '<button aria-hidden="true" data-dismiss="alert" type="button" class="' . Config::core('closeFlashSuccessMessageButtonClass') . '">×</button>';
			$html .= $message . '</div>';

			return $html;
		}
		else
			Session::set('__vandaFlashSuccess[]', $message);
 	}

	/**
	 * Sets a flash message of type warning.
	 *
	 * @param string $message  The message to display.
	 * @return string|void     Returns the HTML code for the message if the request is AJAX,
	 *						   otherwise it sets the message in the session.
	 */
  	public static function warning(string $message)
 	{
		if (Request::isAjax())
		{
			$html = '<div class="' . Config::core('closeFlashWarningMessageWrapperClass') . '">';
			$html .= '<button aria-hidden="true" data-dismiss="alert" type="button" class="' . Config::core('closeFlashWarningMessageButtonClass') . '">×</button>';
			$html .= $message . '</div>';

			return $html;
		}
		else
			Session::set('__vandaFlashWarning[]', $message);
 	}

	/**
	 * Sets a flash message of type danger.
	 *
	 * @param string $message  The message to display.
	 * @return string|void     Returns the HTML code for the message if the request is AJAX,
	 *						   otherwise it sets the message in the session.
	 */
   	public static function danger(string $message)
 	{
		if (Request::isAjax())
		{
			$html = '<div class="' . Config::core('closeFlashDangerMessageWrapperClass') . '">';
			$html .= '<button aria-hidden="true" data-dismiss="alert" type="button" class="' . Config::core('closeFlashDangerMessageButtonClass') . '">×</button>';
			$html .= $message . '</div>';

			return $html;
		}
		else
			Session::set('__vandaFlashDanger[]', $message);
 	}

	/**
	 * Clears all flash messages.
	 *
	 * @return void
	 */
	public static function clear() : void
	{
		Session::clear('__vandaFlashInfo');
		Session::clear('__vandaFlashSuccess');
		Session::clear('__vandaFlashWarning');
		Session::clear('__vandaFlashDanger');
	}
}
