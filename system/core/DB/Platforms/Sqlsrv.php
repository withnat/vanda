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

namespace System\DB\Platforms;

use System\Config;
use System\DB\AbstractPlatform;
use PDO;
use PDOException;

/**
 * Class Sqlsrv
 * @package System\DB\Platforms
 */
final class Sqlsrv extends AbstractPlatform
{
	protected static $_delimitIdentifierLeft = '[';
	protected static $_delimitIdentifierRight = ']';

	protected static function _connect() : void
	{
	}
}
