<?php
/**
 * Frontend
 */

use \AmiLabs\DevKit\Application;

define('APP_NAME', 'frontend');

require_once 'config.php';

$oApp = Application::getInstance();
$oApp->run();