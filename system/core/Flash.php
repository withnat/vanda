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
 * Class Flash
 *
 * This class is used to communicate information with the users of your
 * application, so they can know what happens as a result of their actions.
 *
 * @package System
 * @codeCoverageIgnore
 */
class Flash
{
	private function __construct(){}
 
 	public static function info(string $message) : ?string
 	{
		if (Request::isAjax())
		{
			$html = '<div class="' . Config::core('closeFlashInfoMessageWrapperClass') . '">
						<button aria-hidden="true" data-dismiss="alert" type="button" class="' . Config::core('closeFlashInfoMessageButtonClass') . '">
							×
						</button>'
						. $message
					. '</div>';

			return $html;
		}
		else
			Session::set('__vandaFlashInfo[]', $message);
 	}
 
  	public static function success(string $message) : ?string
 	{
	    if (Request::isAjax())
	    {
		    $html = '<div class="' . Config::core('closeFlashSuccessMessageWrapperClass') . '">
						<button aria-hidden="true" data-dismiss="alert" type="button" class="' . Config::core('closeFlashSuccessMessageButtonClass') . '">
							×
						</button>'
						. $message 
					. '</div>';

		    return $html;
	    }
	    else
		    Session::set('__vandaFlashSuccess[]', $message);
 	}
 
  	public static function warning(string $message) : ?string
 	{
		if (Request::isAjax())
		{
			$html = '<div class="' . Config::core('closeFlashWarningMessageWrapperClass') . '">
						<button aria-hidden="true" data-dismiss="alert" type="button" class="' . Config::core('closeFlashWarningMessageButtonClass') . '">
							×
						</button>'
						. $message
					. '</div>';

			return $html;
		}
		else
			Session::set('__vandaFlashWarning[]', $message);
 	}
 
   	public static function danger(string $message) : ?string
 	{
	    if (Request::isAjax())
	    {
		    $html = '<div class="' . Config::core('closeFlashDangerMessageWrapperClass') . '">
						<button aria-hidden="true" data-dismiss="alert" type="button" class="' . Config::core('closeFlashDangerMessageButtonClass') . '">
							×
						</button>'
		    			. $message
					. '</div>';

		    return $html;
	    }
	    else
		    Session::set('__vandaFlashDanger[]', $message);
 	}

	public static function clear() : void
	{
		Session::clear('__vandaFlashInfo');
		Session::clear('__vandaFlashSuccess');
		Session::clear('__vandaFlashWarning');
		Session::clear('__vandaFlashDanger');
	}
}
