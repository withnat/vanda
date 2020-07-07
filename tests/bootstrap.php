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
 * @package		Vanda
 * @author		Nat Withe <nat@withnat.com>
 * @copyright	Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license		MIT
 * @link		http://vanda.io
 */

declare(strict_types=1);

namespace Tests;

use System\Autoloader;

// Set constants used in Vanda.
putenv('APP_ENV=development');

define('VD', 1);
define('ENV', getenv('APP_ENV'));

define('DS', DIRECTORY_SEPARATOR);
define('PATH_BASE', __DIR__ . DS . '..');
define('PATH_APP', PATH_BASE . DS . 'app');
define('PATH_CONFIG', PATH_BASE . DS . 'config');
define('PATH_STORAGE', PATH_BASE . DS . 'storage');
define('PATH_SYSTEM', PATH_BASE . DS . 'system');
define('PATH_THEMES', PATH_BASE . DS . 'themes');
define('PATH_VENDOR', PATH_BASE . DS . 'vendor');

define('DEV_MODE', true);

// Load the Vanda autoloader.
require PATH_SYSTEM . '/Autoloader.php';
require PATH_SYSTEM . '/common.php';

// Create the Composer autoloader.
$loader = require PATH_VENDOR . '/autoload.php';
$loader->unregister();

// Decorate Composer autoloader.
spl_autoload_register([new Autoloader($loader), 'loadClass'], true, true);
