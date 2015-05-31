<?php

namespace AmiLabs\DevKit;

use AmiLabs\DevKit\Utils;

/**
 * Abstract Cache class.
 */
abstract class Cache {
    /**
     * Cache stored in files.
     */
    const FILE = 1;
    /**
     * Used caches
     *
     * @var array
     */
    protected static $aCaches = array();
    /**
     * Returns a named cache entry.
     *
     * @param string $name  Cache entry name
     * @param int $type     Cache type
     * @return \AmiLabs\DevKit\FileCache
     */
    public static function get($name, $type = Cache::FILE){
        if(!isset(self::$aCaches[$name])){
            switch($type){
                default:
                    self::$aCaches[$name] = new FileCache($name);
            }
        }
        return self::$aCaches[$name];
    }
}
/**
 * Cache driver interface.
 */
interface ICache {
    /**
     * Constructor.
     *
     * @param string $name  Cache entry name
     */
    public function __construct($name);
    /**
     * Check if cached information exists.
     */
    public function exists();
    /**
     * Loads cached data.
     */
    public function load();
    /**
     * Stores cached data.
     *
     * @param mixed $data
     */
    public function save($data);
    /**
     * Clears cached data.
     */
    public function clear();
}
/**
 * File cache driver.
 */
class FileCache implements ICache{
    /**
     * Cache filename
     *
     * @var string
     */
    protected $fileName;
    /**
     * Constructor.
     *
     * @param string $name  Cache entry name
     */
    public function __construct($name){
        $this->fileName = PATH_TMP . '/' . Utils::sanitizeFilename($name) . '_cache.tmp';
    }

    /**
     * Returns cache filename.
     *
     * @return string
     */
    public function getFilename(){
        return $this->fileName;
    }

    /**
     * Returns true if cache file exists and is readable.
     *
     * @return bool
     */
    public function exists(){
        return file_exists($this->fileName) && is_readable($this->fileName);
    }

    /**
     * Returns cache creation time.
     *
     * @return int
     */
    public function getTime(){
        return $this->exists() ? filemtime($this->fileName) : 0;
    }

    /**
     * Clear cache if it is older than specified amount of time in seconds.
     *
     * @param int $seconds  Cache lifetime in seconds
     */
    public function clearIfOlderThan($seconds){
        $deleted = FALSE;
        if((time() - $this->getTime()) > $seconds){
            $this->clear();
            $deleted = TRUE;
        }
        return $deleted;
    }

    /**
     * Loads cache file.
     *
     * @return mixed
     */
    public function load(){
        return unserialize(file_get_contents($this->fileName));
    }

    /**
     * Saves cached data.
     *
     * @param mixed $data
     */
    public function save($data){
        Utils::saveFile($this->fileName, serialize($data));
    }

    /**
     * Clears cached data.
     */
    public function clear(){
        if($this->exists()){
            unlink($this->fileName);
        }
    }
}