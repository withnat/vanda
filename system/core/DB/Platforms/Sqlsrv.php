<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @author		Vanda Dev Team
 * @copyright	Copyright (c) 2010 - 2019, Vanda, Inc. All rights reserved.
 * @license		Proprietary
 * @link		http://vanda.io
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
	protected static function _connect() : void
	{
	}
}