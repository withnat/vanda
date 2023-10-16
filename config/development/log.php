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
	/**
	 * Error Logging Threshold
	 *
	 * You can enable error logging by setting a threshold over zero. The
	 * threshold determines what gets logged. Any values below or equal to the
	 * threshold will be logged.
	 *
	 * Threshold options are:
	 *
	 * 0 = Disables logging, Error logging TURNED OFF
	 * 1 = Emergency Messages - System is unusable
	 * 2 = Alert Messages - Action Must Be Taken Immediately
	 * 3 = Critical Messages - Application component unavailable, unexpected exception.
	 * 4 = Runtime Errors - Don't need immediate action, but should be monitored.
	 * 5 = Warnings - Exceptional occurrences that are not errors.
	 * 6 = Notices - Normal but significant events.
	 * 7 = Info - Interesting events, like user logging in, etc.
	 * 8 = Debug - Detailed debug information.
	 * 9 = All Messages
	 *
	 * You can also pass an array with threshold levels to show individual error types
	 *
	 *     [1, 2, 3, 8] = Emergency, Alert, Critical, and Debug messages
	 */
	'threshold' => (ENVIRONMENT === 'production' ? 4 : 9)
];
