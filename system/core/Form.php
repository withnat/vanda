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

namespace System;

/**
 * Class Form
 *
 * The Form class manages form functionality for the framework, allowing
 * easy creation, validation, and processing of HTML forms.
 *
 * @package System
 */
class Form
{
	protected static $_formId;

	/**
	 * Creates a form open tag with the necessary attributes.
	 *
	 * @param string|null       $action   The URL or package action to submit the form to. Defaults to null.
	 *                                    If null, the form will submit to the current package's saveAction().
	 * @param string|array|null $attribs  Additional attributes for the form tag. Defaults to null.
	 * @return string                     Returns the form open tag.
	 */
	public static function open(?string $action = null, $attribs = null) : string
	{
		$action = trim((string)$action);

		if (stripos($action, 'http://') === false and stripos($action, 'https://') === false)
		{
			if (!$action)
				$action = 'save';

			$action = Url::createFromAction($action);
		}

		$id = Html::getAttribute($attribs, 'id');
		$name = Html::getAttribute($attribs, 'name');

		if (!$id and !$name)
		{
			$id = 'frm-default';
			$name = 'frm-default';
		}
		elseif ($id)
			$name = $id;
		elseif ($name)
			$id = $name;

		static::$_formId = $id;

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'action', $action);
		$attribs = Html::setAttribute($attribs, 'method', 'post');
		$attribs = Html::setAttribute($attribs, 'class', 'form-horizontal form');
		$attribs = Html::setAttribute($attribs, 'enctype', 'multipart/form-data');

		$html = '<form ' . $attribs . '>';

		return $html;
	}
}
