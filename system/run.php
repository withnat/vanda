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

require PATH_SYSTEM . '/Autoloader.php';
require PATH_SYSTEM . '/common.php';

/* Create the Composer autoloader */

$loader = require PATH_VENDOR . '/autoload.php';
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

ini_set('session.cookie_lifetime', '86400'); //(Setting::get('lifetime', 30) * 60)
ini_set('session.gc_maxlifetime', '86400'); //(Setting::get('lifetime', 30) * 60)

// Debian/Ubuntu distro, by default PHP disables its session garbage collection mechanism
// (eg. the default php.ini contains the line ;session.gc_probability = 0 in Ubuntu).
// For every page request, there would be a 0.01% chance the Garbage collection method would be run.
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '1000');

ini_set('session.use_cookies', '1');

// It is also a good idea to make sure that PHP only uses cookies for
// sessions and disallow session ID passing as a GET parameter.
ini_set('session.use_only_cookies', '1');

// This is supposed to make session fixation attacks harder by not
// allowing an attacker to make up their own session IDs.
ini_set('session.use_strict_mode', '1'); // Available since PHP 5.5.2

// To prevent session hijacking through cross site scripting (XSS)
// you should always filter and escape all user supplied values before
// printing them to screen. However some bugs may slip through or a
// piece of legacy code might be vulnerable so it makes sense to also
// make use of browser protections against XSS.
//
// By specifying the HttpOnly flag when setting the session cookie you
// can tell a users browser not to expose the cookie to client side
// scripting such as JavaScript. This makes it harder for an attacker
// to hijack the session ID and masquerade as the effected user.
ini_set('session.cookie_httponly', '1'); // Available since PHP 5.2.0

// Another important way to increase the security of PHP sessions in
// your application is to install an SSL certificate on the web server
// and force all user interactions to occur over HTTPS only. This will
// prevent the users session ID from being transmitted in plain text
// to make it much harder to hijack the users session.
//if (isHttps)
//	ini_set('session.cookie_secure', 1);

//ini_set('session.hash_function', 1);
//ini_set('session.hash_bits_per_character', 4);
//ini_set('url_rewriter.tags', '');

// Log
//ini_set('log_errors', 1);
//ini_set('error_log', PATH_STORAGE . '/logs/error.log');

// Time zone
date_default_timezone_set(Setting::get('timezone', 'UTC'));

// Display errors
if (Config::app('env') === 'development')
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
$themePath = PATH_THEMES . DS . SIDE . DS . $themeName;

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

//echo File::getPermission('test/img.jpg');


echo System\Folder::getSize('xxx');

exit;

/* Module */

$module = '';
$controller = '';
$action = '';

if (SIDE === 'frontend')
{
	DB::table('Page')->where('status', 1);

	if ($uri)
	{
		$arr = explode('/', $uri);
		DB::where('alias', $arr[0]);
	}
	else
		DB::where('default', 1);

	$page = DB::load();

	if (is_object($page))
	{
		if ($page->module)
		{
			$module = $page->module;
			$controller = $page->controller;
			$action = ($page->action ? $page->action : 'index');
		}
		else
		{
			$module = Config::app('defaultFrontendModule', 'home');
			$controller = $module;
			$action = 'index';

			$_GET['id'] = $page->id;
		}
	}
}

if (!$module)
{
	if ($uri)
	{
		$segs = explode('/', $uri);

		if (SIDE === 'frontend')
			$module = (isset($segs[0]) ? $segs[0] : Config::app('defaultFrontendModule', 'home'));
		else
			$module = (isset($segs[0]) ? $segs[0] : Config::app('defaultBackendModule', 'dashboard'));

		if (isset($segs[2]))
		{
			$controller = $segs[1];
			$action = $segs[2];
		}
		elseif (isset($segs[1]))
		{
			$paths = [
				PATH_APP . DS . 'modules' . DS . SIDE . DS . $module . DS . 'controllers' . DS,
				PATH_SYSTEM . DS . 'modules' . DS . SIDE . DS . $module . DS . 'controllers' . DS
			];

			$file = Uri::toControllerFormat($segs[1]) . '.php';

			if (is_file($paths[0] . $file) or is_file($paths[1] . $file))
			{
				$controller = $segs[1];
				$action = 'index';
			}
			else
			{
				$controller = $module;
				$action = $segs[1];
			}
		}
		else
		{
			$controller = $module;
			$action = 'index';
		}
	}
	else
	{
		if (SIDE === 'frontend')
			$module = Config::app('defaultFrontendModule', 'home');
		else
			$module = Config::app('defaultBackendModule', 'dashboard');

		$controller = $module;
		$action = 'index';
	}
}

/* Define Constants */

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
