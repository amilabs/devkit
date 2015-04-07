<?php

namespace AmiLabs\DevKit;

/**
 * Class for logging debug information.
 */
class Logger {
    /**
     * Logfile delimiter for long records
     */
    const DELIMITER = '---';
    /**
     * Array of loggers
     *
     * @var array
     */
    private static $aLoggers = array();
    /**
     * Is logger active
     *
     * @var bool
     */
    private $active = FALSE;
    /**
     * Filename for a current logger
     *
     * @var string
     */
    private $logFile;
    /**
     * Constructor.
     *
     * @param string $logFile    Filename to log debug data
     * @param boolean $bRewrite  Rewrite existing file if true
     */
    public function __construct($logFile, $bRewrite = false){
        $this->active = Registry::useStorage('CFG')->get('debug/log', TRUE);
        // Sanitize filename: remove all restricted characters and sequences
        $logFile = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $logFile);
        $logFile = preg_replace("([\.]{2,})", '', $logFile);
        $this->logFile = PATH_LOG . '/' . $logFile . '.log';
        if($this->active && $bRewrite && file_exists($this->logFile)){
            unlink($this->logFile);
        }
        self::$aLoggers[$logFile] = $this;
    }
    /**
     * Returns logger by name.
     *
     * @param string $name  Logger name
     * @return \AmiLabs\DevKit\Logger
     */
    public static function get($name){
        return isset(self::$aLoggers[$name]) ? self::$aLoggers[$name] : new Logger($name);
    }
    /**
     * Logs message to a file.
     *
     * @todo Add message types (error, warning, notice, etc)
     * @param string $message
     */
    public function log($message){
        $string = '';
        if($message !== self::DELIMITER){
            $timeString = '[' . date('Y-m-d H:i:s') . '] ';
            $string = $timeString . $message . "\r\n";
        }else{
            $string = "============================================================================\r\n";
        }
        if($this->active && $string){
            file_put_contents($this->logFile, $string, FILE_APPEND);
        }
    }
}