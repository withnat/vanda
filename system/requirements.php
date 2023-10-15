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

defined('VD') or exit('Access Denied');

/* Specify minimum version */

$minPPHPVersion = '7.2';
$minMySQLVersion = '5.5.3';
// JavaScript ECMAScript v.5.x (just note to remember)

/* Check system requirements */

$phpVerMsg = '';
$phpExtMsg = '';
$dbVerMsg = '';
$requirementMsg = '';
$permissionMsg = '';

// PHP

if (version_compare(PHP_VERSION, $minPPHPVersion) < 0)
{
	preg_match("#^\d+(\.\d+)*#", PHP_VERSION, $version);
	$phpversion = $version[0];

	$phpVerMsg .= '<li>PHP 7.2+, you are running ' . $phpversion . '</li>';
}

// MySQL

$dbconfig = config('db');
$dbconfigpath = $dbconfig[0];
$dbplatform = strtolower($dbconfig[1]['platform']);

switch ($dbplatform)
{
	case 'sqlite':

		$dbVerMsg .= '<li>Sqlite library has not yet been developed.</li>';
		break;

	case 'pgsql':

		$dbVerMsg .= '<li>Pgsql library has not yet been developed.</li>';
		break;

	case 'sqlsrv':

		$dbVerMsg .= '<li>Sqlsrv library has not yet been developed.</li>';
		break;

	case 'mysql':

		$output = shell_exec('mysql -V');
		preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
		$dbversion = $version[0];

		if (version_compare($dbversion, $minMySQLVersion) < 0)
			$dbVerMsg .= '<li>MySQL 5.5.3+ to use utf8mb4 Character Set, you are running ' . $dbversion . '</li>';

		break;

	default:

		$dbVerMsg .= '<li>' . ucfirst($dbplatform) . ' database platform in ' . $dbconfigpath . ' is not supported. Vanda is compatible with MySQL, SQLite, PostgreSQL and SQL Server.</li>';
}

// PHP Extension

$imageconfig = config('image');
$imageconfigpath = $imageconfig[0];
$imagedriver = strtolower($imageconfig[1]['driver']);

if (!in_array($imagedriver, ['gd', 'imagick']))
	$phpExtMsg .= '<li>' . ucfirst($imagedriver) . ' image driver in ' . $imageconfigpath . ' is not supported. Vanda is compatible with GD and Imagick.</li>';
elseif (!extension_loaded($imagedriver))
	$phpExtMsg .= '<li>' . $imagedriver . '</li>';

if (!extension_loaded('mbstring'))
	$phpExtMsg .= '<li>mbstring</li>';

if (in_array($dbplatform, ['mysql', 'sqlite', 'pgsql', 'sqlsrv']) and !extension_loaded('pdo_' . $dbplatform))
	$phpExtMsg .= '<li>pdo_' . $dbplatform . '</li>';

if ($phpVerMsg or $phpExtMsg or $dbVerMsg)
{
	$requirementMsg = '<h3>Vanda has a few system requirements:</h3>';
	$requirementMsg .= '<ul>';

	if ($phpVerMsg)
		$requirementMsg .= $phpVerMsg;

	if ($phpExtMsg)
	{
		if (!$phpVerMsg)
			$requirementMsg .= '<li>PHP Extentions</li>';

		$requirementMsg .= '<ul>' . $phpExtMsg . '</ul>';
	}

	$requirementMsg .= $dbVerMsg;
	$requirementMsg .= '</ul>';
}

$errorMsg = $requirementMsg;

/* Check path permissions */

if (!is_writable(PATH_APP . '/assets/'))
	$permissionMsg .= '<li>Asset directory ' . PATH_APP . DS . 'assets' . DS .' is not writeable by the webserver.</li>';

if (!is_writable(PATH_STORAGE))
	$permissionMsg .= '<li>Storage directory ' . PATH_STORAGE . DS . ' is not writeable by the webserver.</li>';

if ($permissionMsg)
	$errorMsg .= '<h3>Permissions</h3><ul>' . $permissionMsg . '</ul>';

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

/* Display error message */

if ($errorMsg)
{
	$path = PATH_THEMES . DS . 'system' . DS . 'error.php';

	if (is_file($path) and is_readable($path))
	{
		ob_start();

		include $path ;

		$content = ob_get_clean();
		$content = str_replace('{{main}}', $errorMsg, $content);
	}
	else
		$content = $errorMsg;

	die($content);
}

function config($config)
{
	$path = PATH_BASE . DS . 'config' . DS . ENV . DS . $config . '.php';

	if (!is_file($path) or !is_readable(($path)))
		$path = PATH_BASE . DS . 'config' . DS . $config . '.php';

	// Don't use include_once because Config.php need to include config file too!
	$config = include($path);

	$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);

	return [$path, $config];
}