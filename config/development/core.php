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
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license     MIT
 * @link        http://vanda.io
 */

return [
	'checked' => '<i class="fa fa-check text-navy"></i>',
	'unchcked' => '<i class="fa fa-check text-muted"></i>',
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
			'plugins/clockpicker/clockpicker.css'
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
			'plugins/bootstrap-markdown/bootstrap-markdown.js'
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
