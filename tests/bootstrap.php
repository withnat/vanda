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

namespace Tests;

use System\Autoloader;

// Set constants used in Vanda.
define('VD', 1);
define('ENV', 'development'); // 'development', 'test', 'staging' or 'production'
define('DS', DIRECTORY_SEPARATOR);
define('PATH_BASE', __DIR__ . DS . '..');
define('PATH_APP', PATH_BASE . DS . 'app');
define('PATH_CONFIG', PATH_BASE . DS . 'config');
define('PATH_STORAGE', PATH_BASE . DS . 'storage');
define('PATH_SYSTEM', PATH_BASE . DS . 'system');
define('PATH_THEMES', PATH_BASE . DS . 'themes');
define('PATH_VENDOR', PATH_BASE . DS . 'vendor');

// Load the Vanda autoloader.
require PATH_SYSTEM . '/Autoloader.php';
require PATH_SYSTEM . '/common.php';

// Create the Composer autoloader.
$loader = require PATH_VENDOR . '/autoload.php';
$loader->unregister();

// Decorate Composer autoloader.
spl_autoload_register([new Autoloader($loader), 'loadClass'], true, true);
