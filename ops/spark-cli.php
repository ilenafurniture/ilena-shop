<?php

// LSPHP runs web requests in LSAPI mode. Define CLI streams before bootstrapping
// Spark so the same PHP 8.3 runtime can safely run migrations and chat workers.
defined('STDIN') || define('STDIN', fopen('php://stdin', 'rb'));
defined('STDOUT') || define('STDOUT', fopen('php://stdout', 'wb'));
defined('STDERR') || define('STDERR', fopen('php://stderr', 'wb'));

require dirname(__DIR__) . '/spark';
