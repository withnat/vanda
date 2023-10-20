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

namespace Tests;

use System\Autoloader;

// Define a constant used within the framework.
// Cannot use the 'const' keyword in this bootstrap file because
// it is in a different namespace from the core files.

define('VD', 1);
define('ENVIRONMENT',  'development'); // 'development', 'testing', 'staging' or 'production'

define('PATH_BASE', str_replace('/' . basename(__DIR__), '', __DIR__));
define('PATH_ASSET', PATH_BASE . '/assets');
define('PATH_CONFIG', PATH_BASE . '/config');
define('PATH_LANGUAGE', PATH_BASE . '/languages');
define('PATH_PACKAGE', PATH_BASE . '/packages');
define('PATH_STORAGE', PATH_BASE . '/storage');

define('PATH_SYSTEM', PATH_BASE . '/system');
define('PATH_ASSET_SYSTEM', PATH_SYSTEM . '/assets');
define('PATH_LANGUAGE_SYSTEM', PATH_SYSTEM . '/languages');
define('PATH_PACKAGE_SYSTEM', PATH_SYSTEM . '/packages');

define('PACKAGE', 'user');
define('PATH_THEME', PATH_BASE . '/themes');
define('THEME_PATH', PATH_THEME . '/backend/vanda');

// Load the Vanda autoloader.

require PATH_BASE . '/system/Autoloader.php';
require PATH_BASE . '/system/common.php';

// Create the Composer autoloader.

$loader = require PATH_BASE . '/vendor/autoload.php';
$loader->unregister();

// Decorate Composer autoloader.
spl_autoload_register([new Autoloader($loader), 'loadClass'], true, true);
