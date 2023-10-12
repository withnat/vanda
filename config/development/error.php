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

		// Use a JSON library instead of json_encode()
		// to handle data that cannot be encoded.
		$content = System\Json::encode($data) . "\n";

		file_put_contents($path, $content, FILE_APPEND | LOCK_EX);
	}
];
