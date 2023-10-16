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
 * @link         http://vanda.io
 */

declare(strict_types=1);

namespace Tests;

use System\Autoloader;

// Define a constant used within the framework.
// Cannot use the 'const' keyword in this bootstrap file because
// it is in a different namespace from the core files.

define('VD', 1);

define('DS', DIRECTORY_SEPARATOR);
define('PATH_BASE',  __DIR__ . DS . '..');
define('PATH_APP',  PATH_BASE . DS . 'app');
define('PATH_CONFIG',  PATH_BASE . DS . 'config');
define('PATH_STORAGE',  PATH_BASE . DS . 'storage');
define('PATH_SYSTEM',  PATH_BASE . DS . 'system');
define('PATH_THEME',  PATH_BASE . DS . 'themes');
define('PATH_VENDOR',  PATH_BASE . DS . 'vendor');
define('ENVIRONMENT',  'development'); // 'development', 'testing', 'staging' or 'production'

// Load the Vanda autoloader.

require PATH_SYSTEM . '/Autoloader.php';
require PATH_SYSTEM . '/common.php';

// Create the Composer autoloader.

$loader = require PATH_VENDOR . '/autoload.php';
$loader->unregister();

// Decorate Composer autoloader.
spl_autoload_register([new Autoloader($loader), 'loadClass'], true, true);
