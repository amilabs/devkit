<?php
/**
 * Project configuration.
 *
 * @todo Documentation how to make local config file.
 */
use AmiLabs\DevKit\Registry;

// Temporary
if(!isset($appName)){
    $appName = defined('APP_NAME') ? APP_NAME : '';
}

// Default project configuration.
$aConfig = array(
    'path' => array(
        // Document root
        'www' => dirname(__FILE__),
        // Libraries
        'lib' => realpath(dirname(__FILE__) . '/../vendor'),
        // Application classes
        'app' => realpath(rtrim(dirname(__FILE__) . '/../app/' . $appName, '/')),
        // Temporary files
        'tmp' => realpath(dirname(__FILE__) . '/../tmp'),
        // Logs and other debug files
        'log' => realpath(dirname(__FILE__) . '/../log'),
    )
);

// Check if local application config file is present. Will use default config if not.
$cfgFile = $appName ? $appName . '.' : '';
if(file_exists('config.' . $cfgFile . 'local.php')){
    require_once 'config.' . $cfgFile . 'local.php';
}elseif($cfgFile && file_exists('config.' . $cfgFile . 'php')){
    require_once 'config.' . $cfgFile . 'php';
}

require_once $aConfig['path']['lib'] . '/autoload.php';

// Initialize environment registry
Registry::addStorage('ENV');

// Store configuration to registry
Registry::addStorage('CFG')->set(Registry::ROOT, $aConfig, Registry::PERSIST);
unset($aConfig);
