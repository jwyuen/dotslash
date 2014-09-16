<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('DOTSLASH_SRC_PATH', __DIR__ . '/src/');
define('DOTSLASH_FIXTURES_PATH', __DIR__ . '/tests/fixtures/');

// Loading dependencies
require_once(DOTSLASH_SRC_PATH . '/../vendor/autoload.php');
require_once(DOTSLASH_SRC_PATH . '/../vendor/composer/autoload_classmap.php');
