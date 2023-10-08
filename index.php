<?php
define('VD', 1);
define('VERSION', '0.1');

const DS = DIRECTORY_SEPARATOR;
const BASEPATH = __DIR__;
const PATH_ASSETS = BASEPATH . DS . 'assets';
const PATH_LANGUAGES = BASEPATH . DS . 'languages';
const PATH_PACKAGES = BASEPATH . DS . 'packages';
const PATH_SYSTEM = BASEPATH . DS . 'system';
const PATH_TEMPLATES = BASEPATH . DS . 'templates';
const PATH_TMP = BASEPATH . DS . 'tmp';
const PATH_VENDOR = BASEPATH . DS . 'vendor';

include 'system/run.php';
