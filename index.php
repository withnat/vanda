<?php
define('VD', 1);

const DS = DIRECTORY_SEPARATOR;
const PATH_BASE = __DIR__;
const PATH_APP = PATH_BASE . DS . 'app';
const PATH_CONFIG = PATH_BASE . DS . 'config';
const PATH_STORAGE = PATH_BASE . DS . 'storage';
const PATH_SYSTEM = PATH_BASE . DS . 'system';
const PATH_THEME = PATH_BASE . DS . 'themes';
const PATH_VENDOR = PATH_BASE . DS . 'vendor';
const ENVIRONMENT = 'development'; // 'development', 'testing', 'staging' or 'production'

include 'system/run.php';
