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
if(!isset($_subconfig)){
    $_subconfig = filter_input(INPUT_GET, '_subconfig');
}
foreach(array(
    $appName ? $appName : 'config',
    $_subconfig
) as $cfgFile){
    if(!is_null($cfgFile)){
        $cfgFile = FS::sanitizeFilename($cfgFile);
        $cfgFile = $aConfig['path']['cfg'] . '/config.' . $cfgFile . '.php';
        if(file_exists($cfgFile)){
            require_once $cfgFile;
        }else{
            throw new \Exception(
                sprintf(
                    "Missing configuration file '%s'",
                    $cfgFile
                )
            );
        }
    }
}

Registry::initialize();

// Initialize environment registry
Registry::addStorage('ENV');

// Store configuration to registry
Registry::addStorage('CFG')->set(Registry::ROOT, $aConfig, Registry::OVERWRITE);

// Remove all local variables
unset($aConfig, $appName, $appRoot, $cfgFile);
