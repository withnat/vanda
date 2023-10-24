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
 * A lightweight & flexible PHP web framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         https://vanda.io
 */

declare(strict_types=1);

defined('VD') or exit('Access Denied');

const VERSION = '0.1.0';

const DS = DIRECTORY_SEPARATOR;
const PATH_ASSET = PATH_BASE . DS . 'assets';
const PATH_CONFIG = PATH_BASE . DS . 'config';
const PATH_LANGUAGE = PATH_BASE . DS . 'languages';
const PATH_PACKAGE = PATH_BASE . DS . 'packages';
const PATH_STORAGE = PATH_BASE . DS . 'storage';
const PATH_THEME = PATH_BASE . DS . 'themes';

const PATH_SYSTEM = PATH_BASE . DS . 'system';
const PATH_ASSET_SYSTEM = PATH_SYSTEM . DS . 'assets';
const PATH_LANGUAGE_SYSTEM = PATH_SYSTEM . DS . 'languages';
const PATH_PACKAGE_SYSTEM = PATH_SYSTEM . DS . 'packages';

use System\Arr;
use System\Auth;
use System\Autoloader;
use System\Config;
use System\DB;
use System\File;
use System\Request;
use System\Response;
use System\Router;
use System\Session;
use System\Str;
use System\Url;

$timestart = microtime(true);

/* Include startup files */

require PATH_SYSTEM . DS . 'Autoloader.php';
require PATH_SYSTEM . DS . 'common.php';

/* Create the Composer autoloader */

$loader = require PATH_BASE . DS . 'vendor/autoload.php';
$loader->unregister();

// Decorate Composer autoloader

spl_autoload_register([new System\Autoloader($loader), 'loadClass'], true, true);

/* Error handling */

set_exception_handler(['System\\Error', 'exception']);
set_error_handler(['System\\Error', 'native']);
register_shutdown_function(['System\\Error', 'shutdown']);

/* Check configuration file */
/*
if (!is_file(PATH_CONFIG . DS . 'db.php') or (filesize(PATH_CONFIG . DS . 'db.php') < 10))
{
	if (is_file('install/index.php'))
	{
		if (!is_writable(PATH_CONFIG))
		{
			throw new SystemRequirementException('Vanda config directory ' . PATH_CONFIG . DS . ' needs to be temporarily writable
				so we can create your application and database configuration files.');
		}

		header('Location: install/index.php');
	}
	else
		throw new SystemRequirementException('No configuration file found and no installation code available, exiting...');

	exit();
}
*/

/* PHP config */

mb_internal_encoding('UTF-8');

ini_set('arg_separator.output', '&amp;');
ini_set('auto_detect_line_endings', '1');
ini_set('session.cache_expire', '1440'); //Setting::get('lifetime', 30)

// Send an empty string to disable the cache limiter.
ini_set('session.cache_limiter', '');

// Server should keep session data for AT LEAST n seconds.
ini_set('session.cookie_lifetime', '86400'); //(Setting::get('lifetime', 30) * 60)
// Each client should remember their session id for EXACTLY n seconds.
ini_set('session.gc_maxlifetime', '86400'); //(Setting::get('lifetime', 30) * 60)

// More info about session lifetime
// https://thinkbolt.com/articles/php/sessions-in-php

// Debian/Ubuntu distro, by default PHP disables its session garbage collection mechanism
// (eg. the default php.ini contains the line ;session.gc_probability = 0 in Ubuntu).
// For every page request, there would be a 0.01% chance the Garbage collection method would be run.
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '1000');

// Another important way to increase the security of PHP sessions in
// your application is to install an SSL certificate on the web server
// and force all user interactions to occur over HTTPS only. This will
// prevent the users session ID from being transmitted in plain text
// to make it much harder to hijack the user session.
if (Request::isSecure())
	ini_set('session.cookie_secure', '1');

// Security is king

// The risk is that someone could give you link with sid, and you would
// use that link to login and them they would have active session where
// you have logged in.
ini_set('session.use_trans_sid', '0');

// This is how you want to manage your session id in client side, If set
// (default) the session id will be stored in cookies, otherwise it will
// be passed in url as a GET variable.
ini_set('session.use_cookies', '1');

// It is also a good idea to make sure that PHP only uses cookies for
// sessions and disallow session ID passing as a GET parameter.
ini_set('session.use_only_cookies', '1');

// This is supposed to make session fixation attacks harder by not
// allowing an attacker to make up their own session IDs.
ini_set('session.use_strict_mode', '1'); // Available since PHP 5.5.2

// To prevent session hijacking through cross site scripting (XSS)
// you should always filter and escape all user supplied values before
// printing them to screen. Howeverม some bugs may slip through or a
// piece of legacy code might be vulnerable so, it makes sense to also
// make use of browser protections against XSS.
//
// By specifying the HttpOnly flag when setting the session cookie you
// can tell a users browser not to expose the cookie to client side
// scripting such as JavaScript. This makes it harder for an attacker
// to hijack the session ID and masquerade as the effected user.
ini_set('session.cookie_httponly', '1'); // Available since PHP 5.2.0

// Time zone
date_default_timezone_set(Setting::get('timezone', 'UTC'));

// Display errors
if (Config::error('report'))
{
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
}
else
{
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
	error_reporting(0);
}

// Set the sessions to time out after a duration of inactivity by resetting the cookie expiration on reload.
session_set_cookie_params((int)(Config::get('lifetime', 30) * 60));

// Both options mentioned by others (session.gc_maxlifetime and session.cookie_lifetime)
// are not reliable. This link explains the reasons for that.
// http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
$vandaLastActivityTime = Session::get('__vandaLastActivityTime');
$currentTime = time();

if ($vandaLastActivityTime and $currentTime - $vandaLastActivityTime > Setting::get('lifetime', 30) * 60)
	Session::destroy();
else
	Session::set('__vandaLastActivityTime', $currentTime);

/* URL routing */

$uri = $_SERVER['PHP_SELF'];
$baseUri = $_SERVER['SCRIPT_NAME'];

$backendpath = Setting::get('backendpath', '/admin');
$backendpath = str_replace('/', '', $backendpath);

$haystack = substr_replace($uri, '', 0, strlen(Request::basePath()));
$needle = '/index.php/' . $backendpath;

if (stripos($haystack, $needle) === 0)
{
	$baseUri .= '/' . $backendpath;
	define('SIDE', 'backend');
}
else
	define('SIDE', 'frontend');

//

$themeName = Setting::get(strtolower(SIDE) . 'theme', 'vanda');
$themePath = PATH_THEME . DS . SIDE . DS . $themeName;

define('THEME_PATH', $themePath);

//

$uri = str_replace($baseUri, '', $uri); // remove /vanda/index.php from uri
$uri = trim($uri, '/'); // ie /about/

/* Language */

if (SIDE === 'frontend')
{
	$langDefault = '';
	$langCurrent = '';
	$langCurrentId = '';

	$langs = DB::table('Language')->where('status', 1)->sortDesc('default')->loadAll();
	$arr = explode('/', $uri);

	foreach ($langs as $lang)
	{
		if ($lang->{'default'})
		{
			$langDefault = $lang->sef;
			$langDefaultId = $lang->id;
		}

		// http://localhost/en/about
		if ($arr[0] === $lang->sef)
		{
			$langCurrent = $lang->sef;
			$langCurrentId = $lang->id;

			break;
		}
	}

	// http://localhost/about
	if (!$langCurrentId)
		$langCurrentId = $langDefaultId;

	if (!$uri and $langCurrent != $langDefault)
	{
		if ((int)\Setting::get('sef'))
			$prefix = '';
		else
			$prefix = '/index.php';

		$url = Url::base() . $prefix . '/' . $langDefault;
		Response::redirect($url);
	}

	if ($langCurrent)
		$uri = substr($uri, strlen($langCurrent) + 1); // +1 for slash ie 'en/'

	define('LANG', $langCurrent);
	define('LANG_ID', $langCurrentId);
}
else
{
	define('LANG', ''); 
	define('LANG_ID', '');
}

/* Module */

//$module = '';
//$controller = '';
//$action = '';
//
//if (SIDE === 'frontend')
//{
//	DB::table('Page')->where('status', 1);
//
//	if ($uri)
//	{
//		$arr = explode('/', $uri);
//		DB::where('alias', $arr[0]);
//	}
//	else
//		DB::where('default', 1);
//
//	$page = DB::load();
//
//	if (is_object($page))
//	{
//		if ($page->module)
//		{
//			$module = $page->module;
//			$controller = $page->controller;
//			$action = ($page->action ? $page->action : 'index');
//		}
//		else
//		{
//			$module = Config::app('defaultFrontendModule', 'home');
//			$controller = $module;
//			$action = 'index';
//
//			$_GET['id'] = $page->id;
//		}
//	}
//}
//
//if (!$module)
//{
//	if ($uri)
//	{
//		$segs = explode('/', $uri);
//
//		if (SIDE === 'frontend')
//			$module = (isset($segs[0]) ? $segs[0] : Config::app('defaultFrontendModule', 'home'));
//		else
//			$module = (isset($segs[0]) ? $segs[0] : Config::app('defaultBackendModule', 'dashboard'));
//
//		if (isset($segs[2]))
//		{
//			$controller = $segs[1];
//			$action = $segs[2];
//		}
//		elseif (isset($segs[1]))
//		{
//			$paths = [
//				PATH_APP . DS . 'modules' . DS . SIDE . DS . $module . DS . 'controllers' . DS,
//				PATH_SYSTEM . DS . 'modules' . DS . SIDE . DS . $module . DS . 'controllers' . DS
//			];
//
//			$file = Uri::toControllerFormat($segs[1]) . '.php';
//
//			if (is_file($paths[0] . $file) or is_file($paths[1] . $file))
//			{
//				$controller = $segs[1];
//				$action = 'index';
//			}
//			else
//			{
//				$controller = $module;
//				$action = $segs[1];
//			}
//		}
//		else
//		{
//			$controller = $module;
//			$action = 'index';
//		}
//	}
//	else
//	{
//		if (SIDE === 'frontend')
//			$module = Config::app('defaultFrontendModule', 'home');
//		else
//			$module = Config::app('defaultBackendModule', 'dashboard');
//
//		$controller = $module;
//		$action = 'index';
//	}
//}

$package = 'home';
$module = 'home';
$controller = 'home';
$action = 'index';

/* Define Constants */

define('PACKAGE', $package);
define('MODULE', $module);
define('CONTROLLER', $controller);
define('ACTION', $action);

$controller = Uri::toControllerFormat($controller);
$action = Uri::toActionFormat($action);

/* Run */

Autoloader::importModule($module, $controller);
$controller = new $controller();

// Execute preInit() before authentication checking.
$controller->preInit();

/* Check Authentication */

if (SIDE === 'backend')
{
	$passthruActions = Auth::getPassthru();

	if (empty(Auth::identity()->id) and $action != 'loginAction' and !Arr::has($passthruActions, $action, true))
	{
		$redirect = '';

		if (Request::isGet())
		{
			if (Request::get('redirect'))
				$url = Uri::route(Request::get('redirect'));
			else
				$url = Request::url();

			$redirect = '?redirect=' . Str::base64encode($url);
		}

		Response::redirect('user/login' . $redirect);
	}
}

$controller->init();
$controller->{$action}();
$controller->end();

if (Config::app('env') === 'development')
{
	$timeend = microtime(true);
	$totaltime = $timeend-$timestart;

//	echo '<p style="color:black; font-size:12px; font-family:tahoma; clear:both;">
//		Memory used: '.number_format((memory_get_usage()/1024), 2).' KB<br />
//		Parsing time: ' .number_format($totaltime, 3). ' seconds.<br />
//		Query: '.count($_query).'<ol>';
//		foreach ($_query as $query)
//			echo '<li>'.$query.'</li>';
//		echo '</ol></p>';
}

$timeend = microtime(true);
$totaltime = $timeend-$timestart;
//echo '<p>Parsing time: ' .number_format($totaltime, 3). ' seconds.<br />';
//echo 'Memory used: '.number_format((memory_get_usage()/1024), 2).' KB</p>';

class Setting
{
	public static function get($arg, $default = '')
	{
		return $default;
	}
}

class Uri
{
	public static function toControllerFormat($value)
	{
		return ucfirst($value) . 'Controller';
	}

	public static function toActionFormat($value)
	{
		return ucfirst($value) . 'Action';
	}
}
