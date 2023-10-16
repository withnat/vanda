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

return [
	'report' => true,
	'log' => function($e)
	{
		$data = [
			'date' => date('Y-m-d H:i:s'),
			'message' => $e->getMessage(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'trace' => $e->getTrace()
		];

		$path = PATH_STORAGE . DS . 'logs' . DS . 'errors.log';

		// Use JSON lib instead of json_encod()
		// to remove data that cannot be encoded.
		$content = System\JSON::encode($data) . "\n";

		file_put_contents($path, $content, FILE_APPEND | LOCK_EX);
	}
];
