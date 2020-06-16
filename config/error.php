<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @author		Webster Solutions Dev Team
 * @copyright	Copyright (c) 2010 - 2019, webster:Solutions (http://webster.solutions)
 * @license		Webster Solutions License
 * @link		http://vanda.io
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
