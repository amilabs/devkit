<?php
/**
 * Application initialization.
 */
use AmiLabs\DevKit\Registry;
use AmiLabs\DevKit\Utility\FS;

// Application root
if(!isset($appRoot)){
    $appRoot = rtrim(realpath(dirname(__FILE__) . '/../../../../'), '/');
}

// Default project configuration.
$aConfig = array(
    'path' => array(
        'www' => $appRoot . '/web',                         // Document root
        'lib' => $appRoot . '/vendor',                      // Libraries
        'app' => rtrim($appRoot . '/app/' . $appName, '/'), // Application classes
        'tmp' => $appRoot . '/tmp',                         // Temporary files
        'log' => $appRoot . '/log',                         // Logs and other debug files
        'cfg' => $appRoot . '/cfg',                         // Custom config files
    )
);

// Include classes autoloader
require_once $aConfig['path']['lib'] . '/autoload.php';

// Check if local application config file is present.
// Can be overrided by "_subconfig" GET parameter
$cfgFile = isset($_GET['_subconfig']) ? $_GET['_subconfig'] : ($appName ? $appName : 'config');
$cfgFile = FS::sanitizeFilename($cfgFile);
$cfgFile = $aConfig['path']['cfg'] . '/config.' . $cfgFile . '.php';
if(file_exists($cfgFile)){
    require_once $cfgFile;
}else{
    throw new \Exception('Missing configuration file');
}

Registry::initialize();

// Initialize environment registry
Registry::addStorage('ENV');

// Store configuration to registry
Registry::addStorage('CFG')->set(Registry::ROOT, $aConfig, Registry::PERSIST);

// Remove all local variables
unset($aConfig, $appName, $appRoot, $cfgFile);
