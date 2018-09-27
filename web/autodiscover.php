<?php

/**
 * Generates autodiscover configuration for various email clients.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$config = new PhpAutodiscover\Config;
$config->load('../config.ini');

require_once __DIR__ . '/../app/bootstrap.php';
