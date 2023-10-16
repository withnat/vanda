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

return [
	'checked' => '<i class="fa fa-check text-navy"></i>',
	'unchcked' => '<i class="fa fa-check text-muted"></i>',
	'closeFlashSuccessMessageWrapperClass' => 'alert alert-success alert-dismissable',
	'closeFlashSuccessMessageButtonClass' => 'close',
	'closeFlashDangerMessageWrapperClass' => 'alert alert-danger alert-dismissable',
	'closeFlashDangerMessageButtonClass' => 'close',
	'closeFlashWarningMessageWrapperClass' => 'alert alert-warning alert-dismissable',
	'closeFlashWarningMessageButtonClass' => 'close',
	'assets' => [
		'upload' => [
			'templates/backend/vanda/bootstrap-fileinput-master/css/fileinput.min.css',
			'templates/backend/vanda/bootstrap-fileinput-master/js/plugins/canvas-to-blob.min.js',
			'templates/backend/vanda/bootstrap-fileinput-master/js/plugins/sortable.min.js',
			'templates/backend/vanda/bootstrap-fileinput-master/js/plugins/purify.min.js',
			'templates/backend/vanda/bootstrap-fileinput-master/js/fileinput.min.js',
			'templates/backend/vanda/bootstrap-fileinput-master/themes/fa/theme.js'
		],
		'autocomplete' => [
			'plugins/chosen/bootstrap-chosen.css',
			'plugins/chosen/chosen.jquery.js'
		],
		'checkbox' => [
			'plugins/iCheck/custom.css',
			'plugins/iCheck/icheck.min.js'
		],
		'radio' => [
			'plugins/iCheck/custom.css',
			'plugins/iCheck/icheck.min.js'
		],
		'clockpicker' => [
			'plugins/clockpicker/clockpicker.css',
			'plugins/clockpicker/clockpicker.js'
		],
		'colorpicker' => [
			'plugins/colorpicker/bootstrap-colorpicker.min.css',
			'plugins/colorpicker/bootstrap-colorpicker.min.js'
		],
		'datepicker' => [
			'plugins/datapicker/datepicker3.css',
			// Date range use moment.js same as full calendar plugin.
			'plugins/fullcalendar/moment.min.js',
			'plugins/datepicker/bootstrap-datepicker.js'
		],
		'daterangepicker' => [
			'plugins/datapicker/datepicker3.css',
			// Date range use moment.js same as full calendar plugin.
			'plugins/fullcalendar/moment.min.js',
			'plugins/datepicker/bootstrap-datepicker.js',
			'plugins/daterangepicker/daterangepicker-bs3.css',
			'plugins/daterangepicker/daterangepicker.js'
		],
		'datatypechecker' => [
			'plugins/jasny/jasny-bootstrap.min.css',
			'plugins/jasny/jasny-bootstrap.min.js'
		],
		'editor' => [
			'plugins/summernote/summernote-bs4.css',
			'plugins/summernote/summernote-bs4.js'
		],
		'rangespin' => [
			'plugins/touchspin/jquery.bootstrap-touchspin.min.css',
			'plugins/touchspin/jquery.bootstrap-touchspin.min.js'
		],

		'markdown' => [
			'plugins/bootstrap-markdown/bootstrap-markdown.min.css',
			'plugins/bootstrap-markdown/bootstrap-markdown.js',
			'plugins/bootstrap-markdown/markdown.js'
		],
		'switcher' => [
			'plugins/switchery/switchery.min.css',
			'plugins/switchery/switchery.min.js'
		],
		'tagsinput' => [
			'plugins/bootstrap-tagsinput/bootstrap-tagsinput.css',
			'plugins/bootstrap-tagsinput/bootstrap-tagsinput.js'
		]
	]
];
